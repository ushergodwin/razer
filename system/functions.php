<?php
use League\BooBoo\BooBoo;
use System\HttpResponse\HttpResponse;
$root = $_SERVER['DOCUMENT_ROOT'];
if(empty(trim($root)))
{
    $root = @getcwd();
}
require_once $root . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname( __DIR__));
$dotenv->safeLoad();

//exception handling
$booboo = new BooBoo([new League\BooBoo\Formatter\HtmlFormatter()]);

$booboo->register(); // Registers the handlers


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
function url(string $url = '') {
    $base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base .= "://".$_SERVER['HTTP_HOST']."/";
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

/**
 * Return the app path
 *
 * @return string
 */
function APPPATH()
{
    return $_SERVER['DOCUMENT_ROOT'];
}

/**
 * Convert an array to an Object
 *
 * @param array $array
 * @return object
 */
function array_to_object(array $array) {
    return json_decode(json_encode($array), FALSE);
}


/**
 * Object to array
 *
 * @param mixed $data
 * @return array
 */
function object_to_array($data)
{
    if (is_array($data) || is_object($data))
    {
        $result = [];
        foreach ($data as $key => $value)
        {
            $result[$key] = (is_array($data) || is_object($data)) ? object_to_array($value) : $value;
        }
        return $result;
    }
    return $data;
}



/**
 * Http Bad request 403
 *
 * @param string $message Message to print on the screen
 * @return never
 */

/**
 * Get|set session kesys
 *
 * @param string|array $key The sesion key to get or an associative array of key and value to set in a session
 * @param string|callable $default The deafult value to return if the key is not found or a callback function
 * @return void|string|callback
 */
function session($key, $default =NULL) {
    if (is_array($key)) {
        foreach($key as $session_key => $value) {
            $_SESSION[$session_key] = $value;
        }
        return;
    }
    if (!isset($_SESSION[$key])) {
        if(!is_callable($default)) {
            return $default;
        }
        return call_user_func($default);
    }

    return $_SESSION[$key];
}

/**
 * Access app assets
 *
 * @param string $asset
 * @return string asset url
 */
function assets(string $asset){
    return url('assets/'.$asset);
}

function response()
{
    return new HttpResponse();
}


//CONSTANTS;
//csrf

$_SESSION['token'] = bin2hex(random_bytes(35));
$csrf = $_SESSION['token'];

define('csrf', "<input type='hidden' name='_token' value = '$csrf'>");
define('APPNAME', env('APP_NAME'));
define('APPPATH', $_SERVER['DOCUMENT_ROOT']);
