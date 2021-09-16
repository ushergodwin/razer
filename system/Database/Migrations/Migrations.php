<?php
namespace System\Database\Migrations;
//date_default_timezone_set(env("TIMEZONE"));
require_once @getcwd() .'/app/Config/migrations.config.php';

$db_config = (object) $config;

use PDO;
use PDOException;


define('SERVER_NAME', $db_config->SERVER_NAME);

define('USER_NAME', $db_config->USER_NAME);

define('PASSWORD', $db_config->PASSWORD);

define('DATABASE_NAME', $db_config->DB_NAME);

define('PORT', (int)$db_config->PORT);
define('MIGRATIONS_DIR', $db_config->MIGRATIONS_DIR);

/**
 * PHASER MIGRATIONS MANAGER
 */
class Migrations
{   
    private static $db;

    private static $config;

    private static $dir;

    private static $default_db;

    public static $version = '1.0';

    private static $is_table;

    private static $server_version;

    public function __construct()
    {

    }

    public function __destruct()
    {
        self::$db = null;
    }

    private static function connect() {
        $mysql_host = SERVER_NAME;
        $mysql_database = DATABASE_NAME;
        $mysql_user = USER_NAME;
        $mysql_password = PASSWORD;
        try {
            $conn = new PDO("mysql:host=$mysql_host;", $mysql_user, $mysql_password);
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$server_version = $conn->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $conn;
    }

    public static function __init__(string $path) {
        self::$dir = $path .MIGRATIONS_DIR;
        if (!file_exists(self::$dir)) {
            echo "please create migrations path at ".self::$dir . " and try again. \n Exiting..";
            exit;
        }
    }

    public static function config(array $config, bool $default_db = true, bool $is_table = false) {
        self::$config = (object) $config;
        self::$default_db = $default_db;
        self::$is_table = $is_table;
    }

    /**
     * Run migrations for a single file
     *
     * @return void
     */
    public static function RunSingle()
    {
        $file = self::$config->migration;

        $migration_file = self::$dir;
        
        $scanned_files = scandir(self::$dir);
        if (empty($scanned_files)) {
            echo "The migrations file is not found. \n\nPlease make sure that the file exists under database/migrations/";
            return;
        }
        $file_count = count($scanned_files);
      
        for ($f = 0; $f < $file_count; $f++) {

            if (preg_match('/^.*\.(sql|json|csv|xml)$/i', $scanned_files[$f]) === 1) {
                if (preg_match("/{$file}/i",$scanned_files[$f]) === 1) {
                    $migration_file .= $scanned_files[$f];
                    break;
                }
            }
        }

        if (!file_exists($migration_file) or $migration_file == self::$dir) {
            echo "The migrations file is not found. \n\n Please make sure that the file exists under database/migrations/";
            return;
        }

        if (self::checkMigrations($migration_file)) {
            echo "Migration has already been run\r\n";
            return;
        }

        $query = file_get_contents($migration_file);

        self::$db = self::connect();

        try {
            echo "Running migrations...";

            $stmt = self::$db->prepare($query);
            $stmt->execute();

            self::$db = null;

            self::$db = self::connect();
            $query2 = "INSERT INTO migrations (migration_name) VALUES (?)";
            $stmt2 = self::$db->prepare($query2);
            $stmt2->bindParam(1, $file);
            $stmt2->execute();

            echo "\r\n Migrated $file";
        } catch (PDOException $e) {
            echo "Migration failed ( ".$e->getMessage() .")";
        }
        echo "\r\n Migration complete.";
    }

    /**
     * Run all application's migrations
     *
     * @return void
     */
    public static function RunAll() {

        self::$db = self::connect();

        $migration_files = array();

        $scanned_files = scandir(self::$dir);
        if (empty($scanned_files)) {
            echo "The migrations file is not found. \n\n Please make sure that the file exists under database/migrations/";
            return;
        }

        $file_count = count($scanned_files);

        for ($f = 0; $f < $file_count; $f++) {

            if (preg_match('/^.*\.(sql|json|csv|xml)$/i', $scanned_files[$f]) === 1) {
                $migration_files[] = $scanned_files[$f];
            }
        }

        $all_files = count($migration_files);

        if ($all_files === 1) {
            echo "Switching to single migration \n";

            self::config(array('migration' => $migration_files[0]));
            self::RunSingle();
            return;
        }


        echo "Running migrations...";
 
        for ($i = 0; $i < $all_files; $i++) {

            if (!self::checkMigrations(
                substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4)
                )
                ) {

                    try {
                        $query = file_get_contents(self::$dir.$migration_files[$i]);

                        $stmt = self::$db->prepare($query);
                        $stmt->execute();

                        self::$db = null;

                        self::$db = self::connect();
                        $query2 = "INSERT INTO migrations (migration_name) VALUES (:migration_name)";
                        $stmt2 = self::$db->prepare($query2);
                        $stmt2->execute(['migration_name' => substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4)]);

                        echo "\r\n Migrated " . substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4);
                    }catch (PDOException $e) {
                        echo $e->getMessage();
                    }
            } else {
                echo "\r\n There no new migrations to run.";
                return;
            }
        }
        echo "\r\nMigrations Complete.";
    }

    private static function checkMigrations(string $name = '') {

        $migrations_table = "CREATE TABLE migrations (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(100) NOT NULL,
            migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

        $sql = "SHOW TABLES LIKE 'migrations'";

        self::$db = self::connect();
        $stmt = self::$db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();


        if (empty($stmt->fetch())) {
            self::$db = null;

            self::$db = self::connect();
            try {
                self::$db->exec($migrations_table);
                self::$db = null;
                return false;
            }catch (PDOException $e) {
                echo $e->getMessage();
            }

        }else {
            $sql = "SELECT id FROM migrations WHERE migration_name = ?";

            self::$db = self::connect();
            try {
                $stmt = self::$db->prepare($sql);
                $stmt->bindParam(1, $name);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $stmt->execute();
                if (empty($stmt->fetch())) {
                    return false;
                }
                return true;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            
        }
    }

    public static function exportDataForMigration(bool $group=false) {

        if (!$group) {
            echo "Exporting...";
        }else {
            echo "Grouping Migrations...";
        }


        $table_name = self::$config->tables;
        $tables = array();

        $file_name = DATABASE_NAME;
        self::$db = self::connect();
        
        if (!self::$default_db) {
            self::$db->exec("USE ".$table_name[0]);
            $file_name = $table_name[0];
        }

        $stmt = self::$db->prepare("SHOW TABLES");
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $tables[] = $row[0];
        }

        $table_count = count($tables);
        for ($t = 0; $t < $table_count; $t++) {
            if ($tables[$t] == 'migrations') {
                unset($tables[$t]);
                break;
            }
        }
        
        if (count($table_name) > 1 or self::$is_table) {
            $tables = array();
            $tables = $table_name;
        }
        $sql_script = "-- BOOSTED MIGRATIONS MANAGER SQL Dump\n-- version ". self::$version ."\n-- https://boostedtechs.com/
        \n-- Host: ".SERVER_NAME . "\n-- Generation Time ".date('M d, Y ')." at ".date('h:i:s A')."\n-- Server Version: ".self::$server_version. "\n-- PHP Version: ". phpversion()."\n\n".'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";'."\n"."--\n-- Database: `".DATABASE_NAME ."`\n--\n\n-- ----------------------------------------------\n\n--\n";
       
        foreach ($tables as $table) {
            $sql_script .= "-- Table structure for table `".$table."`\n--";
            $query = "SHOW CREATE TABLE $table";
            $stmt = self::$db->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_NUM);
            $stmt->execute();
            $row = $stmt->fetch();

            $sql_script .= "\n\n" . $row[1] . ";\n\n";

            $query = "SELECT * FROM $table";
            $stmt = self::$db->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $result = $stmt->fetch();
            // Prepare SQL script for dumping data for each table
            $keys = array();
   
            if (!empty($result)) {
                $sql_script .= "--\n-- Dumping data for table `$table`-- \n\n";
                $sql_script .= "INSERT INTO `$table` (";
                foreach ($result as $key => $value) {
                    $keys[] = $key;
                }
                $key_count = count($keys);
                $columns = "";
                for ($k = 0; $k < $key_count; $k++) {
                    $columns .= "`".$keys[$k]."`,";
                }

                //get array for values 
                $query = "SELECT * FROM $table";
                $stmt = self::$db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll();
                $values = array_values($result);
                $values_count = count($values);
                
                $sql_script .= substr($columns, 0, strlen($columns) - 1).") VALUES ";

                $values_sql = "";
                for ($i = 0; $i < $values_count; $i++) {
                    $values_sql .= "(";
                    for ($j = 0; $j < (count($values[$i]) / 2); $j++) {
                        $values_sql .= "'".addslashes($values[$i][$j]) . "', ";
                    }
                    $values_sql = substr($values_sql, 0, strlen($values_sql) - 2);
                    $values_sql .= "), ";
                }
                $values_sql = substr($values_sql, 0, strlen($values_sql) -2).";";

                $sql_script .= $values_sql . "\n";
            }
        }
        $str_id = uniqid('', true);
        $id = substr($str_id, 15, 23);

        $file = self::$dir.$file_name.'_'.$id.'_'.date('Y-m-d-H-i-s').'.sql';

        $export = fopen($file, 'w+');
        fwrite($export, $sql_script);
        fclose($export);
        $res = "\n Exported data to $file \n" ."Export complete ";
        if ($group){
            $res = "\n All Migrations grouped in $file \n" ."Grouping complete";
        }
        echo $res;
    }

    public static function clearMigrations() {
        self::$db = self::connect();
        $stmt = self::$db->prepare("SHOW TABLES");
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute();
        $tables = array();
        while ($row = $stmt->fetch()) {
            $tables[] = $row[0];
        }
        if (empty($tables)) {
            echo "There are no migrations to clear!";
            return;
        }

        self::$db = null;

        self::$db = self::connect();
        echo "Clearing Migrations\n";
        foreach ($tables as $table) {
            try {
                if ($table !== 'migrations') {
                    $stmt = self::$db->prepare("DROP TABLE `$table`");
                    $stmt->execute();
                }
            } catch(PDOException $e) {

            }
        }

        try {
            $stmt = self::$db->prepare("TRUNCATE TABLE `migrations`");
            $stmt->execute();
        } catch(PDOException $e) {
            
        }

        echo "All Migrations cleared.\n";
    }

    /**
     * Group all migrations into a single migration file
     * 
     * The file generated should be migrated after clearing all migrations
     *
     * @return void
     */
    public static function groupMigrations() {
        $dir = self::$dir;
        array_map('unlink', glob("{$dir}*.sql"));
        self::exportDataForMigration(true);
    }

    public static function README() {
        $url = "https://github.com/boosteddevmate/boosted-migrations-manager/blob/main/app/migrations/README.md";
        $cmd=sprintf( 'start %s',$url );
        exec( $cmd );
    }

    public static function dropMigration() {
        echo "Dropping Migration... \n";
        self::$db = self::connect();
        $table = self::$config->table;
        $sql = "DROP TABLE $table";
        try {
            self::$db->exec($sql);
            echo "Migration dropped.";
        } catch (PDOException $e) {
            echo "Drop migration failed!";
        }
        
    }

    public static function listMigrations() {
        echo "Listing Migrations... \n";
        self::$db = self::connect();
        $stmt = self::$db->prepare("SHOW TABLES");
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute();
        $tables = array();
        while ($row = $stmt->fetch()) {
            $tables[] = $row[0];
        }
        if (empty($tables)) {
            echo "There are no migrations in ". DATABASE_NAME . " database";
            return;
        }

        $table_count = count($tables);
        for ($i = 0; $i < $table_count; $i++) {
            echo $tables[$i] . "\n";
        }
    }
}

//End Migrations class