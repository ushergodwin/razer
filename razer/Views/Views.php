<?php
namespace Razer\Views;
use Jenssegers\Blade\Blade;
require BASE_PATH .'/vendor/autoload.php';
	
class Views {
	
	static function render($file, $data = []) {
		$views_settings = require_once BASE_PATH .'/config/view.php';
		
		$blade = new Blade(
			$views_settings['path'], 
			$views_settings['compiled']
		);
		echo $blade->make($file, $data)->render();
	}

}
