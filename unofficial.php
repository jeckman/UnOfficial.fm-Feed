<?php 
// these first ones are configured, not available in the official.fm feed
$source_user_id = '113174';  //user_id at official.fm whose tracks we want
$my_title = 'The Waiting Room'; // plain text
$my_description = 'We are a New Music Radio Show based/produced in Cardiff, Wales, UK and syndicated worldwide via HoundstoothRadio.com, ErrorFM.com/Music, RadioPhoenix.org, and ThisIsFakeDIY.com/Radio. Podcast available every Friday.'; // plain text
$my_link = 'http://twrhq.official.fm/'; 
$my_feed_url = 'http://johneckman.com/uo/feed.xml'; //url for the feed output
$itunes_subtitle = 'New Music Radio Show';
$itunes_author = 'The Waiting Room';
$itunes_owner_name = 'One Half of Drunk Country';
$itunes_owner_email = 'dc@twrhq.com';
$itunes_summary = 'We are a New Music Radio Show based/produced in Cardiff, Wales, UK and syndicated worldwide via HoundstoothRadio.com, ErrorFM.com/Music, RadioPhoenix.org, and ThisIsFakeDIY.com/Radio.  Podcast available every Friday.'; // these are all plain text
$itunes_image = 'http://cdn.official.fm/user_avatars/113/113174_large.jpg'; // url
$itunes_category = 'Music';


/*
 ======================================================================
 UnOfficial.fm Feed
 
 Take an Official.fm tracks feed and turn it into a usable RSS feed
 with proper enclosures.  

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
 ======================================================================
*/




/* nothing to configure below here */ 

$feed_url = 'http://official.fm/tracks.rss?user_id=' . $source_user_id; 

/* include feed fetching and parsing library */ 
include('parse.php');

$rss = new lastRSS; 
if ($rs = $rss->get($feed_url)){
	//print_r($rs); 
} else { die('Error: RSS file not found, dude.'); }

/* get the pages */ 
$my_pages = $rs[pages]; // how many pages of results

/* write out the outer shell, channel, globals */ 
$now = date("D, d M Y H:i:s T");
$output = "<?xml version=\"1.0\"?>
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
		<itunes:category text=\"$itunes_category\"/>
		<language>en-us</language>
		<lastBuildDate>$now</lastBuildDate>
		
		";

/* now get the info on each item in the feed
 *	todo: get the description, which is not in the rss feed
 *  but could be retrieved from official.fm at the link
 * location
 */
if (($my_pages) && ($my_pages > 1)) {	
	for ($i = 1; $i <= $my_pages; $i++) {
		$feed_url = $feed_url . '&page=' . $i; 
		if ($rs = $rss->get($feed_url)){
		//print_r($rs); 
		} else { die('Error: RSS file not found, dude.'); }
		foreach ($rs[items] as $item) {	
			$item_url = get_location($item[link] .'/download');
			$item_size = get_size($item_url);
			$output .= "<item>
			<itunes:author>$itunes_author</itunes:author>
			<itunes:subtitle>$item[description]</itunes:subtitle>
			<itunes:summary>$item[description]</itunes:summary>
			<title>$item[title]</title>
			<link>$item[link]</link>
			<description>$item[description]</description>
			<pubDate>$item[pubDate]</pubDate>
			<enclosure url=\"$item_url\" length=\"$item_size\" type=\"audio/mpeg\" />
			<guid>$item_url</guid>
		</item>
		";
		}
	}
} else {  // no pages, just get the items
	foreach ($rs[items] as $item) {	
		$item_url = get_location($item[link] .'/download');
		$item_size = get_size($item_url);
		$output .= "<item>
			<itunes:author>$itunes_author</itunes:author>
			<itunes:subtitle>$item[description]</itunes:subtitle>
			<itunes:summary>$item[description]</itunes:summary>
			<title>$item[title]</title>
			<link>$item[link]</link>
			<description>$item[description]</description>
			<pubDate>$item[pubDate]</pubDate>
			<enclosure url=\"$item_url\" length=\"$item_size\" type=\"audio/mpeg\" />
			<guid>$item_url</guid>
		</item>
		";
	}
}

/* seems like we're getting the closing footer too early */
sleep(15); 

/* and output the closing footer */
$output .= "
	<atom:link href=\"$my_feed_url\" rel=\"self\" type=\"application/rss+xml\" /> 
	</channel>
</rss>
";
header("Content-Type: application/rss+xml");
echo $output;

/* end of main loop */ 

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
?>