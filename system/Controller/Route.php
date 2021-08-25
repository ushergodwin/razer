<?php
namespace System\Controller;

class Route
{
    static $routes = [];

    public static function To(string $uri, $controller) {
        self::map_uri($uri, $controller);
    }

    private static function map_uri(string $uri, string $controller) {
        self::$routes[$uri] = $controller;
    }


    public static function Dynamic(string $uri, $controller) {
        self::map_dynamic_uri($uri, $controller);
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
}