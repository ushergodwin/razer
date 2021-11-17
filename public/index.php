<?php
/**
 * System requires PHP v7.4 or greater
 */
if (version_compare(PHP_VERSION, '7.4', 'lt'))
{
	die("Your PHP version must be 7.4 or higher to run Phaser. Current version: " . PHP_VERSION);
}

$path = str_replace('public', '', $_SERVER['DOCUMENT_ROOT']);
/**
 * Base Project Path
 * @var BASE_PATH string
 */
define("BASE_PATH", trim(substr($path, 0, strlen($path) - 1)));

/**
 * Let's utilize composer's autoload to load our classes so we don't have to worry
 * about loading them, cool to relax right
 */
require_once '../vendor/autoload.php';

use System\App\App;
/**
 * Let's boot and run the application
 */
App::Boot()->run();
