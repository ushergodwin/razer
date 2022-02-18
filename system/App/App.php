<?php
namespace System\App;
session_start();

define("VERSION", '1.0.1');

use System\Routes\Route;
use System\Http\Request\Request;


define("SYSTEM_PATH", BASE_PATH."/system/");

define("APP_PATH", BASE_PATH."/app/");

define("APP_NAME", env('APP_NAME'));

include_once(APP_PATH."/routes/web.php");


/**
 * Application
 */
class App 
{

    protected $request_uri;
    /**
     * Boot the application and handle the request
     *
     * @return \System\App\App
     */
    public static function handle($request) {
        $app = new self;
        $app->request_uri = $request;
        return $app;
    }

    /**
     * Send the user request to internal app and return the response
     *
     * @return Response
     */
    public function send()
    {
        return $this->run();
    }

    private function run() {
        $routes = Route::$routes;
        //re order routes such that dynamic ones come last
        foreach($routes as $key => $value)
        {
            if(preg_match('/\b(\w*args\w*)\b/', $key, $matches) === 1)
            {
                unset($routes[$key]);
                $routes[$key] = $value;
            }
        }

        //Simple routes.
        if (isset($_GET))
        {
            $uri = explode("?", $this->request_uri)[0];
        }
        $uri_exploded = explode("/", $uri);
        $uri_exploded_len = count($uri_exploded);
        //The function is expected to have arguments
        $args = [];
        for ($i = 1; $i < $uri_exploded_len; $i++) {
            if (strlen($uri_exploded[$i]) < 1)
                //For urls that end in slashes, we truncate the last space and match the uri to the perfect route
                //Eg, ./some/ and ./some are the same. There we treat both as the same
                continue;
            array_push($args, $uri_exploded[$i]);
        }

        $args_array = $args;

        $args = implode("/", $args);

        $args = implode("/", explode("?", $args));

        return $this->map_uri_to_method($routes, $args, $args_array);
    }



    private function map_uri_to_method($routes, $args, $args_array)
    {
        foreach ($routes as $route => $val) {
            $dynamic_route = explode("/", $route);
            $is_args_supplied = (in_array("(:args)", $dynamic_route)) ? true : false;
            if (! $is_args_supplied) {
                //Less build the function from the appropriate file
                $val_route = array();

                if ($route == $args) {
                    if (is_object($val) && is_callable($val)) {
                        
                        return call_user_func_array($val, [new Request]);
                    }else {
                        $val_route = explode("::", $val);
                    }

                    $class_ucfirst = ucfirst($val_route[0]);
                    
                    $class = new $class_ucfirst;
                    if ($_SERVER['REQUEST_METHOD'] == "POST"){
                        return call_user_func_array(array($class, $val_route[1]), [new Request]);
                    }
                    return call_user_func(array($class, $val_route[1]));
                } else
                    continue;
            } else {

                //Reverse array order to get arguments first
                $dynamic_route_reversed = array_reverse($dynamic_route, false);
                $args_array_reversed = array_reverse($args_array, false);
                $argc_len = count($args_array_reversed);
                $dynamic_len = count($dynamic_route_reversed);
                $func_arguments = array(); //Arguments supplied from the url.
                //In this case, we want to begin the array from the end to the first. We do not reserve array positions in this case
                if($dynamic_len === $argc_len) { 
                    for ($i = 0; $i < $argc_len ; $i++)
                        if (strcmp($dynamic_route_reversed[$i], "(:args)") == 0) {
                            unset($dynamic_route_reversed[$i]);
                            if (isset($args_array_reversed[$i])) { //Lets store the arguments provided by the uri
                                $func_arguments[$i] = $args_array_reversed[$i];
                                unset($args_array_reversed[$i]);
                            } else
                                continue;
                        }
                    $func_arguments = array_reverse($func_arguments);
                    //After reducing both arrays to the exact number of arguments, we convert the arrays back to strings and compare if they match
                    $args_array_reversed = implode("/", $args_array_reversed);
                    $dynamic_route_reversed = implode("/", $dynamic_route_reversed);
                    if (strcmp($args_array_reversed, $dynamic_route_reversed) == 0) {
                        //When the strings match, we then route the request to the called class and method
  
                        if(isset($_POST['_method']) && $_POST['_method'] == "DELETE")
                        {
                            $val = implode('::', $val);   
                        }
                        $val_route = explode("::", $val); //We get the routing value and break it down to get the file name and class name
                        
                        $class_ucfirst = ucfirst($val_route[0]);
                        
                        $class = new  $class_ucfirst;
                        return call_user_func_array(array($class, $val_route[1]), $func_arguments);
                    }
                } else
                    continue;
            }
        }

        $this->urlNotFound();
    }

    private function urlNotFound() {
        header("HTTP/1.0 404 Not Found");
        exit("<div align='center'><a href='/'><img src='".asset('404.jpg')."'/> </div></a> ");
    }
}