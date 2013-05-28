<?php

namespace suite6\LightCDN;

ignore_user_abort(true);
require_once "bootstrap.php";


// Running my test case
$_SERVER['HTTP_REFERRER'] = 'http://img3.cache.netease.com/www/logo/logo_png.png';
#$_SERVER['HTTP_REFERRER'] = 'http://www.google.com/images/srpr/logo4w.png';
#$_SERVER['HTTP_REFERRER'] = 'http://www.bing.com/partner/primedns.gif';
#$_SERVER['HTTP_REFERRER'] = 'http://a248.e.akamai.net/assets.github.com/images/modules/header/github-logotype.png';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.swf';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.gif';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.jpg';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.png';
#$_SERVER['HTTP_REFERRER'] = 'http://samplepdf.com/sample.pdf';
#$_SERVER['HTTP_REFERRER'] = 'http://g-ecx.images-amazon.com/images/G/01/gno/images/general/navAmazonLogoFooter._V169459313_.gif';


$request = new HTTPRequest();

// Create object
$assets = new LightCDNEngine($request);

// Get  assets
$assets->getAsset();

//Run cron delete files form data directory
//$assets->remove_assets();
?>