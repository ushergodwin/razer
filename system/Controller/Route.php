<?php
namespace System\Controller;
use Closure;

class Route
{
    static $routes = [];
    static $is_group = false;
    static $prefix;

    private static function map_uri(string $uri, $callback) {
        self::$routes[$uri] = $callback;
    }

    private static function requestMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Parse Dynamic data to a uri
     *
     * @param string $uri The request URI
     * @param array|Clouser $callback The callback class and the mothod
     * @return void
     */
    public static function dynamic(string $uri, $callback) {
        if(self::requestMethod() == "get") {

            if (self::$is_group) {
                $uri = self::$prefix['prefix'] . $uri;
            }

            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }

            self::map_dynamic_uri($uri, $callback);
        }
    }

    private static function map_dynamic_uri(string $uri, $callback) {

        if (preg_match('/\w+(\/)$/', $uri) == 1) {
            $uri = $uri."(:any)";
        }
        else {
            $uri = $uri."/(:any)";
        }

        self::$routes[$uri] = $callback;
    }

    /**
     * Get a Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return void
     */
    static function get(string $uri, $callback) {
        if(self::requestMethod() == "get") {

            if (self::$is_group) {
                $uri = self::$prefix['prefix'] . $uri;
            }

            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }
            
            self::map_uri($uri, $callback);
        }
    }

        /**
     * Post request Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return void
     */
    static function post(string $uri, $callback) {
        if(self::requestMethod() == "post") {

            if (self::$is_group) {
                $uri = self::$prefix['prefix'] . $uri;
            }

            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }

            self::map_uri($uri, $callback);
        }
    }

    /**
     * Group URIs
     *
     * @param array $prefix
     * @param Closure $callback
     * @return void
     */
    public static function group(array $prefix, Closure $callback) {
        self::$is_group = true;
        self::$prefix = $prefix;
        call_user_func($callback);
        self::$is_group = false;
    }

    /**
     * Resource routes for a controller
     *
     * @param string $name
     * @param string $controller
     * @return void
     */
    public static function resource(string $name, string $controller) {
        self::get($name, [$controller, 'index']);
        self::get($name.'/create', [$controller, 'create']);
        self::dynamic($name.'/edit', [$controller, 'edit::$1']);

        if ((new self)->requestMethod() == 'post'){
            self::post($name.'/destroy', [$controller, 'destroy']);
            self::post($name.'/update', [$controller, 'update']);
            self::post($name.'/store', [$controller, 'store']);
        }

    }
}