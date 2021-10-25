<?php
namespace System\Database\Schema;

use PDO;
use PDOException;
use System\Database\Migrations\Migrations;

trait Modifications
{
    protected static $modify_sql = array();
    protected static $modify_table = '';
    protected static $rename_sql = '';
    protected static $drop_sql = '';
    protected static $add_sql = '';
    private $db;
    private $column;

    protected static function logError($error)
    {
        $root = getcwd();

        $f = fopen($root.'/database/logs/db-logs.txt', 'a+');
        $error = "[" . date("D d M Y H:i:s") . "] \t" . $error;
        $error .= "\n\n ----------------------------------------------------------------------- \n\n";
        fwrite($f, $error);
        fclose($f);
    }

    protected function columExists($column = null)
    {
        $this->db = Migrations::getMigrationInstance();
        $stmt = $this->db->prepare("DESCRIBE " . self::$modify_table);
        $stmt->execute();
        $data= $stmt->fetchAll();

        $fields  = array();
        
        foreach($data as $value)
        {
            $fields[$value['Field']] = 1;
        }
        
        return array_key_exists($column, $fields);

    }

    public function renameColumn(string $old, string $new, string $datatype, 
    int $length = null, bool $nullable = false, string $default = null)
    {
        
        self::$rename_sql = "ALTER TABLE `" . self::$modify_table . "` CHANGE `$old` `$new` ";
        $constraint = "NOT NULL";
        if($nullable)
        {
            $constraint = "NULL";
        }
        $datatype = strtoupper($datatype);

        if($length !== null)
        {
            self::$rename_sql .= $datatype . "($length) $constraint";
        }else{
            self::$rename_sql .= $datatype . " $constraint";
        }

        if($default !== null)
        {
            self::$rename_sql .= " DEFAULT '$default'";
        }
        self::$rename_sql .= ";";

        $this->db = Migrations::getMigrationInstance();
        echo "\e[0;33;40mRenaming column: \e[0m".  self::$modify_table . ".".   $old . "\n";

        try {

            $stmt = $this->db->prepare(self::$rename_sql);
            $stmt->execute();
            echo "\e[0;33;40mRenamed: \e[0m " .  self::$modify_table . ".".  $old 
            . " \e[0;33;40mTo \n[0m". self::$modify_table . ".".  $new ."\n";
            
        } catch (PDOException $th) {
            self::logError($th->getMessage());
            echo "\e[0;33;40mFailed to rename column: \e[0m Run migrate:logs for error logs \n";
        }
        $this->db = null;
    }

    
    public function dropColumn(string $column)
    { 

        self::$drop_sql = "ALTER TABLE `" . self::$modify_table. "` DROP COLUMN `$column`;";
        $this->db = Migrations::getMigrationInstance();
        echo "\e[0;33;40mDropping column: \e[0m".  self::$modify_table . ".".   $column ."\n";

        try {

            $stmt = $this->db->prepare(self::$drop_sql);
            $stmt->execute();
            echo "\e[0;32;40mDropped column: \e[0m" .  self::$modify_table . ".".  $column . "\n";

        } catch (PDOException $th) {
            self::logError($th->getMessage());
            echo "\e[0;33;40mFailed to drop column: \e[0m Run migrate:logs for error logs \n";
        }

        $this->db = null;
    }

    public function addColumn(string $column, string $datatype, 
    int $length = null, bool $nullable = false, string $default = null)
    {

        self::$add_sql = "ALTER TABLE `" . self::$modify_table ."`" ;

        $datatype = strtoupper($datatype);
        $constraint = "NOT NULL";
        if($nullable)
        {
            $constraint = "NULL";
        }

        if($length !== null)
        {
            self::$add_sql .=" ADD `$column` ". $datatype . "($length) $constraint, ";
            return $this;
        }
        self::$add_sql .= " ADD `$column` $datatype $constraint, ";

        if($default !== null)
        {
            self::$add_sql = substr(self::$add_sql, 0, strlen(self::$add_sql) - 2)
            . " DEFAULT '$default'  ";
        }
        $this->column = $column;
        return $this;
    }

    
    public function after($column = null)
    {

        self::$add_sql = substr(self::$add_sql, 0, strlen(self::$add_sql) - 2);
        if($column !== null)
        {

            self::$add_sql .= " AFTER `{$column}`;";
        }

        $this->db = Migrations::getMigrationInstance();
        echo "\e[0;33;40mAdding column: \e[0m ". self::$modify_table . ".".  $this->column . "\n";

        try {

            $stmt = $this->db->prepare(self::$add_sql);
            $stmt->execute();
            echo "\e[0;32;40mAdded column: \e[0m " .  self::$modify_table . ".".  $this->column . "\n";

        } catch (PDOException $th) {
            self::logError($th->getMessage());
            echo "\e[0;33;40mFailed to add new column: \e[0m Run migrate:logs for error logs \n";
        }
        $this->db = null;
    }
}