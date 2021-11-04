<?php
$minPHPVersion = '7.4';
if (version_compare(PHP_VERSION, $minPHPVersion, '<'))
{
	die("Your PHP version must be {$minPHPVersion} or higher to run Phaser. Current version: " . PHP_VERSION);
}
unset($minPHPVersion);
$base = $_SERVER['DOCUMENT_ROOT'];
$base = str_replace('public', '', $base);
define("BASE_PATH", $base."/");
require_once '../vendor/autoload.php';

use System\App\App;
App::Run();
