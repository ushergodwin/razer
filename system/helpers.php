<?php
use League\BooBoo\BooBoo;
use System\Http\CRSF\CRSF;
use System\Http\Redirect;
use System\Http\Request;
use System\Http\Response;
use System\Views\Template;

$root = $_SERVER['DOCUMENT_ROOT'];
if(empty(trim($root)))
{
    $root = @getcwd();
}
require_once $root . '/vendor/autoload.php';

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

if(!function_exists('url'))
{
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
}



if(!function_exists('APPPATH'))
{
    /**
     * Return the app path
     *
     * @return string
     */
    function APPPATH()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }
}



if(!function_exists('array_to_object'))
{
    /**
     * Convert an array to an Object
     *
     * @param array $array
     * @return object
     */
    function array_to_object(array $array) {
        return json_decode(json_encode($array), FALSE);
    }
}



if(!function_exists('object_to_array'))
{
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
}



if(!function_exists('session'))
{
    /**
     * Get|set session kesys
     *
     * @param string|array $key The sesion key to get or an associative array of key and value to set in a session
     * @param mixed $default The deafult value to return if the key is not found
     * @return bool|string
     */
    function session($key, $default =NULL) {
        if (is_array($key)) {
            foreach($key as $session_key => $value) {
                $_SESSION[$session_key] = $value;
            }
            return;
        }
        if (!isset($_SESSION[$key])) {
            return $default;
        }

        return $_SESSION[$key];
    }
}



if(!function_exists('assets'))
{
    /**
     * Access app assets
     *
     * @param string $asset
     * @return string asset url
     */
    function assets(string $asset){
        return url($asset);
    }
}


if(!function_exists('route'))
{   
    /**
     * Get uri using roote name
     *
     * @param string $name
     * @return string
     */
    function route(string $name)
    {
        if(!isset($_SESSION[$name]))
        {
            throw new Exception
            ("Error Processing Route. The route name {$name} is not registered.\n
            Call the name method on a route request to register its name",
            
            1);
            
        }
        return url(session($name));
    }
}


if(!function_exists('response'))
{
    /**
     * Http Response helper
     *
     * @return \System\Http\Response;
     */
    function response()
    {
        return new Response();
    }
}


if(!function_exists('request'))
{
    /**
     * Http Request helper
     *
     * @return \System\Http\Request
     */
    function request()
    {
        return new Request();
    }
}


if(!function_exists('render'))
{
    /**
     * Return an HttpResponse rendering a request file with optional data parsed 
     *
     * @param string $view
     * @param array $context
     * @return void HttpResponse
     */
    function render(string $view, array $context)
    {
        $view = str_replace('.', '/', $view);
        return Template::view($view, $context);
    }
}


if(!function_exists('redirect'))
{
        
    /**
     * Send an Http Redirect
     * @param int $status Response status
     *  
     *  200 - Success
     * 
     * 418 - Infor
     * 
     * 419 - Failure
     * 
     * 500 - Server error
     * @return \System\Http\Redirect
     */
    function redirect(string $url = '', int $status = 200) {
        $redirect = new Redirect();
        $redirect->status = $status;
        $redirect->url = $url;
        return $redirect;
    }
}


if(!function_exists('old'))
{
    /**
     * Get the previously submitted data after a request as a requet of calling redirect()->back()->withInput()
     *
     * @param string $key
     * @return string
     */
    function old(string $key = '')
    {
        $old = isset($_SESSION['responseData']) ? session('responseData') : [];
        $old_key = array_keys($old);
        if(!empty($old_key))
        {
            $old_len = count($old_key);
            $last_key = $old_key[$old_len - 1];
            if($key == $last_key)
            {
                unset($_SESSION['responseData']);
            }
        }
        return isset($old[$key]) ? $old[$key] : '';

    }
}

if(!function_exists('csrf_field'))
{
    function csrf_field()
    {
        $token_id = CRSF::crsfTokenId();
        $token = CRSF::crsfTokenValue();
        return "<input type='hidden' name='$token_id' value = '$token'>";
    }
}

//CONSTANTS;
define('APPNAME', env('APP_NAME'));
define('APPPATH', $_SERVER['DOCUMENT_ROOT']);
