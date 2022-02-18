<?php
namespace System\Database\Migrations;

use League\BooBoo\BooBoo;
use League\BooBoo\Formatter\CommandLineFormatter;
require_once @getcwd() . '/vendor/autoload.php';
date_default_timezone_set(env("TIMEZONE"));
require_once @getcwd() .'/app/Config/database.php';
//exception handling
$booboo = new BooBoo([new CommandLineFormatter()]);

$booboo->register(); // Registers the handlers

$db_config = (object) $config;

use PDO;
use PDOException;
use System\Database\Schema\ColumnDefination;

define('SERVER_NAME', $db_config->DB_HOST);

define('USER_NAME', $db_config->DB_USER);

define('PASSWORD', $db_config->DB_PASSWORD);

define('DATABASE_NAME', $db_config->DB_NAME);

define('PORT', (int)$db_config->PORT);

define('MIGRATIONS_DIR', '/database/migrations/');

/**
 * PHASER MIGRATIONS MANAGER
 */
class Migrations extends ColumnDefination
{   
    private static $db;

    private static $config;

    private static $dir;

    public static $version = '1.0.1';

    private static $is_file;

    private static $server_version;


    public function __destruct()
    {
        self::$db = null;
    }

    private static function connect(bool $is_db = false) {
        $mysql_host = SERVER_NAME;
        $mysql_database = DATABASE_NAME;
        $mysql_user = USER_NAME;
        $mysql_password = PASSWORD;
        try {
            if($is_db)
            {
                return new PDO("mysql:host=$mysql_host;", $mysql_user, $mysql_password);
            }
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$server_version = $conn->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        return $conn;
    }

    public static function getMigrationInstance()
    {
        return self::connect();
    }

    public static function __init__(string $path) {
        self::$dir = $path .MIGRATIONS_DIR;
        if (!file_exists(self::$dir)) {
            $dir = getcwd() . '/database/';
            mkdir($dir);
            $dir = getcwd() . '/database/migrations/';
            mkdir($dir);
        }
    }

    public static function config(array $config, bool $is_file = false) {
        self::$config = (object) $config;
        self::$is_file = $is_file;
    }

    public static function createDatabase(string $dbname = DATABASE_NAME)
    {
        $db = self::connect(true);
        try {
            $db->exec("CREATE DATABASE $dbname");
            echo "\e[0;32;40mCreated database \e[0m" . $dbname . "\n";
        } catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * Run all application's migrations
     *
     * @return void
     */
    public static function RunGroupedMigrations() {

        self::$db = self::connect();

        $migration_files = array();

        $scanned_files = scandir(self::$dir);
        if (empty($scanned_files)) {
            echo "\e[0;33;40mNo grouped migrations found!\e[0m\n";
            return;
        }

        $file_count = count($scanned_files);

        for ($f = 0; $f < $file_count; $f++) {

            if (preg_match('/^.*\.(sql|json|csv|xml)$/i', $scanned_files[$f]) === 1) {
                $migration_files[] = $scanned_files[$f];
            }
        }

        $all_files = count($migration_files);

        echo "\e[0;33;40mRunning grouped migrations...\e[0m\n";
        $migration_exists = 0;

        for ($i = 0; $i < $all_files; $i++) {
            $migration_file_name = substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4);
            if (self::checkMigrations($migration_file_name))
            {
                $migration_exists += 1;
                continue;
            }

            try {
                $query = file_get_contents(self::$dir.$migration_files[$i]);

                $stmt = self::$db->prepare($query);
                $stmt->execute();
                if(self::storeMigration($migration_file_name))
                {
                    echo "\e[0;32;40mMigrated: \e[0m" . $migration_file_name;
                }else {
                    echo "\e[0;33;40mFailed to Migrate: \e[0m" . $migration_file_name;
                }

            }catch (PDOException $e) {
                self::logError($e->getMessage());
                echo "\e[0;33;40mFailed to Migrate: \e[0m" . $migration_file_name . "\n";
                echo "\e[0;33;40mRun migrate:logs to view error logs. \n";
            }
        }
        
        if($migration_exists == $all_files)
        {
            echo "\e[0;33;40mNothing to migrate! \e[0m \n";
        }
    }


    private static function initMigrations() {
        $migrations_table = "CREATE TABLE migrations (
            id BIGINT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(100) NOT NULL,
            migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

        $sql = "SHOW TABLES LIKE 'migrations'";

        self::$db = self::connect();
        $stmt = self::$db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        if (empty($stmt->fetch())) {
            try {
                self::$db->exec($migrations_table);
                echo "\e[0;32;40mInitialized Migrations successfully\e[0m \n";
            }catch (PDOException $e) {
                self::logError($e->getMessage());
                echo "\e[0;33;40mFailed to Initialize migrations \e[0m\n";
                echo "\e[0;33;40mRun migrate:logs to view error logs.\e[0m \n";
            }

        }
    }



    private static function checkMigrations(string $name = '') {
        $sql = "SELECT id FROM migrations WHERE migration = ?";

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



    private static function exportDataForMigration() {

        echo "\e[0;33;40mGrouping Migrations... \e[0m \n";
        $tables = array();

        self::$db = self::connect();

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
   
            if (!empty($result)) {
                $sql_script .= "--\n-- Dumping data for table `$table`-- \n\n";
                $sql_script .= "INSERT INTO `$table` (";
                $keys = array_keys($result);
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


        $file = self::$dir.date('Y_m_d_His').'_grouped_migrations.sql';

        $export = fopen($file, 'w+');
        fwrite($export, $sql_script);
        fclose($export);

        echo "\e[0;32;40mAll Migrations grouped\e[0m \n";
    }

    public static function clearMigrations() {
        self::$db = self::connect();
        try {
            $stmt = self::$db->prepare("TRUNCATE TABLE `migrations`");
            $stmt->execute();
        } catch(PDOException $e) {
            
        }
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
        array_map('unlink', glob("{$dir}*"));
        self::exportDataForMigration(true);
    }

    public static function README() {
        $url = "https://github.com/boosteddevmate/boosted-migrations-manager/blob/main/app/migrations/README.md";
        $cmd=sprintf( 'start %s',$url );
        exec( $cmd );
    }

    public static function listMigrations()
    {
        echo "\e[0;33;40mListing Migrations... \e[0m\n";
        self::initMigrations();
        self::$db = self::connect();
        $stmt = self::$db->prepare("SELECT * FROM migrations");
        $stmt->execute();
        $tables = $stmt->fetchAll();

        if (empty($tables)) {
            echo "\e[0;33;40mThere no migrations. Did you forget to run php manage migrate? \e[0m\n";
            return;
        }

        foreach($tables as $value) {
            echo "\e[0;32;40m".$value['id']. ". ". $value['migration'] . ": ". $value['migrated_at']."\e[0m \n";
        }
    }



    public static function makeMigration()
    {
        $table = self::$config->table;
        $scanned_files = scandir(self::$dir);
        $file = $table . ".php";

        foreach ($scanned_files as  $value) {
            $mig_f = substr($value, 18, strlen($value) - 18);
            if($mig_f === $file)
            {
                echo "\e[0;33;40mMigration already exists! \e[0m\n";
                return;
            }
        }
        $class = str_replace('_', ' ', $table);
        $class = ucwords($class);
        $class = preg_replace('/\s+/', '', $class);

        $tableArray = explode('_', $table);
        array_pop($tableArray);
        array_shift($tableArray);
        $tableName = '';

        if (count($tableArray) === 1)
        {
            $tableName = $tableArray[0];

        }else {
            $tableName = implode('_', $tableArray);
        }

        $code = "<?php\n";
        $code .= "use System\Database\Schema\BluePrint;\n";
        $code .= "use System\Database\Schema\Schema;\n";
        $code .= "\n\n class " . $class . "\n { \n";
        $code .= "\t\t/**\n\t\t* Run the Migrations\n\t\t*\n\t\t* @return void\n\t\t*/ \n\t\t";
        $code .= "public function up()\n\t\t{\n\n";
        $code .= "\t\t\tSchema::create('" . $tableName . "', function (BluePrint $"."table) {\n\n";
        $code .= "\t\t\t\t$" . "table->id();\n\t\t\t\t$"."table->timestamps(); \n\t\t\t}); \n\n\t\t} \n\n\t\t";
        $code .= "/**\n\t\t* Modify Migrations\n\t\t*\n\t\t* @return void\n\t\t*/ \n\t\t";
        $code .= "public function alter()\n\t\t{\n\n";
        $code .= "\t\t\tSchema::modify('" . $tableName . "', function (BluePrint $"."table) {\n\n";
        $code .= "\t\t\t\t" . " \n\t\t\t}); \n\n\t\t} \n\n\t\t";
        $code .= "/**\n\t\t* Reverse the migrations.\n\t\t*\n\t\t* @return void\n\t\t*/\n\n\t\tpublic function down()\n\t\t{\n\n\t\t\tSchema::dropIfExists('" . $tableName . "');
     \n\t\t} \n\n}";
        $file_name = date('Y_m_d_His_').$table . ".php";
        $file = fopen(self::$dir.$file_name, 'w+');
        fwrite($file, $code);
        fclose($file);
        echo "\e[0;32;40mCreated Migration:\e[0m ".substr($file_name, 0, strlen($file_name) - 4);
    }

    private static function storeMigration(string $migrations_table_file)
    {
        self::$db = self::connect();
        $stmt = self::$db->prepare("INSERT INTO migrations(`migration`) VALUES(?)");
        $stmt->bindParam(1, $migrations_table_file);
        try{
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }catch(PDOException $e)
        {

        }
    }

    public static function RunAll(bool $refresh = false, array $migration_classes = [],
     array $migration_files = [])
    {
        self::$db = self::connect();

        if(!$refresh)
        {
            self::initMigrations();

            if(empty($migration_files))
            {
                $scanned_files = scandir(self::$dir);

                $file_count = count($scanned_files);
    
    
                for ($f = 0; $f < $file_count; $f++) {
    
                    if (preg_match('/^.*\.(php)$/i', $scanned_files[$f]) === 1) {
                        $migration_files[] = $scanned_files[$f];
                    }
                }
            }

            $file_count = count($migration_files);
        
            for($i = 0; $i < $file_count; $i++){
                $file = getcwd() . MIGRATIONS_DIR . $migration_files[$i];
                if(self::$is_file)
                {
                    $file = $file . ".php";
                }
                $st = get_declared_classes();
                include $file;

                $migration_classes[] = array_values(array_diff_key(get_declared_classes(),$st))[0];
            }
        }

        $migration_classes_count = count($migration_classes);
        $migration_exists = 0;

        for($i = 0; $i < $migration_classes_count; $i++){
            $object = new $migration_classes[$i];
            call_user_func([$object, 'up']);
            
            $migrations_table_file = substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4);
            if(self::$is_file) {
                $migrations_table_file = $migration_files[$i];
            }
            try {
                if(self::checkMigrations($migrations_table_file))
                {
                    $migration_exists += 1;
                    continue;
                }
                // echo self::$migration . "\n\n";

                echo "\e[0;33;40mMigrating: \e[0m";
                echo $migrations_table_file . "\n";
    
                self::$db->exec(self::$migration);

                echo "\e[0;32;40mMigrated: \e[0m ";
                echo $migrations_table_file. "\n";

                self::storeMigration($migrations_table_file);

            } catch (PDOException $e) {
                self::logError($e->getMessage());
                echo "\e[0;33;40mFailed to Migrate: \e[0m" . $migrations_table_file . "\n";
                echo "\e[0;33;40mRun migrate:logs to view error logs. \e[0m\n";
            }
        }

        if($migration_exists == $migration_classes_count)
        {
            echo "\e[0;33;40mNothing to migrate! \e[0m";
        }

    }

    public static function rollBack(bool $refresh = false)
    {
        self::$db = self::connect();
        $migration_files = array();

        $scanned_files = scandir(self::$dir);

        $file_count = count($scanned_files);

        $migration_classes = array();

        for ($f = 0; $f < $file_count; $f++) {

            if (preg_match('/^.*\.(php)$/i', $scanned_files[$f]) === 1) {
                $migration_files[] = $scanned_files[$f];
            }
        }

        $file_count = count($migration_files);
        
        $migration_files = array_reverse($migration_files);
        for($i = 0; $i < $file_count; $i++){
            $file = getcwd() . MIGRATIONS_DIR . $migration_files[$i];
            
            $st = get_declared_classes();
            include $file;

            $migration_classes[] = array_values(array_diff_key(get_declared_classes(),$st))[0];
        }

        $migration_classes_count = count($migration_classes);
        
        for($i = 0; $i < $migration_classes_count; $i++){
            $object2 = new $migration_classes[$i];
            call_user_func([$object2, 'down']);
            try {
                    echo "\e[0;33;40mRolling Back: \e[0m";
                    echo substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4) . "\n";

                    if(!empty(self::$dropForeignkey)){
                        foreach(self::$dropForeignkey as $sql){
                            self::$db->exec($sql);
                        }
                    }

                    self::$db->exec(self::$rollBackMigration);
                    self::$dropForeignkey = array();
                    self::$rollBackMigration = '';

                    echo "\e[0;32;40mRolled Back: \e[0m ";
                    echo substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4) . "\n";

            } catch (PDOException $e) {
                self::logError($e->getMessage());
                echo "\e[0;33;40mFailed to Rollback: \e[0m" . substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4) . "\n";
                echo "\e[0;33;40mRun migrate:logs to view error logs. \e[0m\n";
            }

            self::clearMigrations();
        }

        if($refresh){
            $migration_classes = array_reverse($migration_classes);
            $migration_files = array_reverse($migration_files);
            self::clearMigrations();
            self::RunAll(true, $migration_classes, $migration_files);
        }

    }


    public static function modifyMigrations()
    {
        self::$db = self::connect();
        $migration_files = array();

        $scanned_files = scandir(self::$dir);

        $file_count = count($scanned_files);

        $migration_classes = array();

        for ($f = 0; $f < $file_count; $f++) {

            if (preg_match('/^.*\.(php)$/i', $scanned_files[$f]) === 1) {
                $migration_files[] = $scanned_files[$f];
            }
        }

        $file_count = count($migration_files);
        
        $migration_files = array_reverse($migration_files);
        for($i = 0; $i < $file_count; $i++){
            $file = getcwd() . MIGRATIONS_DIR . $migration_files[$i];
            
            $st = get_declared_classes();
            include $file;

            $migration_classes[] = array_values(array_diff_key(get_declared_classes(),$st))[0];
        }

        $migration_classes_count = count($migration_classes);
        
        for($i = 0; $i < $migration_classes_count; $i++){
            $object2 = new $migration_classes[$i];
            echo "\e[0;32;40mAttempt to Modify: \e[0m";
            echo substr($migration_files[$i], 0, strlen($migration_files[$i]) - 4) . "\n";
            
            call_user_func([$object2, 'alter']);

        }

    }

    public static function showMigrationErrors()
    {
        $root = getcwd();

        $f = fopen($root.'/database/logs/db-logs.txt', 'r');

        $content = fread($f, filesize($root.'/database/logs/db-logs.txt'));
        d($content);
    }

    public static function clearMigrationErrors()
    {
        echo "\e[0;33;40mClearing Migration logs... \e[0m\n";
        $root = getcwd();

        $f = fopen($root.'/database/logs/db-logs.txt', 'w+');
        fwrite($f, '');
        fclose($f);
        echo "\e[0;32;40mMigration logs cleared. \e[0m\n";

    }
}

//End Migrations class