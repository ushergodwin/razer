<?php
namespace System\Database;

use Exception;
use PDO;
use PDOException;

strtolower(env("AUTO_START_SESSION")) === "true" ? session_start() : NULL;
strtolower(env("ERROR_REPORTING")) === "true" ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

define("HOST", env("DB_HOST"));
define("USERNAME", env("DB_USER"));
define("PASSWORD", env("DB_PASSWORD"));
define("DB", env("DB_NAME"));
define("TIMEZONE", env("TIMEZONE"));
  
/**
 * @category  Database Access
 * @package   Database
 * @author Tumuhimbise Godwin
 * @copyright Copyright (c) 2020-2021
 * @version   2.0
 */
class Database
{
    private $_conn;

    private  $_insertQuery = "";

    private   $_updateQuery = "";

    private  $_deleteQuery = "";

    private   $_selectQuery = "";

    private  $_cols = array();

    private   $values = array();

    private  $insert_place_holders = array();

    private   $named_keys = array();

    protected   $_where = array();

    private   $_whereCols = array();

    private   $_orderBy = "";

    protected   $_operator = "";

    private   $_rows = array();

    private   $_updateData = array();

    private   $_updateQueryData = array();

    protected   $_whereOr = array();


    private $_database;

    protected $join_query = "";

    protected $_is_slect = false;

    private $_betweenData = array();

    protected $_likeData = array();

    protected $objectTableName;

    protected $objectTableData;

    protected $objectTableColumns;


    protected $action = '';
    protected $is_one_value = false;
    protected $is_distinct = false;

    /**
     * Instantiate the Database Model
     * @param string $database The database to use in the connection establishment. Default DB is the one set in the environment configurations (in .env file).
     * @param string $time_zone The time zone to be used by the server. default is Africa/Kampala
     */
    public function __construct(string $database = DB, string $time_zone = TIMEZONE) {
        date_default_timezone_set($time_zone);
        try {
            if (empty(trim($this->_database))) {
                $this->_database = $database;
            }
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
    }

    /**
     * @param string $database_name The name of the database to use for querying.
     * Use this function if you intend not use the default database
     * @return void
     */
    public function use($database_name) {
        $this->_database = $database_name;
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->_database = $this->_database;
        $instance->action = $this->action;
        
        return $instance;
    }

    private function connect() {
        $dsn = "mysql:host=".HOST.";dbname=".$this->_database;
        $conn = (object) "";
        try {
            $conn = new PDO($dsn, USERNAME, PASSWORD, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $PDOException) {
            echo $PDOException->getMessage();
        }
        return $conn;
    }
        /**
     * Reset states after an execution
     *
     * @return Database Returns the current instance.
     */
    private function reset() {
        $this->_insertQuery = "";
        $this->_updateQuery = "";
        $this->_deleteQuery = "";
        $this->_selectQuery = "";
        $this->_cols = array();
        $this->values = array() ;
        $this->place_holders = array();
        $this->insert_place_holders = array();
        $this->named_keys = array();
        $this->_where = array();
        $this->_whereCols = array();
        $this->_orderBy = "";
        $this->_operator = "";
        $this->_updateData = array();
        $this->_updateQueryData = array();
        $this->_whereOr = array();
        $this->_whereTable = "";
        $this->_andOperator = "";
        $this->_andKeys = array();
        $this->_errorInfo = array();
        $this->join_query = "";
        $this->_betweenData = array();
        $this->_likeData = array();
        $this->action = '';
        $this->is_one_value = false;
        $this->is_distinct = false;
        return $this;
    }

    public function __init__($tableName = "", $columns = "*", array $tableData = []) {
        $this->objectTableData = $tableData;
        $this->objectTableColumns = $columns;
        $this->objectTableName = $tableName;
        $this->action = 'many';
    }


    /**
     * @param string $tableName The name of the table to insert the data to.
     * @param array $tableData An associative array containing column names and their data to insert in the table.
     *
     * @return int Number of affected rows
     * 
     */
    public  function insert($tableName, array $tableData) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        if(!is_array($tableData)) {
            return false;
        }
        $stmt = $this->_conn->prepare($this->buildInsert($tableName, $tableData));
        try{
            $stmt->execute($tableData);
            $this->_conn->commit();
        } catch (PDOException $error) {
            $this->_conn->rollBack();
            echo $error->getMessage();
        }
        
        $this->reset();
        return $stmt->rowCount();

    }

    public function save() {
        
        if (empty($this->_where) and gettype(reset($this->objectTableData)) == 'string') {
            return $this->insert($this->objectTableName, $this->objectTableData);
        }elseif(gettype(reset($this->objectTableData)) == "array") {
            return $this->insertMany($this->objectTableName, $this->objectTableData);
        }
        return $this->update($this->objectTableName, $this->objectTableData);
    }



    public function row(string $columns = '*') {
        $this->action = 'one';

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $columns);
        $instance->action = $this->action;
        $instance->_database = $this->_database;
        return $instance;
    }



    public function select(string $columns) {
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $columns);
        $instance->action = 'many';
        $instance->_database = $this->_database;
        return $instance;
    }



    public function distinct(string $columns) {
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $columns);
        $instance->is_distinct = true;
        $instance->action = 'many';
        $instance->_database = $this->_database;

        return $instance;
    }



    public function value(string $column) {
        return $this->getOne($column, $this->objectTableName);
    }



    public function count(string $column = '*') {
        if (!empty($this->_where)) {
            return $this->countRows($column, $this->objectTableName);
        }
        return count($this->getAll($column, $this->objectTableName));
    }



    public function max(string $column) {
        return $this->aggregate($column, $this->objectTableName);
    }



    public function min(string $column) {
        return $this->aggregate($column, $this->objectTableName, "MIN");
    }



    public function avg(string $column) {
        return $this->aggregate($column, $this->objectTableName, "AVG");
    }



    public function sum(string $column) {
        return $this->aggregate($column, $this->objectTableName, "SUM");
    }



    public function exists() {
        $data = $this->getOneRow($this->objectTableColumns, $this->objectTableName);
        if (!empty($data)) {
            return true;
        }
        return false;
    }



    /**
     * @param string $columns A string of columns to return separated by commas
     * @param string $tableName A string containing the table name to select data from.
     * @param int|null $limit An optional integer of number of rows to select from the table.
     * @param bool $is_distinct
     * @return array     An associative array of returned rows
     */
    private function getAll($columns, $tableName, $limit = null, $is_distinct = false) {
        $this->_conn = $this->connect();

        try {
            if (!is_string($columns)):
                throw new Exception("Expected a string of columns separated by commas");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($columns, $tableName, $limit, $is_distinct));

        try {
            empty($this->_where) ? $stmt->execute() :  $stmt->execute($this->_where);
            $this->reset();
            return $stmt->fetchAll();
        } catch (PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }


     /**
     * @param string $columns A string of columns to return separated by commas
     * @param string $tableName A string containing the table name to select data from.
     * @param int $limit An optional integer of number of rows to select from the table.
     * @return array     An array of the returned rows
     */
    public function getOneRow($columns, $tableName, $limit = 1) {
        $this->_conn = $this->connect();

        try {
            if (!is_string($columns)):
                throw new Exception("Expected a string of columns separated by commas");
            endif;
            if (empty($this->_where)):
                throw new Exception("Expected a condition to be passed to the where method first, 0 given.");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($columns, $tableName, $limit));

        try {
            empty($this->_where) ? $stmt->execute() :  $stmt->execute($this->_where);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
      
            $this->reset();
            return $stmt->fetch();
        }catch (PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }
  


    /**
     * @param array $wherePos  An associative array with the where column and the condition in the first index (0). The Optional second index (1) takes the AND clause with the column and the condition. The Optional third index takes the OR with column name and the condition.
     * @param string $operator Takes the operator to be used. Default is =;
     * @return object An Instance of the Database class
     */
    public function where(array $wherePos, $orderBy = '', $operator = '=') {
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns, $this->objectTableData);

        $instance->action = $this->action;

        $instance->is_one_value = $this->is_one_value;

        $instance->is_distinct = $this->is_distinct;

        $instance->_is_slect = true;
        $instance->_where = $wherePos;
        $instance->_operator = explode(',', $operator);
        $_len = count($instance->_where);
        if ($_len > 1 and count($instance->_operator) == 1) {
            for ($i = 0; $i < $_len; $i++) {
                array_push($instance->_operator, '=');
            } 
        }

        if (!empty(trim($orderBy))) {
            $instance->_orderBy = $orderBy;
        }
        $instance->_database = $this->_database;
        return $instance;
    }



    /**
     * @param string $column A string containing the column name to select
     * @param string $tableName A string containing the table name to select data from.
     * Call The where method if you have a condition to base on in selection of data
     * @param int $limit A string containing the limit of data to select
     * @return string A string of data for a single column
     * The where() and or and() methods should be called first if a condition is requiredS
     */
    private function getOne($column, $tableName, $limit = 1) {
        $this->_conn = $this->connect();
 
        try {
            if (empty($this->_where)):
                throw new Exception("Expected a condition to be passed to the where method first:: 0 given");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($column, $tableName, $limit));
        
        try {
            $stmt->execute($this->_where);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
    
            $this->reset();
            return !empty($result) ? $result[$column] : "";
        }catch (PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }



    /**
     * An abstract function to build the inset query
     * @param string $tableName
     * @param array $tableData
     * @return string
     */
    private function buildInsert($tableName, array $tableData) {
        if(!empty($tableData) && !empty($tableName)):
            foreach ($tableData as $key => $columns) {
                $this->_cols[] = $key;
                $this->insert_place_holders[] = $key;
            }
            $this->_insertQuery = "INSERT INTO $tableName (".implode(',',$this->_cols).") VALUES (";
            foreach ($tableData as $values) {
                $this->values[] = $values;
            }
            $this->_insertQuery .= ":".implode(', :', $this->insert_place_holders);
            $this->_insertQuery .=")";
        endif;
        return $this->_insertQuery;    
    }



    /**
     * Abstraction class that will build the select query
     * @param string $columns
     * @param string $tableName
     * @param null $limit
     * @return string
     */
    private function buildSelect($columns, $tableName, $limit = null, $is_distinct = false){

        $this->_selectQuery = "SELECT ";
        if ($is_distinct) {
            $this->_selectQuery .= "DISTINCT ";
        }
        $this->_selectQuery .= $columns." FROM ".$tableName;

        if(!empty($this->_where)){
            $this->named_keys =array_keys($this->_where);
            $this->_selectQuery .= " WHERE ";
            $where_len = count($this->named_keys);
            for ($i = 0; $i < $where_len; $i++) {
                $this->_selectQuery .= $this->named_keys[$i] . " ". $this->_operator[$i] . " :".$this->named_keys[$i] . " AND ";
            }
    
            $sql_len = strlen($this->_selectQuery);
            $this->_selectQuery = substr($this->_selectQuery, 0, $sql_len - 4);

            if (!empty($this->_whereOr)) {
                $this->_selectQuery .= " OR ";
                $where_keys = array_keys($this->_whereOr);
                $where_or_len = count($where_keys);
                for ($i = 0; $i < $where_or_len; $i++) {
                    $this->_selectQuery .= $where_keys[$i] ." " .$this->_operator[$i] ." :".$where_keys[$i] . " OR ";
                }
                $this->_selectQuery = substr($this->_selectQuery, 0, strlen($this->_selectQuery) - 3);
                $this->_where = array_merge($this->_where, $this->_whereOr);
            }
        }
        !empty($this->_orderBy)?$this->_selectQuery .=" ORDER BY ".$this->_orderBy : $this->_selectQuery;  
        $limit !== null ? $this->_selectQuery .=" LIMIT ".$limit : $this->_selectQuery;
        
        return $this->_selectQuery;
    }


    /**
     * Abstract function to build the update query
     * @param string $tableName
     * @param array $updateData
     * @return string
     */
    private function buildUpdate($tableName, array $updateData) {
        $this->_updateData = array_merge($this->_updateData, $updateData);  
        $this->_cols = array_keys($this->_updateData);
        $count = count($this->_updateData);
        $sql = "UPDATE $tableName SET ";
        try {
            if ($count == 0):
                throw new Exception("Expected at least 1 column and value to update");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }

        try {
            if (empty($this->_where)):
                throw new Exception("Expected the where method to be called first");
            endif;
        } catch (Exception $exception) {
                echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $keys = array_keys($updateData);
        $where_keys = array_keys($this->_where);
        $data_len = count($keys);
        $where_len = count($this->_where);
        for ($i = 0; $i < $data_len; $i++) {
            $sql .= $keys[$i] . " = :".$keys[$i] . ", ";
        }
        $sql = substr($sql, 0, strlen($sql) - 2);
        $sql .= " WHERE ";
        for ($i = 0; $i < $where_len; $i++) {
            $sql .= $where_keys[$i] . " ". $this->_operator[$i] . " :".$where_keys[$i] . " AND ";
        }

        $sql_len = strlen($sql);
        $this->_updateQuery = substr($sql, 0, $sql_len - 4);
        $this->_updateQueryData = array_merge($updateData, $this->_where);

        return $this->_updateQuery;        
    }


    /**
     * Update Database
     * @param string $tableName The name of the table to update
     * @param array $updateData An associative array containing columns and column values to update. 9 columns  can be updated at once.
     * @return int Number of affected rows
     * The Where() the method should be called first
     * 
     */
    private function update($tableName, array $updateData) {
        try {
            if (!is_array($updateData)):
                throw new Exception("Expected the Update data to be of type array");
            endif;
            if (empty($this->_where)):
                throw new Exception("Expected the condition to be parsed first in the where method");
            endif;
        }catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildUpdate($tableName, $updateData));
        
        try {
            $stmt->execute($this->_updateQueryData);
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        }catch(PDOException $error) {
            echo $error->getMessage();
            return;
        }

    }


    private function buildInsertMany($tableName, array $tableData) {
        if(!empty($tableData) && !empty($tableName)):
            foreach ($tableData[0] as $key => $columns) {
                $this->_cols[] = $key;
                $this->insert_place_holders[] = $key;
            }
            $this->_insertQuery = "INSERT INTO $tableName (".implode(', ',$this->_cols).") VALUES (";
            foreach ($tableData as $values) {
                $this->values[] = $values;
            }
            $this->_insertQuery .= ":".implode(', :', $this->insert_place_holders);
            $this->_insertQuery .=")";
        endif;
        return $this->_insertQuery;  
    }



    /**
     * @param string $tableName  The name of the table to insert values to
     * @param array $tableData  A Multi-dimensional array containing rows of data to insert
     * @return array An array of affected rows, accessed by calling the affectedRows method
     */
    private function insertMany($tableName, array $tableData) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        try {
            if (!is_array($tableData)):
                throw new Exception("Expected insertion data to be of type array");
            endif;
        }catch (Exception $exception){
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildInsertMany($tableName, $tableData));

        try {
            foreach($tableData as $row) {
                $stmt->execute($row);
            }
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        } catch (PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }



    /**
     * @param string $tableName A string containing the name of the table to delete a record from
     * @param string $operator An optional string containing the operator to use in the where clause, Default is "="
     * @return array ::rowCount    An array of affected rows, accessed by calling the affectedRows method.
     *The where method should be called first, and the parsed array should have not more than 2 indexes
     */
    public function delete($tableName, bool $all = false, $operator = "=") {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildDelete($tableName, $all, $operator));
        
        try {
            $stmt->execute($this->_where);
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        } catch(PDOException $error) {
            echo $error->getMessage();
            return;
        }

    }



    /**
     * Abstract function to build the delete query
     * @param string $tableName
     * @param string $operator
     * @return string
     * The where method should be called first
     */
    private function buildDelete($tableName, $all = false, $operator = "="){
        $this->_deleteQuery = "DELETE FROM ".$tableName;
        $this->_operator = explode(',', $operator);
        if ($all) {
            $this->_deleteQuery = "TRUNCATE table ".$tableName;
        }
        if(!empty($this->_where)) {
            $where_keys = array_keys($this->_where);
            $where_len = count($this->_where);
            $this->_deleteQuery .= " WHERE ";
            for ($i = 0; $i < $where_len; $i++) {
                $this->_deleteQuery .= $where_keys[$i] . " ". $this->_operator[$i] . " :".$where_keys[$i] . " AND ";
            }
    
            $sql_len = strlen($this->_deleteQuery);
            $this->_deleteQuery = substr($this->_deleteQuery, 0, $sql_len - 4);
        }
        return $this->_deleteQuery;    
    }



    /**
     * @param string $tableName A string containing the name of the table to act on
     * @param array $tableData An array containing a status name and the value to change. Use logical True(1) and False(0) as values to mark trashed rows. The same method can be used to un trash a row.
     * @return array ::rowCount     An array containing the number of affected rows.
     * The where method should be called first
     */
    public function trash($tableName, array $tableData){
        return $this->update($tableName, $tableData);
    }



    /**
     * Execute a custom query for SELECT OR UPDATE
     * @param string $query A string containing a custom prepared query statement to execute. Must be a select Query using placeholders (?) if the condition is used
     * @param array $data The data to bind with the  parameters
     * @param string $fetchMode MULTI (returns a multidimensional array) ASSOC (returns a 1 dimension associative array)
     * @return array An associative array containing rows returned from the query
     */
    public function query($query, array $data = [], $fetchMode = "MULTI"){
        
        if (strpos($query, "SELECT") !== false){

            $this->_conn = $this->connect();
            $stmt = $this->_conn->prepare($query);
            array_unshift($data, true);
            $i = 0;
            $data_count = count($data);
            while ($i < $data_count - 1) {
                $i++;
                $stmt->bindParam($i, $data[$i]);
            }

            try {
                $stmt->execute();

                if (strtolower($fetchMode) == "assoc") {
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $data = $stmt->fetch();
                }else{
                    $data = $stmt->fetchAll();
                }
                return $data;
            }catch(PDOException $error) {
                echo $error->getMessage();
                return;
            }

        }

        return $this->_update($query, $data);
    }

    /**
     * @param string $query A string containing a custom prepared statement query to execute. Must be an UPDATE Query using place holders (?)
     * @param array $data An array of data to update
     * @return int affected rows returned from the query
     */
    private function _update($query, array $data){
        try {
            if (strpos($query, "UPDATE") === false):
                throw new Exception("Expected a prepared Update statement but instead saw " . $query);
            endif;
        }catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        array_unshift($data, true);
        $i = 0;
        $data_count = count($data);
        $stmt = $this->_conn->prepare($query);
        while ($i < $data_count - 1) {
            $i++;
            $stmt->bindParam($i, $data[$i]);
        }

        try {
            $stmt->execute();
            $this->_conn->commit();
            return $stmt->rowCount();
        } catch(PDOException $error) {
            echo $error->getMessage();
        }

    }



    private function buildCount($column, $tableName) {
        $this->_selectQuery = "SELECT COUNT($column) FROM ".$tableName;
        if(!empty($this->_where)) {
            $where_keys = array_keys($this->_where);
            $where_len = count($this->_where);
            $this->_selectQuery .= " WHERE ";
            for ($i = 0; $i < $where_len; $i++) {
                $this->_selectQuery .= $where_keys[$i] . " ". $this->_operator[$i] . " :".$where_keys[$i] . " AND ";
            }
            
            $sql_len = strlen($this->_selectQuery);
            $this->_selectQuery = substr($this->_selectQuery, 0, $sql_len - 4);
        }
        return $this->_selectQuery;
    }



     /**
     * @param string $column A column name from where the count will happen
     * @param string $tableName A table name to count from
     * @return int   Number of count returned
     */
    public function countRows($column, $tableName){
        $this->_conn = $this->connect();
 
        $stmt = $this->_conn->prepare($this->buildCount($column, $tableName));

        try {
            empty($this->_where) ? $stmt->execute(): $stmt->execute($this->_where);

            $this->reset();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
            return $result['COUNT('.$column.')'];
        }
        catch(PDOException $error) {
            echo $error->getMessage();
        }
    }



    /**
     * @abstract Function to built counting of data
     * @param string $column
     * @param string $tableName
     * @return string Query to executed
     */
    private function buildAggregate($column, $tableName, $aggregate = "MAX") {
        $this->_selectQuery = "SELECT $aggregate($column) FROM $tableName";
        if(!empty($this->_where)) {
            $this->_selectQuery .=" WHERE ";
            $count_cond = count($this->_where);
            $this->_whereCols = array_keys($this->_where);
            for ($i = 0; $i < $count_cond; $i++) {
                $this->_selectQuery .= $this->_whereCols[$i].$this->_operator[$i]." :".$this->_whereCols[$i] . " AND ";
            }
            $this->_selectQuery = substr($this->_selectQuery, 0, strlen($this->_selectQuery) - 4);
        }
        return $this->_selectQuery;

    }



    /**
     * @param string $column The column name to get the maximum value from
     * @param string $tableName The name of the table to act upon
     * @return int|string The maximum value obtained
     */
    public function aggregate($column, $tableName, string $aggregate = "MAX") {
        $this->_conn = $this->connect();
        try {
            $stmt = $this->_conn->prepare($this->buildAggregate($column, $tableName, $aggregate));
            empty($this->_where)? $stmt->execute() : $stmt->execute($this->_where);
    
            $this->reset();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
            return $result[$aggregate.'('.$column.')'];
        }
        catch(PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }



    /**
     * Initialize the join table sequence
     *
     * @param string $table_name The parent table to select data from
     * @param string $column_names The columns to select
     * @return void
     */
    public function initJoin(string $columns = '*'){
        $this->action = 'join';

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $columns);
        $instance->join_query = "SELECT ". $columns . " FROM ".$this->objectTableName;
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * The table to join (INNER JOIN)
     *
     * @param string $table_name
     * @param string $parent_key The primary key for the parent table joined to
     * @param string $foreign_key The foreign key of this table
     * @return void
     * 
     * Call the method multiple times with different tables to join multiple.
     * 
     * The join Sequence should initialized first
     */
    public function join(string $table_name, string $parent_key, string $foreign_key){
        $this->join_query .= " INNER JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;
        
        return $instance;
    }



    /**
     * The table to join (LEFT JOIN)
     *
     * @param string $table_name
     * @param string $parent_key The primary key for the parent table joined to
     * @param string $foreign_key The foreign key of this table
     * @return void
     * 
     * Call the method multiple times with different tables to join multiple.
     * 
     * The join Sequence should initialized first
     */
    public function leftJoin(string $table_name, string $parent_key, string $foreign_key){
        $this->join_query .= " LEFT JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }

        /**
     * The table to join (RIGHT JOIN)
     *
     * @param string $table_name
     * @param string $parent_key The primary key for the parent table joined to
     * @param string $foreign_key The foreign key of this table
     * @return void
     * 
     * Call the method multiple times with different tables to join multiple.
     * 
     * The join Sequence should initialized first
     */
    public function rightJoin(string $table_name, string $parent_key, string $foreign_key){
        $this->join_query .= " RIGHT JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * Join tables without foreign keys
     *
     * The number of columns selected in each table should be equal
     * @param array $tables An associative array of table name and its columns
     * @param boolean $distinct Set it to true if you want distinct data. defaults to false
     * @return void
     * 
     * Call the get() method to get data returned
     */
    public function unionJoin(array $tables, bool $distinct = false){
        $table_names = array_keys($tables);
        $slect_q = array_values($tables);
        $union = $distinct ? " UNION ALL ": " UNION ";
        for ($i = 0; $i < count($table_names); $i++) {
            $this->join_query .= "SELECT " . $slect_q[$i] . " FROM " . $table_names[$i] . $union;
        }
        $trim = $union == " UNION ALL " ? 11 : 7;
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - $trim);

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * The WHERE clause in the join SQL
     *
     * @param array $where_data An associative array of column name and value
     * @param string $operator An operator to use, Defaults to =
     * @return void
     * 
     * The method formulate WHERE AND AND AND .....
     */
    public function joinWhere(array $where_data, string $operator = "="){
        $where_keys = array_keys($where_data);
        $where_len = count($where_keys);

        $operator = explode(',', $operator);
        if ($where_len > 1 and count($operator) == 1) {
            for ($i = 0; $i < $where_len; $i++) {
                $operator[] = "=";
            }
        }

        $this->join_query .= " WHERE ";
        for ($i = 0; $i < $where_len; $i++) {
            $this->join_query .= $where_keys[$i] . " $operator[$i] :".$where_keys[$i] . " AND ";
        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
        
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->_where = $where_data;
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }



    /**
     * The OR clause in the join SQL
     *
     * @param array $where_data An associative array of column name and value
     * @param string $operator An operator to use, Defaults to =
     * @return void
     * 
     * The method formulate OR .....
     * 
     * The whereAnd() method should be called first
     */
    public function whereOr(array $where_data, string $operator = "="){
        try {
            if (empty($this->_where)) {
                throw new Exception("The joinWhere() method should be called first with 
                one pair of key and value.", 0);
            }
        }catch(Exception $e) {
            echo $e->getMessage() . " thrown in ".$e->getFile() . " on line ".$e->getLine()
            . "<br/> Call stack: ". $e->getTraceAsString() . "<br/>";
        }

        $where_keys = array_keys($where_data);
        $where_len = count($where_keys);
        $operator = explode(',', $operator);

        if ($where_len > 1 and count($operator) == 1) {
            for ($i = 0; $i < $where_len; $i++) {
                $operator[] = "=";
            }
        }

        $this->join_query .= " OR ";
        for ($i = 0; $i < $where_len; $i++) {
            $this->join_query .= $where_keys[$i] . " $operator[$i] :".$where_keys[$i] . " OR ";
        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 3);
        $this->_whereOr = $where_data;

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;

        if (!$this->_is_slect)
            $this->_where = array_merge($this->_where, $where_data);
        
        $instance->action = $this->action;

        $instance->is_distinct = $this->is_distinct;

        $instance->_is_slect = true;

        $instance->_operator = $this->_operator;

        $instance->_where = $this->_where;
        $instance->_whereOr = $this->_whereOr;

        $instance->_is_select = $this->_is_slect;

        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * Order by clause
     *
     * @param string $order The colum(s) to order by
     * @param string $mode Defaults ASC
     * @return void
     */
    public function orderBy(string $order, string $mode = "ASC") {
        $this->join_query .= " ORDER BY ".$order . " ".$mode;

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->_where = $this->_where;
        $instance->action = $this->action;
        
        return $instance;
    }

    public function between(array $between_data, $columns = '*'){
        $this->action = 'between';

        $keys = array_keys($between_data);
        $values = array_values($between_data);
        $keys_len = count($keys);
        $this->join_query = " SELECT $columns FROM {$this->objectTableName} WHERE ";
        for ($i = 0; $i < $keys_len; $i++) {
            $this->join_query .= $keys[$i] . " BETWEEN ";
            for ($j = 0; $j < count($values[$i]); $j++) {
                $this->join_query .= " ? ". " AND ";
                array_push($this->_betweenData, $values[$i][$j]);
            }

        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");

        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_betweenData = $this->_betweenData;
        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * Initialize the LIKE operation in fetching data
     *
     * @param string $columns The columns to select
     * @param string $table The table name to select the data from
     * @return void
     * 
     * Should be followed by a call to the like() method
     */
    public function initLike(string $columns = '*') {
        $this->action = 'like';

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $columns);
        $instance->join_query = "SELECT ". $columns . " FROM ".$this->objectTableName . " WHERE ";
        $instance->action = $this->action;
        $instance->_database = $this->_database;

        return $instance;
    }

    /**
     * Complete the LIKE operation procedure
     *
     * @param array $like_data An associative array of column and value. The value 
     * should include a wild cat, eg 
     * ['column_name' => '%value']
     * 
     * Multiple pairs of key and value will be combined using the AND operator
     * If you want to use the OR operator, call the likeOr() next
     * @return void
     * 
     * Call the get() method to ge the returned data
     */
    public function like(array $like_data){
        $keys = array_keys($like_data);
        $keys_len = count($keys);
        for ($i = 0; $i < $keys_len; $i++){
            $this->join_query .= $keys[$i]. " LIKE :".$keys[$i]. " AND ";
        }
        $this->_likeData = $like_data;
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;
        $instance->_likeData = $this->_likeData;
        
        return $instance;

    }

    /**
     * The OR operator construct
     * Forms LIKE ... OR .. LIKE ... OR
     * @param array $like_data An associate array of column name and value
     * @return void
     * 
     * The like() method should be called fist with at least one pair of key and value
     */
    public function likeOr(array $like_data){
        $keys = array_keys($like_data);
        $keys_len = count($keys);
        $this->join_query .= " OR ";
        for ($i = 0; $i < $keys_len; $i++){
            $this->join_query .= $keys[$i]. " LIKE :".$keys[$i]. " OR ";
        }

        foreach ($like_data as $key => $value) {
            $this->_likeData[$key] = $value;
        }
        
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 3);

        $tableName = preg_replace ("/[^-a-z0-9_]+/i",'', $this->objectTableName);
        if (!class_exists ($tableName))
            eval ("
            System\Database\Database;
            class $tableName extends Database {}
            ");
        $instance = new $tableName ();
        $instance->__init__($this->objectTableName, $this->objectTableColumns);
        $instance->join_query = $this->join_query;
        $instance->action = $this->action;
        $instance->_database = $this->_database;
        $instance->_likeData = $this->_likeData;

        return $instance;
        
    }

    /**
     *  A helper method to return the data resulting from the execution of
     * 
     * Joins
     * 
     * between
     * 
     * like
     *
     * @return array An associative array of data.
     */
    public function get(){
        $data = array();

        switch ($this->action)
        {
            case 'one':

                $data = $this->getOneRow($this->objectTableColumns, $this->objectTableName);
                break;

            case 'many':
                $data = $this->getAll(
                    $this->objectTableColumns, 
                    $this->objectTableName, 
                    null, $this->is_distinct
                );
                break;
                
            case 'join' or 'like' or 'between':
                $data = $this->executeJoinOrLike();
                break;
        }

        return $data;
    }



    private function executeJoinOrLike() {
        $this->_conn = $this->connect();

        try {
                
            $stmt = $this->_conn->prepare($this->join_query);
            
            if (!empty($this->_betweenData)){
                
                $i = 0;
                array_unshift($this->_betweenData, 0);
                $data_count = count($this->_betweenData);
                while ($i < $data_count -1) {
                    $i++;
                    $stmt->bindParam($i, $this->_betweenData[$i]);
                }

                $stmt->execute();

            } elseif (!empty($this->_likeData)){

                $stmt->execute($this->_likeData);
            }
            else {
                
                empty($this->_where) ? $stmt->execute() : $stmt->execute($this->_where);
            }
            $this->reset();
            return $stmt->fetchAll();
        }
        catch(PDOException $error) {
            echo $error->getMessage();
            return;
        }
    }

    public function __destruct()
    {
        $this->_conn = null;
    }
}
//End of Database
