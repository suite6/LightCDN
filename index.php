<?php

namespace suite6\LightCDN;

ignore_user_abort(true);
require_once "bootstrap.php";

// Running my test case
//$_SERVER['HTTP_REFERRER'] = 'http://img3.cache.netease.com/www/logo/logo_png.png';

$request = new HTTPRequest();


// Create object
$assets = new LightCDNEngine($request);

// Get  assets
$assets->getAsset();

//Run cron delete files form data directory
//$assets->remove_assets();
?>