<?php
namespace Phaser\Models;

require_once APPPATH().'/vendor/autoload.php';
use Phaser\Database\Database;
use Phaser\Manager\PhpManager;

/**
 * Access to all BLUEFACES Database Model, File Mode and controller functions.
 * 
 * Do not use these models in the root directory.
 * 
 * Call the db property to access the database model
 * 
 * Call the FILES property to access FILES model for uploading multiple files
 * 
 * All your models must extend the BaseModel and should be under the namespace Models
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

    public function db_test() {
        return $this->db->getAll('fname, lname, password', 'staff');
    }

}