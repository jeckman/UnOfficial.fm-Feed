<?php 
/*
 ======================================================================
 UnOfficial.fm Feed
 
 Take an Official.fm tracks feed and turn it into a usable RSS feed
 with proper enclosures.  

 Configure the portion at the top of the script, then run
 on a cron job so it outputs to feed.xml
 
 Point podcast subscribers to feed.xml - set your cron job for the 
 update frequency desired. 
 
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


// these first ones are configured, not available in the official.fm feed
$source_user_id = '113174';  //user_id at official.fm whose tracks we want
$my_title = 'The Waiting Room'; 
$my_description = 'We are a New Music Radio Show based/produced in Cardiff, Wales, UK & syndicated worldwide via HoundstoothRadio.com, ErrorFM.com/Music, RadioPhoenix.org, & ThisIsFakeDIY.com/Radio. 
Podcast available every Friday.';
$my_link = 'http://twrhq.official.fm/'; 


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
	<rss version=\"2.0\">
	<channel>
		<title><![CDATA[$my_title]]></title>
		<link>$my_link</link>
		<description><![CDATA[$my_description]]></description>
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
			<title>$item[title]</title>
			<link>$item[link]</link>
			<description>$item[description]</description>
			<pubDate>$item[pubDate]</pubDate>
			<enclosure url=\"$item_url\" length=\"$item_size\" type=\"audio/mpeg\" />
		</item>
		";
		}
	}
} else {  // no pages, just get the items
	foreach ($rs[items] as $item) {	
		$item_url = get_location($item[link] .'/download');
		$item_size = get_size($item_url);
		$output .= "<item>
			<title>$item[title]</title>
			<link>$item[link]</link>
			<description>$item[description]</description>
			<pubDate>$item[pubDate]</pubDate>
			<enclosure url=\"$item_url\" length=\"$item_size\" type=\"audio/mpeg\" />
		</item>
		";
	}
}

/* and output the closing footer */
$output .= "
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