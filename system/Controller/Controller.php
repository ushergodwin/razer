<?php
namespace System\Controller;
use System\Cookies\Cookies;
use System\File\File;
use System\Filters\Filters;
use System\Mail\Mail;
use System\Password\Password;
use System\Session\Session;
use System\Uuid\Uuid;
use System\Server\Server;
/**
 * Application Controller
 * 
 */

class Controller
{
    /**
     * Server operations
     *
     * @return \System\Server\Server
     */
    public function server()
    {
        return new Server();

    }
    /**
     * Cookie global variable
     *
     * @return \System\Cookies\Cookies
     */
    public function cookies()
    {
        return new Cookies();
    }


    /**
     * Interuct with the Sesion global array
     *
     * @return \System\Session\Session
     */
    public function session() {
        return new Session();
    }

    /**
     * Manage passwords
     *
     * @return \System\Password\Password
     */
    public function password() {
        return new Password();
    }


    /**
     * Send Mail
     *
     * @return \System\Mail\Mail
     */
    public function mail()
    {
        return new Mail();
    }

    /**
     * Access all file operations
     *
     * @return \System\File\File;
     */
    public function files()
    {
        return new File();
    }


    /**
     * Unique indetifiers
     *
     * @return \System\Uuid\Uuid
     */
    public function uuid()
    {
        return new Uuid();
    }


    /**
     * Input filter and conversions
     *
     * @return \System\Filters\Filters
     */
    public function filter()
    {
        return new Filters();
    }
}
