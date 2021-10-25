<?php
namespace System\Http;

use stdClass;
use System\Http\CRSF\CRSF;
use System\Http\Validate\Validate;

class Request {

    /**
     * Get parameter value parsed in a uri
     *
     * @var obejct|\stdClass
     */
    public $params;


    /**
     * Get value sent via a post request
     *
     * @var object|\stdClass
     */
    public $body;


    protected $html_char;
    protected $html_decode;

    public function __construct()
    {
        if(isset($_REQUEST))
        {
            foreach($_REQUEST as $key => $v)
            {
                $this->__set($key, $v);
            }
        }

        if($this->isGet())
        {
            $this->params = $this->params();
        }

        if($this->isPost())
        {
            $this->isRequestCrsfProtected();
            $this->body = $this->body();
        }
    }

    /**
     * Get parameter value parsed in a uri using the params property
     *
     * @return obejct|stdClass
     */
    protected function params()
    {
        return !empty($_GET) ? array_to_object($_GET) : new stdClass();
    }


    /**
     * Get value sent in a post request using the body property
     *
     * @return object|stdClass
     */
    protected function body()
    {
        return !empty($_POST) ? array_to_object($_POST) : new stdClass();
    }

      /**
     * Request Method
     * @return string
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get Request Method
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'get';
    }

    /**
     * POST Request Method
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'post';
    }


    protected function isRequestCrsfProtected()
    {
        switch(CRSF::isValid())
        {
            case 1: 
                return true;
            case 2:
                //Send a 405 Method Not Allowed header.
                header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
                //Halt the script's execution.
                exit;

            case 3:
                //Send a 405 Method Not Allowed header.
                header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
                //Halt the script's execution.
                exit;
        }
    }

    /**
     * Get values parsed in the HTTP POST request
     * @param string $string
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function post(string $key, string $default='') {
        $this->isRequestCrsfProtected();
        $key = trim($key);

        if (isset($_POST[$key]))
            return filter_var($this->xss_clean($_POST[$key]), FILTER_SANITIZE_STRING);
        
        return $default;
    }

    /**
     * Get values parsed in an HTTP GET request
     * @param string $key
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function get(string $key, string $default='') {

        $string = trim($key);
        if (isset($_REQUEST[$string]))
            return filter_var($this->xss_clean($_GET[$string]), FILTER_SANITIZE_STRING);
    
        return $default;
    }

    /**
     * Get the values parsed in the HTTP GET or POST methods
     * @param string $key
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function any(string $key, string $default='') {

        if (isset($_REQUEST[$key])){
            if($this->isPost())
            {
                $this->isRequestCrsfProtected();
            }
            return filter_var($this->xss_clean($_REQUEST[$key]), FILTER_SANITIZE_STRING);
        }
        return $default;
    }

    /**
     * Get the Request data as an associative array
     *
     * @return array Data
     */
    public function all() {
        if ($this->isPost()) {
            $this->isRequestCrsfProtected();
          return $_POST;
        }
        return $_GET;
    }

    /**
     * Return Json request as a PHP array
     *
     * @param string $json
     * @return void
     */
    public function json(string $json) {
        return json_decode($json);
    }

    /**
     * Return a cross site free value
     *
     * @param string  $string
     * @return string the stripped string
     */
    function xss_clean(string $string) {
        return strip_tags($string);
    }

    /**
     * Check if a key exists in the request
     * @param string $key
     * @return bool
     */
    public function has(string $key) {

        return (isset($_REQUEST[$key]));
    }

    /**
     * Check if a key does not exists in the request
     * @param string $key
     * @return bool
     */
    public function missing(string $key) {

        return (!isset($_REQUEST[$key]));
    }


    /**
     * Get the request data except the supplied keys
     *
     * @param array $keys keys to ignore
     * @return array
     */
    public function except(array $keys)
    {
        $origin_post = $_REQUEST;
        $remaining = array();

        $keys_len = count($keys);
        for($i = 0; $i < $keys_len; $i++)
        {
            if(isset($_REQUEST[$keys[$i]]))
            {
                unset($_REQUEST[$keys[$i]]);
            }
        }

       $remaining = $_REQUEST;
       $_REQUEST = $origin_post;
       return $remaining;
    }

    /**
     * Get Query Request String
     *
     * @return mixed
     */
    public function queryString()
    {
        return isset($_SERVER['QUERY_STRING']) ?? $_SERVER['QUERY_STRING'];
    }


    /**
     * Ruest URI
     *
     * @return string
     */
    public function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }


    /**
     * Http Referer
     *
     * @return string
     */
    public function httpReferer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }


    /**
     * Validate User Input
     *
     * @param array $rules
     * @return \System\Http\Validate\Validate
     */
    public function validate(array $rules)
    {
        return new Validate($rules);
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }


    /**
     * Indicate that you are reciving data with HTML characters
     *
     * @param string $key The key to get its value
     * 
     * The $key varaibale can also be an encoded HTML string that would like to decode.
     * Call decoded in this case.
     * @param string $default Default value to return when the key is not found. defaults to ''
     * @return \System\Http\Request;
     */
    public function html(string $key, $default = '')
    {
        if(!isset($_POST[$key]))
        {
            $this->html_char = $default;
        }
        else {
            $this->isRequestCrsfProtected();
            $this->html_char = $_POST[$key];
        }
        $this->html_decode = $key;
        return $this;
    }

    /**
     * Get plain HTML without encoding
     *
     * @return string
     */
    public function plain()
    {
        return $this->html_char;
    }


    /**
     * Encode HTML characters
     *
     * @return string text with HTML Entities
     */
    public function encoded()
    {
        return htmlentities($this->html_char);
    }


    /**
     * Get decoded HTML for redering
     *
     * @return string Text with HTML tags
     */
    public function decoded()
    {
        return html_entity_decode($this->html_decode);
    }
    
}
