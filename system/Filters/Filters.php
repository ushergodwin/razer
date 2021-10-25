<?php
namespace System\Filters;

class Filters
{
       /**
     * Filter a string
     * @param $string
     * @return mixed
     */
    public function string($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    /**
     * Filter an email and remove unwanted characters
     * @param $string
     * @return mixed
     */
    public function email($string) {
        return filter_var($string, FILTER_SANITIZE_EMAIL);
    }

    /**
     * validate an email
     * @param $string
     * @return bool
     */
    public function validateEmail($string) {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if the url is valid
     * @param $url
     * @return bool
     */
    public function validateUrl($url) {
        return (filter_var($url, FILTER_VALIDATE_URL));
    }

    /**
     * Compare 2 strings
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public function compare($value1,  $value2) {
        return $value1 === $value2 ? true : false;
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
    public function isEmpty(array $variable){
        $feed = false;
        $variable = array_values($variable);
        $len = count($variable);
        for ($i = 0; $i < $len; $i++) {
            if (empty(trim($variable[$i]))) {
                $feed = true;
                break;
            }
        }
        return $feed;
    }
}