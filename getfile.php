<?php
// Track using Google Analytics  
echo 'this is the redirect file';
$ga_uid='UA-1816964-3'; // Enter your unique GA Urchin ID (utmac)
$ga_domain='johneckman.com'; // Enter your domain name/host name (utmhn)
$ga_randNum=rand(1000000000,9999999999);// Creates a random request number (utmn)
$ga_cookie=rand(10000000,99999999);// Creates a random cookie number (cookie)
$ga_rand=rand(1000000000,2147483647); // Creates a random number below 2147483647 (random)
$ga_today=time(); // Current Timestamp
$ga_referrer=$_SERVER['HTTP_REFERER']; // Referrer url
$ga_userVar=''; // Enter any variable data you want to pass to GA or leave blank
$ga_hitPage='/uo2/getfile.php'; // Enter the page address you want to track
$gaURL='http://www.google-analytics.com/__utm.gif?utmwv=1&utmn='.$ga_randNum.'&utmsr=-&utmsc=-&utmul=-&utmje=0&utmfl=-&utmdt=-&utmhn='.$ga_domain.'&utmr='.$ga_referrer.'&utmp='.$ga_hitPage.'&utmac='.$ga_uid.'&utmcc=__utma%3D'.$ga_cookie.'.'.$ga_rand.'.'.$ga_today.'.'.$ga_today.'.'.$ga_today.'.2%3B%2B__utmb%3D'.$ga_cookie.'%3B%2B__utmc%3D'.$ga_cookie.'%3B%2B__utmz%3D'.$ga_cookie.'.'.$ga_today.'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.$ga_cookie.'.'.$ga_userVar.'%3B';
$my_id = $_REQUEST['trackid'];
header("Location: http://new.official.fm/tracks/". $my_id  ."/file"); /* Redirect browser */
?>