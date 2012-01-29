Project: UnOfficial.FM Feed Maker
Author: John Eckman
URL: https://github.com/jeckman/UnOfficial.fm-Feed
License: GPL v2 or Later

Takes an official.fm userid, and uses the tracks.rss feed provided by 
official.fm to create an iTunes-compatible RSS feed, including enclosures. 

Configure the section at the top. You have to manually provide many of the
iTunes-specific data elements because Official.fm doesn't provide those items. 

Once the script is configured, run it in a web browser:
     example.com/unofficial.php
(Depending on where you've installed it, obviously)

That should output RSS directly to the screen. 

Once that's working well, configure a cron job to output the rss to a file:

For example, my crontab has this line:

* 3 * * * wget http://example.com/dir/official.php -O ~/example.com/dir/feed.xml

(Your mileage will vary, as you'll need to set the right path to your php
file and the right path to the directory where it can write). 

Then configure podcatcher clients to point at the feed.xml

Set the cronjob to the desired frequency - every time it runs, it will
overwrite the feed.xml with new content. 




