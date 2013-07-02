<?php

namespace suite6\LightCDN;

ignore_user_abort(true);
require_once "library/bootstrap.php";


// Running my test case
#$_SERVER['HTTP_REFERRER'] = 'http://img3.cache.netease.com/www/logo/logo_png.png';
#$_SERVER['HTTP_REFERRER'] = 'http://www.bing.com/partner/primedns.gif';
#$_SERVER['HTTP_REFERRER'] = 'http://a248.e.akamai.net/assets.github.com/images/modules/header/github-logotype.png';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.swf';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.gif';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.jpg';
#$_SERVER['HTTP_REFERRER'] = 'http://www.pragmasoftwares.com/wp-content/uploads/2013/05/test.png';
#$_SERVER['HTTP_REFERRER'] = 'http://samplepdf.com/sample.pdf';
#$_SERVER['HTTP_REFERRER'] = 'http://localhost/test/sample.pdf';
#$_SERVER['HTTP_REFERRER'] = 'http://localhost/test/lightcdn.docx';
#$_SERVER['HTTP_REFERRER'] = 'http://g-ecx.images-amazon.com/images/G/01/gno/images/general/navAmazonLogoFooter._V169459313_.gif';
#$_SERVER['HTTP_REFERRER'] = 'http://www.google.com/images/srpr/logo4w.png';
#$_SERVER['HTTP_REFERRER'] = 'http://acmmm10.unifi.it/wp-content/uploads/2009/09/google_logo_home.jpg';




# Testing
/*
	$test = new LightCDNEngineTest();
	$test->setup();
	$test->testGetAsset();
	exit;
*/


// Read HTTPRequest
$request = new HTTPRequest();

// Create object of LightCDNClientRequest
$clientRequest = new LightCDNClientRequest($request);

// Create object of LightCDNEngine
$assets = new LightCDNEngine($request);

// Get  assets
$assets->getAsset();

//Run cron delete files form data directory
//$assets->remove_assets();
?>