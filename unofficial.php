<?php 
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

$source_user_id = '113174';  //user_id at official.fm whose tracks we want

$feed_url = 'http://official.fm/tracks.rss?user_id=' . $source_user_id; 

/* include feed fetching and parsing library */ 
include('parse.php');

$rss = new lastRSS; 
if ($rs = $rss->get($feed_url)){
	//print_r($rs); 
} else { die('Error: RSS file not found, dude.'); }

foreach ($rs[items] as $item) {	echo 'Count is ' . $count++ . '<br>';
	echo 'Title is ' . $item[title] . '<br>';
	echo 'Link is ' . $item[link] . '<br>';
	$item[link] = $item[link] . '/download';
	echo 'Link is ' . $item[link] . '<br>';
	$item[link] = get_location($item[link]); 
	echo 'Link is ' . $item[link] . '<br>';
	echo '<br>';
	get_location($item[link]);
	
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
		echo 'Header is ' . $header . '<br>'; 
		if(strpos($header, 'Location: ') === 0) {
			return substr($header,10); 
		}
	 }
	return '';
}

?>