<?php

use System\Models\BaseModel;
use League\BooBoo\BooBoo;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname( __DIR__));
$dotenv->safeLoad();

//exception handling
$booboo = new BooBoo([new League\BooBoo\Formatter\HtmlTableFormatter()]);

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
function baseUrl(string $url = '') {
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
function ToObject(array $array) {
    return json_decode(json_encode($array), FALSE);
}

/**
 * Get the value parsed using the GET HTTP Request
 * @return string
 */
function httpGet(string $key, string $default = '') {
    if (!isset($_GET[$key])) {
        return $default;
    }
    return strip_tags($_GET[$key]);
}

/**
 * Get the value parsed using the POST HTTP Request
 * @return string
 */
function httpPost(string $key, string $default = '') {
    if (isset($_POST[$key]) and !isset($_POST['phaser_csrf'])) {
        throw new Exception("The request can not be proccessed because it's missing the CRSF token. use the Phaser crsf constant to generate the token for POST requests");
    }

    if ($_POST['phaser_crsf'] !== $_SESSION['crsf']) {
        throw new Exception("The request can not be proccessed because it has an invalid CRSF token.");
    }
    if (!isset($_POST[$key])) {
        return $default;
    }
    return strip_tags($_POST[$key]);
}

/**
 * Get the value parsed using the GET/POST HTTP Request
 * @return string
 */
function httpAny(string $key, string $default = '') {
    if (!isset($_REQUEST[$key])) {
        return $default;
    }
    return strip_tags($_REQUEST[$key]);
}

/**
 * Http Bad request 403
 *
 * @param string $message Message to print on the screen
 * @return never
 */
function HttpBadRequest(string $message) {
    header("HTTP/1.0 403 Bad Request");
    exit($message);
}

/**
 * Database Operations
 *
 * @param mixed $table Table name to transact
 * @param array $data Table data in case of insert or update operations
 * @return object
 */
function Table($table, array $data = []) {
    return BaseModel::table($table, $data);
}

/**
 * Get|set session kesys
 *
 * @param string|array $key The sesion key to get or an associative array of key and value to set in a session
 * @param string|callable $default The deafult value to return if the key is not found or a callback function
 * @return void|string|callback
 */
function session(string|array $key, callable|string $default ='') {
    if (is_array($key)) {
        foreach($key as $session_key => $value) {
            $_SESSION[$session_key] = $value;
        }
        return;
    }
    if (!isset($_SESS[$key])) {
        if(!is_callable($default)) {
            return $default;
        }
        return call_user_func($default);
    }

    return $_SESSION[$key];
}
//CONSTANTS;
//csrf
$csrf = sha1(uniqid('phaser_csrf_'));
$_SESSION['csrf'] = $csrf;
define('csrf', "<input type='hidden' name='phaser_csrf' value = '$csrf'>");
