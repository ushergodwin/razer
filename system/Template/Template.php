<?php
namespace System\Template;
use Exception;
include_once $_SERVER['DOCUMENT_ROOT'].'/system/config/template.config.php';

class Template {

	static $blocks = array();
	static $cache_path = 'cache/';
	static $cache_enabled;
	static $templates_path;
	static $template_extension;


	static function templatesPath(string $templates_path) {
		self::$templates_path = $templates_path;
	}

	static function templateExtension(string $extension = '.html')
	{
		self::$template_extension = $extension;
	}

	static function setTemplateCaching(bool $cache = FALSE) {
		self::$cache_enabled = $cache;
	}

	static function cachePath(string $cache_path) {
		self::$cache_path = $cache_path;
	}
	static function view($file, $data = array()) {
		$cached_file = self::cache($file);
	    extract($data, EXTR_SKIP);
	   	require $cached_file;
	}

	static function cache($file) {
		if (!file_exists(self::$cache_path)) {
		  	mkdir(self::$cache_path, 0744);
		}
	    $cached_file = self::$cache_path . str_replace(array('/', '.'), array('_', ''), $file . '.php');
	    if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
			$code = self::includeFiles($file);
			$code = self::compileCode($code);
	        file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
	    }
		return $cached_file;
	}

	static function clearCache() {
		foreach(glob(self::$cache_path . '*') as $file) {
			unlink($file);
		}
	}

	static function compileCode($code) {
		$code = self::compileBlock($code);
		$code = self::compileYield($code);
		$code = self::compileEscapedEchos($code);
		$code = self::compileEchos($code);
		$code = self::compilePrintR($code);
		$code = self::compilePHP($code);
		return $code;
	}

	static function file_contents($path) {
		$str = @file_get_contents($path);
		if ($str === FALSE) {
			throw new Exception("Phaser Template Exception: Cannot access '$path'");
		} else {
			return $str;
		}
	}

	static function includeFiles($file) {
		$file .= self::$template_extension;
		$file = self::$templates_path.$file;
		$code = "";
		try {
			$code = self::file_contents($file);
		}catch(Exception $e) {
			echo $e->getMessage();
		}
		preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			$code = str_replace($value[0], self::includeFiles($value[2]), $code);
		}
		$code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
		return $code;
	}

	static function compilePHP($code) {
		return preg_replace('~\{%\s*(.+?)\s*\%}~is', '<?php $1 ?>', $code);
	}

	static function compileEchos($code) {
		return preg_replace_callback('~\{{\s*(.+?)\s*\}}~is', function ($matches) {
			return '<?php echo ' . str_replace(['.','{{', '}}'], ['->', '', ''], $matches[0]) . ' ?>';
		}, $code);
	}

	static function compilePrintR($code) {
		return preg_replace_callback('~\{{\s*(@print_r+?)\s*\}}~is', function ($matches) {
			return '<?php ' . str_replace('@print_r', 'print_r', $matches[0]) . ' ?>';
		}, $code);
	}

	static function compileEscapedEchos($code) {
		return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
	}

	static function compileBlock($code) {
		preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
		foreach ($matches as $value) {
			if (!array_key_exists($value[1], self::$blocks)) self::$blocks[$value[1]] = '';
			if (strpos($value[2], '@parent') === false) {
				self::$blocks[$value[1]] = $value[2];
			} else {
				self::$blocks[$value[1]] = str_replace('@parent', self::$blocks[$value[1]], $value[2]);
			}
			$code = str_replace($value[0], '', $code);
		}
		return $code;
	}

	static function compileYield($code) {
		foreach(self::$blocks as $block => $value) {
			$code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
		}
		$code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
		return $code;
	}

}