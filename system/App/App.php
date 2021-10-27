<?php
namespace System\App;
session_start();
define("BASE_PATH", $_SERVER['DOCUMENT_ROOT']."/");
define("VERSION", '1.0.0');
include_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

strtolower(env("ERROR_REPORTING")) === "true" ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

use System\Routes\Route;
use System\Http\Request;


define("SYSTEM_PATH", $_SERVER['DOCUMENT_ROOT']."/system/");

define("APP_PATH", $_SERVER['DOCUMENT_ROOT']."/app/");

define("APP_NAME", env('APP_NAME'));

include_once(APP_PATH."/routes/routes.php");


class App 
{

    /**
     * Run the application
     *
     * @return void
     */
    public static function Run() {

        (new self)->__init__();
    }

    private function __init__() {
        $routes = Route::$routes;
        //re order routes such that dynamic ones come last
        foreach($routes as $key => $value)
        {
            if(preg_match('/\b(\w*any\w*)\b/', $key, $matches) === 1)
            {
                unset($routes[$key]);
                $routes[$key] = $value;
            }
        }
        $uri = $_SERVER['REQUEST_URI'];
        //Simple routes.
        if (isset($_GET))
            $uri = explode("?", $uri)[0];

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

        $this->map_uri_to_method($routes, $args, $args_array);
    }



    private function map_uri_to_method($routes, $args, $args_array)
    {
        foreach ($routes as $route => $val) {
            $is_args_supplied = false;
            $dynamic_route = explode("/", $route);
            if (in_array("(:any)", $dynamic_route)) {
                $is_args_supplied = true;
                //Dynamic array comes from route after removing the (:any) argument
                //We use it to determine the exact url by match the uri with the route class arguments
                //unset($dynamic_route[count($dynamic_route) - 1]);
            }
            if (! $is_args_supplied) {
                //Less build the function from the appropriate file
                $val_route = array();

                if ($route == $args) {
                    if (is_callable($val)) {
                        
                        call_user_func_array($val, [new Request]);
                        return;
                    }else {
                        $val_route = explode("::", $val);
                    }

                    include_once(APP_PATH."Controller/" . $val_route[0] . ".php");
                    $class_ucfirst = ucfirst($val_route[0]);
                    
                    $class = New $class_ucfirst;
                    if ($_SERVER['REQUEST_METHOD'] == "POST"){
                      call_user_func_array(array($class, $val_route[1]), [new Request]);
                        return;
                    }
                    call_user_func(array($class, $val_route[1]));
                    //Lets deal with function arguments;
                    return;
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
                        if (strcmp($dynamic_route_reversed[$i], "(:any)") == 0) {
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
                        $val_route = explode("::", $val); //We get the routing value and break it down to get the file name and class name
                        include_once(APP_PATH."Controller/" . $val_route[0] . ".php"); //We then import the class file
                        $class_ucfirst = ucfirst($val_route[0]);
                        
                        $class = New  $class_ucfirst;
                        call_user_func_array(array($class, $val_route[1]), $func_arguments);
                        return;
                    }
                } else
                    continue;
            }
        }

        $this->urlNotFound();
    }

    private function urlNotFound() {
        header("HTTP/1.0 404 Not Found");
        exit("<div align='center'><a href='/'><img src='".url('404.jpg')."'/> </div></a> ");
    }
}