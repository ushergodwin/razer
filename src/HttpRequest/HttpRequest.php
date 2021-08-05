<?php
namespace Phaser\HttpRequest;

class HttpRequest {


    public function __construct()
    {
        
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

    /**
     * Get values parsed in the HTTP POST request
     * @param string $string
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function post($string = '', string $default='') {

        $string = trim($string);

        if (isset($_POST[$string]))
            return filter_var(strip_tags($_POST[$string]), FILTER_SANITIZE_STRING);
        
        return $default;
    }

    /**
     * Get values parsed in an HTTP GET request
     * @param string $string
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function get($string = '', string $default='') {

        $string = trim($string);
        if (isset($_POST[$string]))
        return filter_var(strip_tags($_GET[$string]), FILTER_SANITIZE_STRING);
    
        return $default;
    }

    /**
     * Get the values parsed in the HTTP GET or POST methods
     * @param string $string
     * @param string $default The default value to return in case the key is not found
     * @return string Data
     */
    public function any(string $string = "", string $default='') {

        if (isset($_REQUEST[$string]))
            return filter_var(strip_tags($_REQUEST[$string]), FILTER_SANITIZE_STRING);
        return $default;
    }

    /**
     * Get the Request data as an associative array
     *
     * @return array Data
     */
    public function requestAsArray() {
        $data = array();
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[] = [$key => $value];
            }
            return $data;
        }
        foreach ($_GET as $key => $value) {
            $data[] = [$key => $value];
        }
        return $data;
    }

    /**
     * Return Json request as a PHP array
     *
     * @param string $json
     * @return void
     */
    public function JsonRequest(string $json) {
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
}
