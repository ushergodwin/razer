<?php
namespace System\Views;
use Jenssegers\Blade\Blade;
include_once BASE_PATH .'/system/config/template.config.php';
require BASE_PATH .'/vendor/autoload.php';

class Template {

	static $cache_path = BASE_PATH . '/system/Views/cache';
	static $cache_enabled;
	static $templates_path;
	static $template_extension;


	static function templatesPath(string $templates_path) {
		self::$templates_path = $templates_path;
	}

	static function templateExtension(string $extension = '.blade.php')
	{
		self::$template_extension = $extension;
	}
	
	static function view($file, $data = array()) {
		$blade = new Blade(self::$templates_path, self::$cache_path);
		echo $blade->make($file, $data)->render();
	}

}
