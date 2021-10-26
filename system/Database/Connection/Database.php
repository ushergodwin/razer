<?php
namespace System\Database\Connection;

class Database extends Connection
{

    /**
     * Swicth to use a different database connection
     *
     * Convenient when working with multiple database in the app.
     * 
     * @param string $name
     * @return \System\Database\Connection\Database
     */
    public static function switchTo(string $name)
    {
        $db = new self;
        $db::$newdbConnection = $name;
        return $db;
    }
}