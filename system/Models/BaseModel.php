<?php
namespace System\Models;

require_once APPPATH().'/vendor/autoload.php';
use System\Database\Database;
use System\Manager\PhpManager;

/**
 *@category  Database Access
 * @package   Database
 * @author Tumuhimbise Godwin
 * @copyright Copyright (c) 2020-2021
 * @version   2.0
 * 
 * @method object table()
 * 
 * Access to all BLUEFACES Database Model, File Mode and controller functions.
 * 
 * 
 * Call the FILES property to access FILES model for uploading multiple files
 * 
 * All your models must extend the BaseModel and should be under the namespace App\Models
 * 
 */
class BaseModel extends PhpManager
{
    protected $db;

    public static $PAGE;

    public function __construct()
    {
        $this->db = new Database();
        parent::__construct();
    }

    public static function welcome() {
        return (new self)->response->HttpResponse("You have successfully installed the package"); 
    }
    
    /**
     * Get a Database Instance for Database operations
     * 
     * @param mixed $tableName The name of the table
     * @param array $tabledata Optioanl data, (used in inserting and updating)
     * @return object Database Instance
     */

    public static function table($tableName, array $tableData = []) {
        $local_table = $tableName;

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $tableName);

        if (!class_exists ($tableName))
            eval ("
            use System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new Database ();

        $instance->__init__($local_table, '*', $tableData);
        
        return $instance;
    }
}
