<?php
namespace Config;

class Database
{
    /**
     * @var $config
     * Database Configurations
     */
    public $config = 
    array(
        "hostname" => 'localhost',
        "username" => 'root',
        "portnumber" => '3306',
        "password" => '',
        "database" => 'scholar'
    );

    /**
     * @var $time_zone
     * Timezone to be used by the server
     */
    public $time_zone = 'Africa/Kampala';
}