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

wget http://example.com/unofficial.php -O feed.xml 

Then configure podcatcher clients to point at the feed.xml

Set the cronjob to the desired frequency - every time it runs, it will
overwrite the feed.xml with new content. 




