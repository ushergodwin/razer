<?php
use System\Template\Template;

class BasicRoute {

    static $file;
    static $context;

    public static function index() {
        $file = explode('::', self::$file);
        return Template::view($file[1], self::$context);
    }
}