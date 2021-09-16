<?php
namespace System\Controller;

use BasicRoute;

include 'BasicRoute.php';

class Route
{
    static $routes = [];

    private static function map_uri(string $uri, string|callable $controller) {
        self::$routes[$uri] = $controller;
    }

    /**
     * Parse Dynamic data to a uri
     *
     * @param string $uri The request URI
     * @param string $callback The callback class and the mothod
     * @return void
     */
    public static function dynamic(string $uri, $callback) {
        self::map_dynamic_uri($uri, $callback);
    }

    private static function map_dynamic_uri(string $uri, string $controller) {

        if (preg_match('/\w+(\/)$/', $uri) == 1) {
            $uri = $uri."(:any)";
        }
        else {
            $uri = $uri."/(:any)";
        }

        self::$routes[$uri] = $controller;
    }
    
    /**
     * Render a file without a controller
     *
     * @param string $uri The request URI
     * @param string $file The file to render
     * @param array $context Array to data to display in the template. defaults to []
     * @return void
     */
    static function view(string $uri, string $file, array $context = []) {
        BasicRoute::$file = "::".$file;
        BasicRoute::$context = $context;
        self::map_uri($uri, $file);
    }

    /**
     * Get a Route map
     *
     * @param string $uri The request URI
     * @param callable|string $callback Callback class and method || Callback function
     * @return void
     */
    static function get(string $uri, callable|string $callback) {
        self::map_uri($uri, $callback);
    }
}