<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$root_dir = dirname(__DIR__);


require_once "vendor/autoload.php";
require_once "vendor/spyc/spyc.php";
require_once 'SplClassLoader.php';
//require 'autoLoader.php';
require_once 'vendor/suite6/Tackler/TacklerAutoload.php';

// include common.ini.php 
require_once "common.inc.php";

# Path to save Cache
$dir_path = $root_dir . '/data';

# Log all routes in log.txt file under $dir_path : keep it false on production
$debug_mode = true;

//initialize config object
$tackler_config = new suite6\Tackler\TacklerConfiguration();

$tackler_config->set_default_403_handler('403.html');
$tackler_config->set_default_404_handler('404.html');

$doctrineClassLoader = new SplClassLoader('Entities', $root_dir);
$doctrineClassLoader->register();

$doctrineClassLoaderRepo = new SplClassLoader('Repository', $root_dir);
$doctrineClassLoaderRepo->register();

$settings = array();

//Configuration file path and name 
$config_file  = 'config/config.yaml';
$default_file = 'config/defaults.yaml';

if (file_exists($config_file)) {
    $settings = spyc_load_file($config_file);
    //check if defaults.yaml exist then overwrite
    if (file_exists($default_file)) {
        $default_settings = array();
        $default_settings = spyc_load_file($default_file);
        //if config.yaml is not empty
        if (count($settings) > 0) {
            $settings = array_deep_merge($settings, $default_settings);
        } else {
            $settings = $default_settings;
        }
    }
} else {
    $settings = array();
}

$dbsettings = array();
$dbsettings = $settings['database'];

$paths     = array(
    __DIR__ . "Entities"
);
$isDevMode = true;
$config    = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

//// the connection configuration
//// Connection Setup
$dbParams      = array(
    'driver' => $dbsettings['driver'],
    'user' => $dbsettings['user'],
    'password' => $dbsettings['password'],
    'dbname' => $dbsettings['dbname']
);
$entityManager = EntityManager::create($dbParams, $config);
$connection    = $entityManager->getConnection();


/*
$sm = $connection->getSchemaManager();
//List of database
$databases = $sm->listDatabases();

// If DB not exist create it
if (!in_array($dbsettings['dbname'], $databases))
    $sm->createDatabase($dbsettings['dbname']);

$dbParams = array('driver' => $dbsettings['driver'], 'user' => $dbsettings['user'], 'password' => $dbsettings['password'], 'dbname' => $dbsettings['dbname']);
$entityManager = EntityManager::create($dbParams, $config);
$connection = $entityManager->getConnection();


$table = 'assets_info';
// check table if not exist create_table() call
$check_table = checkTable($table);
if ($check_table == false)
    create_table($table);
*/