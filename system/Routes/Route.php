<?php
namespace System\Routes;
use Closure;
use Exception;

class Route
{
    static $routes = [];
    static $is_group = false;
    static $prefix;
    protected $named_routes = [];

    private static function map_uri(string $uri, $callback) {
        self::$routes[$uri] = $callback;
    }

    private static function requestMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Make a Get Request Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return \System\Routes\Route
     */
    static function get(string $uri, $callback) {
        $route = new self;
        if(strlen($uri) === 1)
        {
            $uri = '';
        }
        if($route::requestMethod() == "get") {

            if ($route::$is_group) {
                $uri = $route::$prefix['prefix'] . $uri;
            }

            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }
            
            $uri_array = explode('/', $uri);
            foreach($uri_array as $param)
            {
                if(preg_match('/{(.*?)}/', $param) === 1)
                {
                    $uri = preg_replace('/{(.*?)}/', "(:args)", $uri);
                }
            }
            $route::map_uri($uri, $callback);
        }
        return $route;
    }

    /**
     * Make a Post Request Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return \System\Routes\Route
     */
    static function post(string $uri, $callback) {
        $route = new self;
        if($route::requestMethod() == "post") {

            if ($route::$is_group) {
                $uri = $route::$prefix['prefix'] . $uri;
            }

            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }

            $route::map_uri($uri, $callback);
        }
        return $route;
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
     * Resource routes for a resource controller
     *
     * @param string $name
     * @param string $controller
     * @param callable $appendURI An associative array of request method and the callback method to be added to the resource route.
     * 
     * The call back takes in 2 arguments, $uri and the $class name
     * @return \System\Routes\Route
     */
    public static function resource(string $name, string $controller, $appendURI = NULL) {
        $resource = new self;
        $resource::get($name, [$controller, 'index'])->name($name."."."index");
        $resource::get($name.'/create', [$controller, 'create'])->name($name."."."create");

        if ((new self)->requestMethod() == 'post'){
            $resource::put($name, [$controller, 'update'])->name($name."."."index");
            $resource::post($name.'/store', [$controller, 'store'])->name($name."."."store");
        }
        $resource::get($name.'/{id}/edit', [$controller, 'edit'])->name($name.".$1"."edit");
        $resource::get($name.'/{id}', [$controller, 'how'])->name($name.".$1"."show");
        $resource::delete($name.'/{id}', [$controller, 'destroy'])->name($name."."."destroy");
        if(is_callable($appendURI))
        {
            call_user_func_array($appendURI, [$name, $controller]);
        }
        return $resource; 
    }

    
    /**
     * Exclude methods from automatic resource url mapping
     *
     * @param array $methods
     * @return void
     */
    public function except(array $methods)
    {
        if(empty($methods))
        {
            throw new Exception
            ("The except method expected an array with at least 
            1 method to exclude the resource route mapping, " 
            . count($methods) . " given", 
            1);
            
        }
        $methods_len = count($methods);

        foreach(self::$routes as $key => $value)
        {
            $method = explode('::', $value)[1];
            for($i = 0; $i < $methods_len; $i++)
            {
                if($method === $methods[$i])
                {
                    unset(self::$routes[$key]); 
                }
            }
        }
    }

    
    /**
     * Register a convenient route name.
     *
     * Can be later used in the route() method
     * @param string $name
     * @return void
     */
    public function name(string $name)
    {

        $uri = str_replace('.', '/', $name);
        if(strpos($name, 'index') !== false)
        {
            $uri = explode('.', $name)[0];
        }
        session([$name => $uri]);
    }

        /**
     * Make a PUT Request Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return \System\Routes\Route
     */
    static function put(string $uri, $callback) {
        $route = new self;
        if($route::requestMethod() == "post") {
            
            if(isset($_POST['_method']) and strtoupper($_POST['_method']) == "PUT"
             || strtoupper($_POST['_method']) == "DELETE")
            {
                
                if (is_array($callback)) {
                    $class = $callback[0];
                    $method = $callback[1];
                    $class = explode('::', $class)[0];
                    $callback = $class."::".$method;
                }
                $route::map_uri($uri, $callback);  
            }
        }
        return $route;
    }


    /**
     * Make a DELETE Request Route map
     *
     * @param string $uri The request URI
     * @param callable|array $callback Callback class and method || Callback function
     * @return \System\Routes\Route
     */
    static function delete(string $uri, $callback) {
        $route = new self;

        if(isset($_REQUEST['_method']) and strtoupper($_REQUEST['_method']) == "DELETE")
        {
            if(preg_match('/{(.*?)}/', $uri) === 1)
            {
                    $uri = preg_replace('/{(.*?)}/', "(:args)", $uri);
            }
            
            
            if (is_array($callback)) {
                $class = $callback[0];
                $method = $callback[1];
                $class = explode('::', $class)[0];
                $callback = $class."::".$method;
            }
            
            $route::map_uri($uri, $callback);
                
        }
        return $route;
    }

    protected function appendURI(array $methods, $controller, $name, Route $resource)
    {
        foreach ($methods as $key => $method) {
            # code...
            $key = strtolower($key);
            if($key == 'get')
            {
                $resource::get($name, [$controller, $method])->name($name.".".$method);   
            }
        }
    }
}