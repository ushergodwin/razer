<?php
namespace System\Session;

/**
 * Class Session
 * All session global variable properties
 */
class Session
{

    /**
     * @param string $url
     * The url whether to redirect
     * @return void
     */
    public function redirect($url) {
        header("location:".$url);
        exit();
    }
    /**
     * Starts session
     * @return bool
     */
    public function start() {
        return session_start();
    }


    /**
     * @param string $url The location where to redirect a user after ending the session
     * Destroys all the session data registered on session set
     * @return void
     */
    public function end($url = '') {
        $url = empty($url) ? url() : $url;
        session_unset();
        session_destroy();
        $this->redirect($url);
    }


    /**
     * @param array $session An array of session key and value to register
     * @return void successful registration of the session
     */
    public function set(array $session) {
        foreach ($session as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }


    /**
     * @param string $session_name The name of the registered session
     * @param string|callable $default  The default value|function to return|call
     * @return string A string of session data
     */
    public function get(string $session_name, $default = '') {
        if (empty(trim($_SESSION[$session_name]))) {
            if (is_callable($default)){
                return call_user_func($default);
            }
            return $default;
        }
        return $_SESSION[$session_name];
    }
    

    /**
     * Checks if the key exists in session
     *
     * @param string $key The searched key
     * @param callable $callback Optional function/class method to execute
     * @return bool True if the key exists in the session, False otherwise
     */
    public function contains(string $key, callable $callback = NULL) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, [$key]);
        }
        return isset($_SESSION[$key]);
    }


    /**
     * Unset the session variable | Variables
     *
     * @param string|array $session_name The name of the session to unset
     * Or an array of session variables to unset
     * @return void
     */
    public function unset($session_name) {

        if (is_array($session_name)) {
            $array_len = count($session_name);
            for ($i = 0; $i < $array_len; $i++) {
                unset ($_SESSION[$session_name[$i]]);
            }
        }else {
            unset ($_SESSION[$session_name]);
        }
    }
    

    /**
     * Extract the value of a key from the session and then delete the key.
     *
     * @param string $key The  key to get its value
     * @param callable $callback Optional function/class method to execute
     * @return string|callback The value of the key or functional callback
     */
    public function put(string $key, callable $callback = NULL) {
        $value =  isset($_SESSION[$key]) ? $_SESSION[$key] : '';
        unset($_SESSION[$key]);
        if (is_callable($callback)) {
            return call_user_func_array($callback, [$value]);
        }

        return $value;
    }

    
    /**
     * Get all set variables in a session
     *
     * @return object
     */
    public function all()
    {
        return (object)$_SESSION;
    }

}
