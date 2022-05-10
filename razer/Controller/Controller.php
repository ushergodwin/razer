<?php
namespace Razer\Controller;
use Razer\Cookies\Cookies;
use Razer\Filters\Filters;
use Razer\Mail\Mail;
use Razer\Password\Password;
use Razer\Session\Session;
use Razer\Uuid\Uuid;
use Razer\Server\Server;
use Razer\Storage\File;

/**
 * Application Controller
 * 
 */

class Controller
{
    /**
     * Server operations
     *
     * @return \Razer\Server\Server
     */
    public function server()
    {
        return new Server();

    }
    /**
     * Cookie global variable
     *
     * @return \Razer\Cookies\Cookies
     */
    public function cookies()
    {
        return new Cookies();
    }


    /**
     * Interuct with the Sesion global array
     *
     * @return \Razer\Session\Session
     */
    public function session() {
        return new Session();
    }

    /**
     * Manage passwords
     *
     * @return \Razer\Password\Password
     */
    public function password() {
        return new Password();
    }


    /**
     * Send Mail
     *
     * @return \Razer\Mail\Mail
     */
    public function mail()
    {
        return new Mail();
    }

    /**
     * Access the uploaded file and store it on the filesystem
     * @param string $key
     * @param mixed $default
     * @return \Razer\Storage\File;
     */
    public function file($key, $default = null)
    {
        return new File($key, $default);
    }


    /**
     * Unique indetifiers
     *
     * @return \Razer\Uuid\Uuid
     */
    public function uuid()
    {
        return new Uuid();
    }


    /**
     * Input filter and conversions
     *
     * @return \Razer\Filters\Filters
     */
    public function filter()
    {
        return new Filters();
    }
}
