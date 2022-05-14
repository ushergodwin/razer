<?php
namespace Razer\Database\Connection;

class Database extends Connection
{

    /**
     * Swicth to use a different database connection
     *
     * Convenient when working with multiple database in the app.
     * 
     * @param string $name
     * @return \Razer\Database\Connection\Database
     */
    public static function switchTo(string $name)
    {
        $db = new self;
        $db::$newdbConnection = $name;
        return $db;
    }
}