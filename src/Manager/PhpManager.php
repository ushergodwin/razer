<?php
namespace Phaser\Manager;
use FFI\Exception;
use Phaser\Cookies\Cookies;
use Phaser\File\File;
use Phaser\Filters\Filters;
use Phaser\HttpRequest\HttpRequest;
use Phaser\HttpResponse\HttpResponse;
use Phaser\Mail\Mail;
use Phaser\Password\Password;
use Phaser\Session\Session;
use Phaser\Uuid\Uuid;
use stdClass;

/**
 * Class PhpManager
 * @category  PHP Inbuilt functions
 * @package   Controller
 * @author Tumuhimbise Godwin
 *@copyright Copyright (c) 2020-2021
 * @version   2.0
 */


/**
 * Class PhpManager
 * Get access to commonly used inbuilt php functions to ease up your development cycle
 */

class PhpManager
{
    public  $input; //Input class

    public  $server; //Server class

    public $cookie; //Cookie class

    public $session; //Session class

    public $password; //Password class

    public $response; //HttpResponse

    public $request; //HttpRequest;


    public $model = [];

    public $uuid; //Uuid class

    public $file; //File class

    public $mail; //Mail Class

    public $filter; //filter class

    public function __construct() {

        $this->request = new HttpRequest();
        $this->cookie = new Cookies();
        //Session class
        $this->session = new Session();
        $this->model = new stdClass();
        $this->password = new Password();
        //Notification class
        $this->response = new HttpResponse();
        //Unique_key class
        $this->uuid = new Uuid();
        //File class
        $this->file = new File();

        //Mail class
        $this->mail = new Mail();

        $this->filter = new Filters();
    }

    function remove_none_utf_char($string) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // non breaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $string);
    }

    function remove_special_chars($string) {
        $string = strip_tags($string);
        $string = preg_replace('/[^A-Za-z0-9. -]/', ' ', $string);
        // Replace sequences of spaces with hyphen
        $string = preg_replace('/  */', '-', $string);
        return $string;
    }


    function remove_numbers_from_string($string) {
        return preg_replace('/\d+/u', '', $string);
    }

    function replace_multiple_spaces($string) {
        return preg_replace('!\s+!', ' ', $string);
    }

}
