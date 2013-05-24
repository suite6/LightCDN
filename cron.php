<?php

namespace suite6\LightCDN;

ignore_user_abort(true);
require_once "bootstrap.php";
$assets = new LightCDNEngine();
//Run cron for delete fime 
// Forn cron pass two argumants divider and  remainder e.g php cron.ph 5,2
$assets->clean_up();
?>