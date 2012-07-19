<?php 
/*
 ======================================================================
 UnOfficial.fm Feed
 
 Take an Official.fm tracks json file and turn it into a usable RSS feed
 with proper enclosures- iTUNES version, which gets the lofi.mp3 version
 of the feed only.  

 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 =====================================================================w=
*/

// CONFIGURATION SECTION

/* To find the "source_user_id", load the official.fm page of the 
 * account you are interested in, view source of that page, and look for the 
 * .json file - for example: http://new.official.fm/feed/playlists/6CaZ.json
 * Enter just the unique part, for example: 6CaZ
 */
$source_user_id = '';  //user_id at official.fm whose tracks we want
$my_title = 'Feed Title'; // plain text
$my_description = 'Feed Description'; // plain text
$my_link = 'http://example.com/'; // full URL to show homepage 
$my_install_url = 'http://example.com/dir/'; // url where script is installed

/* 
 * URL where your cron job will save the feed output - this is used for the 
 * atom self-reference in the feed and should match where the feed will be 
 */ 
$my_feed_url = 'http://example.com/dir/itunes_feed.xml'; 
$itunes_subtitle = 'Feed Subtitle';
$itunes_author = 'Feed Author';
$itunes_owner_name = 'Feed Owner';
$itunes_owner_email = 'owner@example.com';
$itunes_summary = 'Description'; // these are all plain text
$itunes_image = 'http://example.com/image.jpg'; // url
$itunes_category = 'Music';

/* nothing to configure below here */ 

$json_url = 'http://official.fm/feed/playlists/' . $source_user_id .'.json'; 
	//echo 'Json url was ' . $json_url .'<br>';
if ($rs = curlGet($json_url)){
	//print_r($rs); 
} else { die('Error: JSON file not found, dude.'); }

$my_json_o = json_decode($rs);

if (!$my_json_o->track_ids) {
	die('Error: JSON file did not have tracks'); 
}

/* write out the outer shell, channel, globals */ 
$now = date("D, d M Y H:i:s T");
$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<rss version=\"2.0\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\"
		 xmlns:atom=\"http://www.w3.org/2005/Atom\">	
	<channel>
		<title>$my_title</title>
		<link>$my_link</link>
		<itunes:subtitle>$itunes_subtitle</itunes:subtitle>
		<itunes:author>$itunes_author</itunes:author>
		<itunes:summary>$itunes_summary</itunes:summary>
		<description>$my_description</description>
		<itunes:owner>
			<itunes:name>$itunes_owner_name</itunes:name>
			<itunes:email>$itunes_owner_email</itunes:email>
		</itunes:owner>
		<itunes:image href=\"$itunes_image\" />
		<image>
			<url>$itunes_image</url>
			<link>$my_link</link>
			<description>$my_title</description>
			<title>$my_title</title>
		</image>
		<itunes:category text=\"$itunes_category\"/>
		<language>en-us</language>
		<lastBuildDate>$now</lastBuildDate>
		<itunes:explicit>no</itunes:explicit>
		<atom:link href=\"$my_feed_url\" rel=\"self\" type=\"application/rss+xml\" /> 

		";
		
		
		
/* now get the info on each item in the feed
 *	todo: get the description, which is not in the rss feed
 *  but could be retrieved from official.fm at the link
 * location
 */ 
foreach ($my_json_o->tracks as $track) {	
	$item_url = $track->mp3_url;
	$full_item_url = get_location($track->permalink . '/file'); 
	$large_photo = $track->picture->urls->large; 
	$item_size = get_size($full_item_url);
	$item_description = htmlspecialchars(get_description($track->permalink),ENT_QUOTES,'UTF-8');
	$item_title = htmlspecialchars($track->title,ENT_QUOTES,'UTF-8');
	$item_subtitle = htmlspecialchars($track->social_message,ENT_QUOTES,'UTF-8');
	$output .= "<item>
			<itunes:author>$itunes_author</itunes:author>
			<itunes:subtitle>$item_subtitle</itunes:subtitle>
			<itunes:summary>$item_description</itunes:summary>
			<title>$item_title</title>
			<link>$track->permalink</link>
			<description>$item_description</description>
			<itunes:image href=\"$large_photo\" />
			<enclosure url=\"$full_item_url\" length=\"$item_size\" type=\"audio/mpeg\" />
			<guid isPermaLink=\"true\">$track->permalink</guid>
		</item>";
}

/* seems like we're getting the closing footer too early */
sleep(15); 

/* and output the closing footer */
$output .= "
	</channel>
</rss>
";
header("Content-Type: application/rss+xml");
echo $output;

/* end of main loop */ 

/*
 * function to get via cUrl 
 * From lastRSS 0.9.1 by Vojtech Semecky, webmaster @ webdot . cz
 * See      http://lastrss.webdot.cz/
 */
 
function curlGet($URL) {
    $ch = curl_init();
    $timeout = 3;
    curl_setopt( $ch , CURLOPT_URL , $URL );
    curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
    curl_setopt( $ch , CURLOPT_CONNECTTIMEOUT , $timeout );
    $tmp = curl_exec( $ch );
    curl_close( $ch );
    return $tmp;
}  

/* 
 * function to use cUrl to get the headers of the file 
 */ 
function get_location($url) {
	$my_ch = curl_init();
	curl_setopt($my_ch, CURLOPT_URL,$url);
	curl_setopt($my_ch, CURLOPT_HEADER,         true);
	curl_setopt($my_ch, CURLOPT_NOBODY,         true);
	curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
	$r = curl_exec($my_ch);
	 foreach(explode("\n", $r) as $header) {
		if(strpos($header, 'Location: ') === 0) {
			return trim(substr($header,10)); 
		}
	 }
	return '';
}

function get_size($url) {
	$my_ch = curl_init();
	curl_setopt($my_ch, CURLOPT_URL,$url);
	curl_setopt($my_ch, CURLOPT_HEADER,         true);
	curl_setopt($my_ch, CURLOPT_NOBODY,         true);
	curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
	$r = curl_exec($my_ch);
	 foreach(explode("\n", $r) as $header) {
		if(strpos($header, 'Content-Length:') === 0) {
			return trim(substr($header,16)); 
		}
	 }
	return '';
}

function get_description($url) {
	$fullpage = curlGet($url);
	$dom = new DOMDocument();
	@$dom->loadHTML($fullpage);
	$xpath = new DOMXPath($dom); 
	$tags = $xpath->query('//div[@class="description"]');
	foreach ($tags as $tag) {
		$my_description .= (trim($tag->nodeValue));
	}	
	
	return utf8_decode($my_description);
}


?>