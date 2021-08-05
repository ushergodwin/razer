<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname( __DIR__));
$dotenv->safeLoad();

if(!function_exists('env')) {
    /**
     * Get the environment settings
     */
	function env($key) {
		return $_ENV[$key];
	}

}

if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        call_user_func_array('dump', $args);
        die();
    }
}

if (!function_exists('d')) {
    function d()
    {
        $args = func_get_args();
        call_user_func_array('dump', $args);
    }
}

/**
* @static base_url
* @return string The a full main url
* eg http://bluefaces.tech/
*/
function baseUrl(string $url = '') {
    $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base .= "://".$_SERVER['HTTP_HOST']."/";
    !empty(trim($url)) ? $base .= $url : $base;
    return $base;
}
    /**
     *
     * @return string The current folder with a full url
     * eg http://bluefaces.tech/sample
     */
function siteUrl(string $url = '') {
    $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base .= "://".$_SERVER['HTTP_HOST'];
    $base .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
    !empty(trim($url)) ? $base .= $url : $base;
    return $base;
}

/**
 * Redirect to a certain location on the server
 *
 * @param string $url
 * @return void
 */
function redirect(string $url) {
    header("location:".$url);
    exit();
}

function PageTitle()
{
    $file = str_replace(["-", "_", ".php"], " ", basename($_SERVER["REQUEST_URI"]));

    return strtolower($file) == "index" ? env("APP_NAME") . " | HOME " : env("APP_NAME") . " | " . strtoupper( $file);
}

/**
 * Return the app path
 *
 * @return string
 */
function APPPATH()
{
    return $_SERVER['DOCUMENT_ROOT'];
}
