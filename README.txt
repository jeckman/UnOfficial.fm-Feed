Project: UnOfficial.FM Feed Maker
Author: John Eckman
URL: https://github.com/jeckman/UnOfficial.fm-Feed
License: GPL v2 or Later

Takes an official.fm userid, and uses the JSON file provided by 
official.fm to create an iTunes-compatible RSS feed, including enclosures. 

To find the "source_user_id" as above, load the official.fm page of the 
account you are interested in, view source of that page, and look for the 
.json file - for example: http://new.official.fm/feed/projects/6CaZ.json

This .json file provides access to the tracks for this user as a JSON feed
(JavaScript Object Notation) which unofficial.php knows how to read

Configure the section at the top. You have to manually provide many of the
iTunes-specific data elements because Official.fm doesn't provide those items. 

Two scripts are provided:
  unofficial.php <- outputs a feed usable by most pod-catcher clients
  unofficial_itunes.php <- outputs a feed usable by iTunes

iTunes requires that enclosure urls end in .mp3 so the file has to be 
redirected through getfile.php 	

That should output RSS directly to the screen. 

Once that's working well, configure a cron job to output the rss to a file:

For example, my crontab has this line:

* 3 * * * curl -s http://example.com/dir/unofficial.php > ~/example.com/dir/feed.xml
* 3 * * * curl -s http://example.com/dir/unofficial_itunes.php > ~/example.com/dir/itunes-feed.xml

(Your mileage will vary, as you'll need to set the right path to your php
file and the right path to the directory where it can write). 

Then configure podcatcher clients to point at the feed.xml

Set the cronjob to the desired frequency - every time it runs, it will
overwrite the feed.xml with new content. 




