<?php
namespace PHASER\Database;

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

    private   $_where = array();

    private   $_whereCols = array();

    private   $_orderBy = "";

    private   $_operator = "";

    private   $_rows = array();

    private   $_updateData = array();

    private   $_updateQueryData = array();

    private   $_whereOr = array();

    private $lastId = array();

    private $_database;

    private $_errorInfo = array();

    private $join_query = "";

    private $_is_slect = false;

    private $_betweenData = array();

    private $_likeData = array();

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
    public function setDatabase($database_name) {
        $this->_database = $database_name;
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
        return $this;
    }

    /**
     * Cancels the transaction and goes back to the initial state
     * @return bool
     */
    public function rollBack() {
        return $this->_conn->rollBack();
    }
    /**
     * @param string $tableName The name of the table to insert the data to.
     * @param array $tableData An associative array containing column names and their data to insert in the table.
     *
     * @return array|false ::rowCount|int Returns number of affected rows
     * @throws PDOException
     */
    public  function insert($tableName, array $tableData) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        if(!is_array($tableData)) {
            return false;
        }
            $stmt = $this->_conn->prepare($this->buildInsert($tableName, $tableData));
            $stmt->execute($tableData);
            if ($stmt->rowCount() > 0){
             $this->_rows = array_merge($this->_rows, array("Insert" => $stmt->rowCount()));
            }
            else
            array_push($this->_errorInfo, $stmt->errorInfo());
            $this->_conn->commit();
            $this->lastId[0] = $this->_conn->lastInsertId();
            $this->reset();
        return $this->_rows;

    }

    public function errorInfor() {
        $all_errors = "";
        if (!empty($this->_errorInfo)) {
            foreach ($this->_errorInfo as $error) {
                $all_errors .= $error[0][2]."<br/>";
            }
        }
        return $all_errors;
    }

    /**
     * The ID of the last Inserted row.
     * @return mixed
     */
    public function lastInsertId() {
        return $this->lastId[0];
    }

    /**
     * @param string $columns A string of columns to return separated by commas
     * @param string $tableName A string containing the table name to select data from.
     * @param int|null $limit An optional integer of number of rows to select from the table.
     * @return array     An associative array of returned rows
     */
    public function getAll($columns, $tableName, $limit = null) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        try {
            if (!is_string($columns)):
                throw new Exception("Expected a string of columns separated by commas");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($columns, $tableName, $limit));
        !empty($this->_whereOr) ? $this->_where = array_merge($this->_where, $this->_where):$this->_where;
        empty($this->_where) ? $stmt->execute() :  $stmt->execute($this->_where);
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $data = $stmt->fetchAll();
        $this->disconnect();
        return $data;
    }
     /**
     * @param string $columns A string of columns to return separated by commas
     * @param string $tableName A string containing the table name to select data from.
     * @param int $limit An optional integer of number of rows to select from the table.
     * @return array     An array of the returned rows
     */
    public function getOneRow($columns, $tableName, $limit = 1) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
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
        !empty($this->_whereOr) ? $this->_where = array_merge($this->_where, $this->_where):$this->_where;
        empty($this->_where) ? $stmt->execute() :  $stmt->execute($this->_where);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->_conn->commit();
        $this->reset();
        $data = $stmt->fetch();
        $this->disconnect();
        return $data;
    }
    /**
     * Helper function to close the database connection
     * @return null
     */
    private function disconnect() {
        return $this->_conn = null;
    }
    /**
     * @param array $wherePos  An associative array with the where column and the condition in the first index (0). The Optional second index (1) takes the AND clause with the column and the condition. The Optional third index takes the OR with column name and the condition.
     * @param string $operator Takes the operator to be used. Default is =;
     * @return array           An associative array of column and condition for WHERE, AND , OR
     */
    public function where(array $wherePos, $operator = '=') {
        $this->_is_slect = true;
        $this->_where = $wherePos;
        $this->_operator = explode(',', $operator);
        $_len = count($this->_where);
        if ($_len > 1) {
            $choices = ['<', '>', '<=', '>=', '!='];
            if (!in_array($choices, $this->_operator)) {
                $this->_operator = array();
                for ($i = 0; $i < $_len; $i++) {
                    array_push($this->_operator, '=');
                }
            } 
        }
        return $this->_where;
    }

    /**
     * The where() and or and() methods should be called first
     * @param string $orderColumns A string of column name(s) separated by commas if many to base on for ordering.
     * @param string $order A string representing the type of order for the rows. Default is ASC.
     * @return string               An order by string to complete the select query
     */
    public function orderBy($orderColumns, $order = "ASC") {
        $this->_orderBy = $orderColumns." ".$order;
        return $this->_orderBy;
    }

    /**
     * @param string $column A string containing the column name to select
     * @param string $tableName A string containing the table name to select data from.
     * Call The where method if you have a condition to base on in selection of data
     * @param int $limit A string containing the limit of data to select
     * @return string A string of data for a single column
     * The where() and or and() methods should be called first if a condition is requiredS
     */
    public function getOne($column, $tableName, $limit = 1) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        try {
            if (empty($this->_where)):
                throw new Exception("Expected a condition to be passed to the where method first:: 0 given");
            endif;
        } catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($column, $tableName, $limit));
        !empty($this->_whereOr) ? $this->_where = array_merge($this->_where, $this->_where):$this->_where;
        $stmt->execute($this->_where);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->disconnect();
        $this->reset();
        return !empty($result) ? $result[$column] : "";
    }
     /**
     * @param string $column A string of columns to return separated by commas
     * @param string $tableName A string containing the table name to select data from.
     * @param int $limit An optional integer of number of rows to select from the table.
     * @return object     An Object with property and value representation of the returned row
     * The where() and or and() methods should be called first if a condition is required
     */
    public function getObject($column, $tableName, $limit = 1) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        try {
            if (empty($this->_where)):
                throw new Exception("Expected a condition to be passed to the where method first:: 0 given");
            endif;
        }catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $stmt = $this->_conn->prepare($this->buildSelect($column, $tableName, $limit));
        !empty($this->_whereOr) ? $this->_where = array_merge($this->_where, $this->_where):$this->_where;
        $stmt->execute($this->_where);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $data = $stmt->fetch();
        $this->disconnect();
        return $data;
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
    private function buildSelect($columns, $tableName, $limit = null){
        $this->_selectQuery = "SELECT ".$columns." FROM ".$tableName;
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
                    $this->_selectQuery .= $where_keys[$i] . " $this->_operator[$i] :".$where_keys[$i] . " OR ";
                }
                $this->_selectQuery = substr($this->_selectQuery, 0, strlen($this->_selectQuery) - 3);
            }
        }
        !empty($this->_orderBy)?$this->_selectQuery .=" ORDER BY ".$this->_orderBy : $this->_selectQuery;  
        $limit !== null ? $this->_selectQuery .=" LIMIT ".$limit : $this->_selectQuery;
        return $this->_selectQuery;
    }
    /**
     * Method that returns the number of affected rows
     * @return int   Number of the affected rows
     */
    public function affectedRows() {
        $affectedRows = 0;
        if(isset($this->_rows['Insert'])):
            $affectedRows = $this->_rows['Insert'];
        endif;
        if(isset($this->_rows['Delete'])):
            $affectedRows = $this->_rows['Delete'];
        endif;
        if(isset($this->_rows['Update'])):
            $affectedRows = $this->_rows['Update'];
        endif;            
        return $affectedRows;
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
     * @return array Number of affected rows, Can be accessed by calling the affectedRows method.
     * The Where() the method should be called first
     * 
     */
    public function update($tableName, array $updateData) {
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
        $stmt->execute($this->_updateQueryData);
        $stmt->rowCount() > 0 ? $this->_rows = array_merge($this->_rows, array("Update" => $stmt->rowCount())) : null;
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $rows = $this->_rows;
        $this->disconnect();
        return $rows;
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
    public function insertMany($tableName, array $tableData) {
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
        foreach($tableData as $row) {
            $stmt->execute($row);
        }
        $stmt->rowCount() > 0 ? $this->_rows = array_merge($this->_rows, array("Insert" => $stmt->rowCount())) : null;
        $this->_conn->commit();
        $this->lastId[0] = $this->_conn->lastInsertId();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $rows = $this->_rows;
        $this->disconnect();
        return $rows;
    }

    /**
     * @param string $tableName A string containing the name of the table to delete a record from
     * @param string $operator An optional string containing the operator to use in the where clause, Default is "="
     * @return array ::rowCount    An array of affected rows, accessed by calling the affectedRows method.
     *The where method should be called first, and the parsed array should have not more than 2 indexes
     */
    public function delete($tableName, $operator = "=") {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildDelete($tableName, $operator));
        $stmt->execute($this->_where) ? $this->_rows = array_merge($this->_rows, array("Delete" => $stmt->rowCount())) : null;
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $rows = $this->_rows;
        $this->disconnect();
        return $rows;
    }

    /**
     * Abstract function to build the delete query
     * @param string $tableName
     * @param string $operator
     * @return string
     * The where method should be called first
     */
    private function buildDelete($tableName, $operator = "="){
        $this->_deleteQuery = "DELETE FROM ".$tableName;
        $this->_operator = explode(',', $operator);
        try {
            if (empty($this->_where)):
                throw new Exception("Expected a condition to be parsed to where method first");
            endif;
        }catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
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
     * @param string $tableName A string containing the name of the table to delete rows from.
     * @return bool True if all data is erased and false otherwise
     * Be Sure of the consequences resulting from calling this method.
     * All data is erased
     */
    public function deleteAll($tableName){
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $this->_deleteQuery = "TRUNCATE table ".$tableName;
        $stmt = $this->_conn->prepare($this->_deleteQuery);
        $stmt->execute();
        $stmt->rowCount() > 0 ? $this->_rows = array_merge($this->_rows, array("delete" => $stmt->rowCount())) : null;
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        if ($stmt->rowCount() > 0) {
            $this->disconnect();
            return true;
        }else
            $this->disconnect();
            return false;
       
    }

    /**
     * @param string $tableName A string containing the name of the table to act on
     * @param array $tableData An array containing a status name and the value to change. Use logical True(1) and False(0) as values to mark trashed rows. The same method can be used to un trash a row.
     * @return array ::rowCount     An array containing the number of affected rows.
     * The where method should be called first
     */
    public function trash($tableName, array $tableData){
        try {
            if (empty($this->_where)):
                throw new Exception("Expected the condition to be parsed first in the where method");
            endif;
        }catch (Exception $exception) {
            echo $exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine();
        }
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildUpdate($tableName, $tableData));
        $stmt->execute($this->_updateQueryData);
        $stmt->rowCount() > 0 ? $this->_rows = array_merge($this->_rows, array("Update" => $stmt->rowCount())) : null;
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $row = $this->_rows;
        $this->disconnect();
        return $row;
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
            $this->_conn->beginTransaction();
            $stmt = $this->_conn->prepare($query);
            array_unshift($data, true);
            $i = 0;
            $data_count = count($data);
            while ($i < $data_count - 1) {
                $i++;
                $stmt->bindParam($i, $data[$i]);
            }
            $stmt->execute();
            $this->_conn->commit();
            if (strtolower($fetchMode) == "assoc") {
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $data = $stmt->fetch();
            }else{
                $data = $stmt->fetchAll();
            }
            array_push($this->_errorInfo, $stmt->errorInfo());
            $this->disconnect();
            return $data;
        } else {
            return $this->_update($query, $data);
        }
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
        $stmt->execute();
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $rows = $stmt->rowCount();
        $rows > 0 ? $this->_rows = array_merge($this->_rows, array("Update" => $rows)) : 0;
        $this->disconnect();
        return $rows;
    }

    private function buildCount($column, $tableName) {
        $this->_selectQuery = "SELECT count($column) FROM ".$tableName;
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

    private function buildSum($column, $tableName) {
        $this->_selectQuery = "SELECT SUM($column) FROM ".$tableName;
        if(!empty($this->_where)):
            foreach($this->_where as $key => $value) {
                $this->_whereCols[] = $key;
            }
            $this->_selectQuery .=" WHERE ".$this->_whereCols[0]." = :".$this->_whereCols[0];
            if(count($this->_where) == 2):
                $this->_selectQuery .=" AND ".$this->_whereCols[1]." = :".$this->_whereCols[1];
            endif; 
            if(count($this->_where) == 3):
                $this->_selectQuery .=" OR ".$this->_whereCols[2]." = :".$this->_whereCols[2];
            endif;    
        endif;
        return $this->_selectQuery;
    }

     /**
     * @param string $column A column name from where the count will happen
     * @param string $tableName A table name to count from
     * @return int   Number of count returned
     */
    public function countRows($column, $tableName){
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildCount($column, $tableName));
        empty($this->_where) ? $stmt->execute(): $stmt->execute($this->_where);
        $this->_conn->commit();
        $this->reset();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $data = $result['count('.$column.')'];
        $this->disconnect();
        return $data;
    }

    /**
     * @abstract Function to built counting of data
     * @param string $column
     * @param string $tableName
     * @return string Query to executed
     */
    private function buildMax($column, $tableName) {
                $this->_selectQuery = "SELECT MAX($column) FROM $tableName";
        if(!empty($this->_where)):
            foreach($this->_where as $key => $value) {
                $this->_whereCols[] = $key;
            }
            $this->_selectQuery .=" WHERE ".$this->_whereCols[0].$this->_operator." :".$this->_whereCols[0];
            if(count($this->_where) == 2):
                $this->_selectQuery .=" AND ".$this->_whereCols[1].$this->_operator." :".$this->_whereCols[1];
            endif; 
            if(count($this->_where) == 3):
                $this->_selectQuery .=" OR ".$this->_whereCols[2].$this->_operator." :".$this->_whereCols[2];
            endif;    
        endif;
        return $this->_selectQuery;

    }

    /**
     * @abstract Function to built counting of data
     * @param string $column
     * @param string $tableName
     * @return string Query to executed
     */
    private function buildMin($column, $tableName) {
                $this->_selectQuery = "SELECT MIN($column) FROM $tableName";
        if(!empty($this->_where)):
            foreach($this->_where as $key => $value) {
                $this->_whereCols[] = $key;
            }
            $this->_selectQuery .=" WHERE ".$this->_whereCols[0].$this->_operator." :".$this->_whereCols[0];
            if(count($this->_where) == 2):
                $this->_selectQuery .=" AND ".$this->_whereCols[1].$this->_operator." :".$this->_whereCols[1];
            endif; 
            if(count($this->_where) == 3):
                $this->_selectQuery .=" OR ".$this->_whereCols[2].$this->_operator." :".$this->_whereCols[2];
            endif;    
        endif;
        return $this->_selectQuery;

    }

    /**
     * @param string $column The column name to get the maximum value from
     * @param string $tableName The name of the table to act upon
     * @return int|string The maximum value obtained
     */
    public function getMax($column, $tableName) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildMax($column, $tableName));
        empty($this->_where)? $stmt->execute() : $stmt->execute($this->_where);
        $this->_conn->commit();
        $this->reset();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $data = $result['MAX('.$column.')'];
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->disconnect();
        return $data;
    }

    /**
     * @param string $column The column name to get the minimum value from
     * @param string $tableName The name of the table to act upon
     * @return int|string The minimum value obtained
     */
    public function getMin($column,  $tableName) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildMin($column, $tableName));
        empty($this->_where)? $stmt->execute() : $stmt->execute($this->_where);
        $this->_conn->commit();
        $this->reset();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $data = $result['MIN('.$column.')'];
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->disconnect();
        return $data;
    }

      /**
     * @param string $column The column name to sum up the value from
     * @param string $tableName The name of the table to act upon
     * @return int|string The sum of value obtained
     */
    public function getTotal( $column, $tableName) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildSum($column, $tableName));
        empty($this->_where)? $stmt->execute() : $stmt->execute($this->_where);
        $this->_conn->commit();
        $this->reset();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $data = $result['SUM('.$column.')'];
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->disconnect();
        return $data;
    }

    /**
     * Initialize the join table sequence
     *
     * @param string $table_name The parent table to select data from
     * @param string $column_names The columns to select
     * @return void
     */
    public function initJoin(string $table_name, string $column_names){
        $this->join_query = "SELECT ". $column_names . " FROM ".$table_name;
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
    public function whereAnd(array $where_data, string $operator = "="){
        $where_keys = array_keys($where_data);
        $where_len = count($where_keys);
        $this->join_query .= " WHERE ";
        for ($i = 0; $i < $where_len; $i++) {
            $this->join_query .= $where_keys[$i] . " $operator :".$where_keys[$i] . " AND ";
        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
        $this->_where = $where_data;
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
                throw new Exception("The whereAnd() method should be called first with 
                one pair of key and value.", 0);
            }
        }catch(Exception $e) {
            echo $e->getMessage() . " thrown in ".$e->getFile() . " on line ".$e->getLine()
            . "<br/> Call stack: ". $e->getTraceAsString() . "<br/>";
        }
        $where_keys = array_keys($where_data);
        $where_len = count($where_keys);
        $this->join_query .= " OR ";
        for ($i = 0; $i < $where_len; $i++) {
            $this->join_query .= $where_keys[$i] . " $operator :".$where_keys[$i] . " OR ";
        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 3);
        $this->_whereOr = $where_data;
        if (!$this->_is_slect){
            empty($this->_where) ? $this->_where = $where_data : 
            $this->_where = array_merge($this->_where, $where_data);
        }
    }

    /**
     * Order by clause
     *
     * @param string $order The colum(s) to order by
     * @param string $mode Defaults ASC
     * @return void
     */
    public function orderJoinsBy(string $order, string $mode = "ASC") {
        $this->join_query .= " ORDER BY ".$order . " ".$mode;
    }

    public function between(string $columns, string $table, array $between_data){
        $keys = array_keys($between_data);
        $values = array_values($between_data);
        $keys_len = count($keys);
        $this->join_query = " SELECT $columns FROM $table WHERE ";
        for ($i = 0; $i < $keys_len; $i++) {
            $this->join_query .= $keys[$i] . " BETWEEN ";
            for ($j = 0; $j < count($values[$i]); $j++) {
                $this->join_query .= " ? ". " AND ";
                array_push($this->_betweenData, $values[$i][$j]);
            }

        }
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
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
    public function initLike(string $columns, string $table) {
        $this->join_query = "SELECT " . $columns . " FROM ". $table . " WHERE ";
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
        echo $this->join_query;
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
        $this->_likeData = $like_data;
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 3);
    }

    /**
     *  A helper method to return the data resulting from the execution of
     * Joins, between, and like methods
     *
     * @return array An associative array of data.
     */
    public function get(string $mode = 'PDO::FETCH_BOTH'){
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
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
        $this->_conn->commit();
        array_push($this->_errorInfo, $stmt->errorInfo());
        $this->reset();
        $data = $stmt->fetchAll();
        $this->disconnect();
        return $data;
    }
}
//End of Database
