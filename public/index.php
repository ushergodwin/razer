<?php
/**
 * Let's utilize composer's autoload to load our classes so we don't have to worry
 * about loading them, cool to relax right
 */
require_once __DIR__ .'/../vendor/autoload.php';

/**
 * System requires PHP v7.4 or greater
 */
if (version_compare(PHP_VERSION, '7.4', 'lt'))
{
	die("Your PHP version must be 7.4 or higher to run Phaser. Current version: " . PHP_VERSION);
}


/**
 * System only runs on a virtual host in development
 */
if(strpos($_SERVER['HTTP_HOST'], "localhost") !== false || filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP))
{
	exit(
		"
		<h1 style='text-align: center; color: red; margin: 10px;'>Oops, <em>" . env("APP_NAME") ." </em>Requires a VIRTUAL HOST.</h1>
		<h2 style='text-align: center; color: orange; margin: 10px;'>Can not be accessed on " . $_SERVER['HTTP_HOST'] ."</h2> 
		<h3 style='text-align: center; color: green'>Please create a virtaul host for this app and access it using the 
		vhost created! <br/> OR <br/> Head to the root directory of the app and run <code>php manage serve </code>
		 to run the PHASER development server</h4>
		"
	);
} 

use Razer\App\App;
use Razer\Http\Request\Request;

/**
 * Display the maintenance notification if the system is under maintenance
 */
if(strtolower(env("APP_ENV")) == 'maintenance')
{
	define('APP_NAME', env('APP_NAME'));
	require_once '../raser/Maintenance/maintenace.php';
	return false;
}


/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP Request. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/
App::handle(Request::capture())->send();
