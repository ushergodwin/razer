<?php
namespace System\Cookies;

/**
 * Class Cookies
 * 
 * All cookie global variable properties
 */
class Cookies  
{

    /**
     * Cookie object
     *
     * @return object
     */
    public function get() {
        return (object) $_COOKIE;
    }


    public function set($cookie_name, $cookie_data) {
        return setcookie($cookie_name, $cookie_data, time() + (86400 * 30 * 3), "/");
    }



    public function read($cookie_name) {
            if (isset($_COOKIE[$cookie_name]))
                return $_COOKIE[$cookie_name];
        return false;

    }

    public function destroy($cookie = false) {
        if ($cookie)
            setcookie( $cookie, "", time()- 60, "/","", 0);
        else {
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
        }
    }
}