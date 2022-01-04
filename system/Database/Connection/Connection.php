<?php
namespace System\Database\Connection;
require_once APP_PATH.'Config/database.php';

use Exception;
use PDO;
use PDOException;
use System\Database\Logs\DatabaseLogs;

$config = (object) $config;

define("HOST", $config->DB_HOST);

define("USERNAME", $config->DB_USER);

define("PASSWORD", $config->DB_PASSWORD);

define("DB", $config->DB_NAME);

define("TIMEZONE", env("TIMEZONE"));

class Connection
{
    protected $database;
    protected $db;
    protected static $newdbConnection;
    use DatabaseLogs;
    /**
     * Instantiate the Database Model
     * @param string $database The database to use in the connection establishment. Default DB is the one set in the environment configurations (in .env file).
     * @param string $time_zone The time zone to be used by the server. default is Africa/Kampala
     * @return \PDO
     */
    public function __construct(string $database = DB, string $time_zone = TIMEZONE) {
        date_default_timezone_set($time_zone);
        try {

            if (empty(trim($this->database))) {
                $this->database = $database;
            }
            $this->db = $this->connect();
            return $this->db;
        } catch (Exception $exception) {
            $this->logError($exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine());
        }
    }

    protected function connect() 
    {
        $dsn = "mysql:host=".HOST.";dbname=".$this->database;
        try {
            $conn = new PDO($dsn, USERNAME, PASSWORD, array(PDO::MYSQL_ATTR_FOUND_ROWS));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }catch (PDOException $PDOException) {
            echo $PDOException->getMessage();
        }
    }


    /**
     * Get the database name for the current connection
     *
     * @return string Database name
     */
    public static function connectedDatabase()
    {
        $db = new self;
        return $db->database;
    }

    public function connectedDB()
    {
        return $this->database;
    }

    public function __destruct()
    {
        $this->db = null;
    }
}