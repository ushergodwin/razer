<?php
$minPHPVersion = '7.4';
if (version_compare(PHP_VERSION, $minPHPVersion, '<'))
{
	die("Your PHP version must be {$minPHPVersion} or higher to run Phaser. Current version: " . PHP_VERSION);
}
unset($minPHPVersion);

require $_SERVER['DOCUMENT_ROOT'] . '/system/App/App.php';

use System\App\App;

App::Run();
