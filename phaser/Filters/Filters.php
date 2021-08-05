<?php
namespace PHASER\Filters;

class Filters
{

    public function __construct()
    {
        
    }

       /**
     * Filter a string
     * @param $string
     * @return mixed
     */
    public function filter_string($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    /**
     * Filter an email and remove unwanted characters
     * @param $string
     * @return mixed
     */
    public function filter_email($string) {
        return filter_var($string, FILTER_SANITIZE_EMAIL);
    }

    /**
     * validate an email
     * @param $string
     * @return bool
     */
    public function validate_email($string) {
        $bool = false;
        if (filter_var($string, FILTER_VALIDATE_EMAIL)):
            $bool = true;
        endif;
        return $bool;
    }

    /**
     * Check if the url is valid
     * @param $url
     * @return bool
     */
    public function validate_url($url) {
        $bool = false;
        if(filter_var($url, FILTER_VALIDATE_URL)):
            $bool = true;
        endif;
        return $bool;
    }

    /**
     * Compare 2 strings
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public function compare($value1,  $value2) {
        $bool = false;
        if($value1 == $value2){
            $bool = true;
        }
        return $bool;
    }

    /**
     * Check if the value in the variable is a number
     * @param $value
     * @return bool
     */
    public function isNaN($value) {
        return is_numeric($value);
    }

    /** Convert to number
     * @param $var
     * @return int
     */
    public function toNumber($var) {
        return (int)$var;
    }

    /**
     * Convert an array to object
     * @param $var
     * @return object
     */
    public function toObject($var) {
        return (object)$var;
    }

    /**
     * Convert an integer to string
     * @param $var
     * @return string
     */

    public function toString($var) {
        return strval($var);
    }

    /**
     * @param array $variable An array of variables to check
     * @return bool false if all parsed variables are empty and True otherwise if not empty
     */
    public function isNotEmpty(array $variable){
        $feed = true;
        $len = count($variable);
        for ($i = 0; $i < $len; $i++) {
            if (empty(trim($variable[$i]))) {
                $feed = false;
                break;
            }
        }
        return $feed;
    }
}