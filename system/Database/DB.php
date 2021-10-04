<?php
namespace System\Database;
require_once APP_PATH.'config/database.php';

use Exception;
use PDO;
use PDOException;

$config = (object) $config;

define("HOST", $config->DB_HOST);

define("USERNAME", $config->DB_USER);

define("PASSWORD", $config->DB_PASSWORD);

define("DB", $config->DB_NAME);

define("TIMEZONE", env("TIMEZONE"));
  
/**
 * @category  Database Access
 * @package   Database
 * @author Tumuhimbise Godwin
 * @copyright Copyright (c) 2020-2021
 * @version   2.0
 */
class DB extends FluentApi implements PhaserDatabase
{
    private $_conn;

    private  $_insertQuery = "";

    private   $_updateQuery = "";

    private  $_deleteQuery = "";

    private   $_selectQuery = "";

    private  $_cols = array();

    private   $named_keys = array();

    protected   $_where = array();

    private   $_whereCols = array();

    private   $_orderBy = "";

    protected   $_operator = "";

    private   $_updateData = array();

    private   $_updateQueryData = array();

    protected   $_whereOr = array();

    private $_database = '';

    protected $join_query = "";

    protected $_is_slect = false;

    private $_betweenData = array();

    protected $_likeData = array();

    protected $objectTableName = '';

    protected $objectTableData = array();

    protected $objectTableColumns = '';


    protected $action = '';
    protected $is_one_value = false;
    protected $is_distinct = false;
    protected $is_one_row = false;
    protected $_selectQueryData = array();
    protected $fetchMode = 4;

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
            $this->logError($exception->getMessage() . " in ".$exception->getFile() . " on ".$exception->getLine());
        }
    }


    public function use($database_name) 
    {
        $this->_database = $database_name;
        
        return $this;
    }

    private function connect() 
    {
        $dsn = "mysql:host=".HOST.";dbname=".$this->_database;
        $conn = (object) "";
        try {
            $conn = new PDO($dsn, USERNAME, PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $PDOException) {
            echo $PDOException->getMessage();
        }
        return $conn;
    }
        /**
     * Reset states after an execution
     *
     * @return DB Returns the current this.
     */
    private function reset()
    {
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
        $this->is_one_row = false;
        $this->_selectQueryData = array();
        $this->fetchMode = 4;
        $this->objectTableColumns = '';
        $this->objectTableName = '';
        $this->objectTableData = array();
        return $this;
    }

    private function logError($error)
    {
        $root = $_SERVER['DOCUMENT_ROOT'];

        $f = fopen($root.'/database/logs/db-logs.txt', 'a+');
        $error = "[" . date("D d M Y H:i:s") . "] \t" . $error;
        $error .= "\n\n ----------------------------------------------------------------------- \n\n";
        fwrite($f, $error);
        fclose($f);
    }

    public static function showDBErrors()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];

        $f = fopen($root.'/database/logs/db-logs.txt', 'r');

        $content = fread($f, filesize($root.'/database/logs/db-logs.txt'));
        d($content);
    }


    public static function table($tableName, array $tableData = [])
    {
        return (new self)->_table($tableName, $tableData);
    }

    private function _table($tableName, array $tableData = [])
    {
        $this->objectTableData = $tableData;
        $this->objectTableColumns = '*';
        $this->objectTableName = $tableName;
        $this->action = 'many';
        return $this;
    }


    private  function insert($tableName, array $tableData)
    {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        if(!is_array($tableData)) {
            return false;
        }
        $stmt = $this->_conn->prepare($this->buildInsert($tableName, $tableData));
        try{
            $stmt->execute($tableData);
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        } catch (PDOException $error) {
            $this->_conn->rollBack();
            $this->logError($error->getMessage());
        }
    }

    public function save()
    {
        if (!empty($this->_where))
        {
            return $this->update($this->objectTableName, $this->objectTableData) > 0;
        }

        if (empty($this->_where) and gettype(reset($this->objectTableData)) !== 'array')
        {

            return $this->insert($this->objectTableName, $this->objectTableData) > 0;
        }
        
        if(gettype(reset($this->objectTableData)) === "array")
        {

            return $this->insertMany($this->objectTableName, $this->objectTableData) > 0;
        }
        
    }


    public function _row(string $columns = '*')
    {
        $this->action = 'one';
        $this->is_one_row = true;
        $this->objectTableColumns = $columns;
        return $this;
    }



    public function _select(string $columns)
    {
        $this->model_class();
        $this->objectTableColumns = $columns;
        $this->action = 'many';
        return $this;
    }



    public function _distinct(string $columns)
    {
        $this->model_class();
        $this->is_distinct = true;
        $this->objectTableColumns = $columns;
        return $this;
    }



    public function value(string $column)
    {
        return $this->getOne($column, $this->objectTableName);
    }



    public function _count(string $column = '*')
    {
        $this->model_class();
        if (!empty($this->_where)) {
            return $this->countRows($column, $this->objectTableName);
        }
        $column = $this->is_distinct ? $this->objectTableColumns : $column;
        return count($this->getAll($column, $this->objectTableName, null, $this->is_distinct));
    }



    public function max(string $column)
    {
        $column = $this->is_distinct ? $this->objectTableColumns : $column;
        return $this->aggregate($column, $this->objectTableName);
    }



    public function min(string $column)
    {
        $column = $this->is_distinct ? $this->objectTableColumns : $column;
        return $this->aggregate($column, $this->objectTableName, "MIN");
    }



    public function avg(string $column)
    {   
        $column = $this->is_distinct ? $this->objectTableColumns : $column;
        return $this->aggregate($column, $this->objectTableName, "AVG");
    }



    public function sum(string $column)
    {   
        $column = $this->is_distinct ? $this->objectTableColumns : $column;
        return $this->aggregate($column, $this->objectTableName, "SUM");
    }



    public function exists() 
    {
        $data = $this->getOneRow($this->objectTableColumns, 
        $this->objectTableName);
        return !empty($data);
    }

    public function doesNotExists() 
    {
        $data = $this->getOneRow($this->objectTableColumns, 
        $this->objectTableName);
        return empty($data);
    }



    private function getAll($columns, $tableName, $limit = null, $is_distinct = false)
    {
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
            $this->logError($error->getMessage());
        }
    }


    private function getOneRow($columns, $tableName, $limit = 1)
    {
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
            $this->logError($error->getMessage());
        }
    }

    protected function model_class(){
        
        if (empty(trim($this->objectTableName)))
        {
            $this->action = $this->is_one_row ? 'one' : 'many';
            $class = get_called_class();
            $table_name = explode("\\", strtolower($class));
            $this->objectTableName = $table_name[(count($table_name) - 1)];
            $this->objectTableColumns = '*';
        }
    }

    public function _all()
    {
        $this->model_class();
        return $this->get();
    }

    protected function desc(string $table)
    {

        $this->_conn = $this->connect();
        $stmt = $this->_conn->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = array();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $stmt->fetch()){
            $columns[] = $row['Field'];
        }

        return $columns;
    }
  

    public function _where(array $wherePos, $operator = '=')
    {
        $this->model_class();
        $this->_is_slect = true;
        $this->_where = $wherePos;
        $this->_operator = explode(',', $operator);
        $_len = count($this->_where);
        if ($_len > 1 and count($this->_operator) == 1) {
            for ($i = 0; $i < $_len; $i++) {
                array_push($this->_operator, '=');
            } 
        }

        if ($this->action == 'join') {
            $this->_is_slect = false;
            $where_keys = array_keys($wherePos);
            $where_len = count($where_keys);
    
            $this->join_query .= " WHERE ";
            for ($i = 0; $i < $where_len; $i++) {
                $this->join_query .= $where_keys[$i] . " ". $this->_operator[$i]." :".$where_keys[$i] . " AND ";
            }
            $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
        }

        return $this;
    }

    private function getOne($column, $tableName, $limit = 1)
    {
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
            $this->logError($error->getMessage());
        }
    }


    private function buildInsert($tableName, array $tableData)
    {
        $this->_cols = array_keys($tableData);
        $this->_insertQuery = "INSERT INTO $tableName (".implode(',',$this->_cols).") VALUES (";
        $this->_insertQuery .= ":".implode(', :', $this->_cols);
        $this->_insertQuery .=")"; 
        return $this->_insertQuery;    
    }


    private function buildSelect($columns, $tableName, $limit = null, $is_distinct = false)
    {

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


    private function buildUpdate($tableName, array $updateData)
    {
        $this->_updateData = array_merge($this->_updateData, $updateData);  
        $this->_cols = array_keys($this->_updateData);
        $sql = "UPDATE $tableName SET ";
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


    private function update($tableName, array $updateData) {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildUpdate($tableName, $updateData));
        
        try {
            $stmt->execute($this->_updateQueryData);
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        }catch(PDOException $error) {
            $this->logError($error->getMessage());
        }

    }


    private function buildInsertMany($tableName, array $tableData)
    {
        $this->_cols = array_keys($tableData);

        $this->_insertQuery = "INSERT INTO $tableName (".implode(', ',$this->_cols).") VALUES (";

        $this->_insertQuery .= ":".implode(', :', $this->_cols);
        $this->_insertQuery .=")";
        return $this->_insertQuery;  
    }


    private function insertMany($tableName, array $tableData)
    {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildInsertMany($tableName, $tableData));

        try {
            foreach($tableData as $row) {
                $stmt->execute($row);
            }
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount();
        } catch (PDOException $error) {
            $this->logError($error->getMessage());
        }
    }


    public function delete() {
        $this->_conn = $this->connect();
        $this->_conn->beginTransaction();
        $stmt = $this->_conn->prepare($this->buildDelete($this->objectTableName));
        
        try {
            $stmt->execute($this->_where);
            $this->_conn->commit();
            $this->reset();
            return $stmt->rowCount() > 0;
        } catch(PDOException $error) {
            $this->logError($error->getMessage());
        }

    }


    private function buildDelete($tableName)
    {
        $this->_deleteQuery = "DELETE FROM ".$tableName;
        if (empty($this->_where)) {
            $this->_deleteQuery = "TRUNCATE table ".$tableName;
        }
        if(!empty($this->_where)) {
            $where_keys = array_keys($this->_where);
            $where_len = count($this->_where);
            $this->_deleteQuery .= " WHERE ";
            for ($i = 0; $i < $where_len; $i++) {
                $this->_deleteQuery .= $where_keys[$i] . " = ". " :".$where_keys[$i] . " AND ";
            }
    
            $sql_len = strlen($this->_deleteQuery);
            $this->_deleteQuery = substr($this->_deleteQuery, 0, $sql_len - 4);
        }
        return $this->_deleteQuery;    
    }



    public function trash()
    {
        return $this->update($this->objectTableName, ['deleted_at' => date('Y-m-d H:i:s')]) > 0;
    }



    public function restore()
    {
        return $this->update($this->objectTableName, ['deleted_at' => NULL]) > 0;  
    }

    private function exec_row_query()
    {
        if (strpos($this->_selectQuery, "SELECT") !== false)
        {

            $this->_conn = $this->connect();
            $stmt = $this->_conn->prepare($this->_selectQuery);
            array_unshift($this->_selectQueryData, true);
            $i = 0;
            
            $data_count = count($this->_selectQueryData);
            while ($i < $data_count - 1) {
                $i++;
                $stmt->bindParam($i, $this->_selectQueryData[$i]);
            }

            try {
                $stmt->execute();
                $data = $stmt->fetchAll();

                if (strtolower($this->fetchMode) == "assoc") {
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $data = $stmt->fetch();
                }
                return $data;
            }catch(PDOException $error) {
                $this->logError($error->getMessage());
            }

        }

        return $this->_update($this->_selectQuery, $this->_selectQueryData) > 0;
    }


    public function _query($query, array $data = [], $fetchMode = PDO::FETCH_BOTH)
    {
        $this->_selectQuery = $query;
        $this->_selectQueryData = $data;
        $this->fetchMode = $fetchMode;
        $this->action = 'query';
        return $this;
    }



    private function _update($query, array $data)
    {
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
            $this->logError($error->getMessage());
        }

    }



    private function buildCount($column, $tableName)
    {
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



    private function countRows($column, $tableName)
    {
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
            $this->logError($error->getMessage());
        }
    }


    private function buildAggregate($column, $tableName, $aggregate = "MAX")
    {
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
            $this->logError($error->getMessage());
        }
    }



    public function initJoin(string $columns = '*')
    {
        $this->action = 'join';
        $this->objectTableColumns = $columns;
        $this->join_query = "SELECT ". $columns . " FROM ".$this->objectTableName;
        return $this;
    }

    public function join(string $table_name, string $parent_key, string $foreign_key)
    {
        $this->join_query .= " INNER JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        return $this;
    }


    public function leftJoin(string $table_name, string $parent_key, string $foreign_key)
    {
        $this->join_query .= " LEFT JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        return $this;
    }


    public function rightJoin(string $table_name, string $parent_key, string $foreign_key)
    {
        $this->join_query .= " RIGHT JOIN " . $table_name . " ON " . $parent_key . " = ".$foreign_key;
        return $this;
    }


    public function unionJoin(array $tables, bool $distinct = false)
    {
        $table_names = array_keys($tables);
        $slect_q = array_values($tables);
        $union = $distinct ? " UNION ALL ": " UNION ";
        for ($i = 0; $i < count($table_names); $i++) {
            $this->join_query .= "SELECT " . $slect_q[$i] . " FROM " . $table_names[$i] . $union;
        }
        $trim = $union == " UNION ALL " ? 11 : 7;
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - $trim);
        return $this;
    }


    public function whereOr(array $where_data, string $operator = "=")
    {
        try {
            if (empty($this->_where)) {
                throw new Exception("The where() method should be called first with 
                one pair of key and value.", 0);
            }
        }catch(Exception $e) {
            echo $e->getMessage();
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

        if (!$this->_is_slect)
            $this->_where = array_merge($this->_where, $where_data);
        return $this;
    }


    public function orderBy(string $column, string $mode = "ASC")
    {
        $this->_is_slect ? $this->_orderBy = " ORDER BY ".$column . " ".$mode:
        $this->join_query .= " ORDER BY ".$column . " ".$mode;
        
        return $this;
    }

    public function between(array $between_data, $columns = '*')
    {
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

        $this->objectTableColumns = $columns;


        return $this;
    }


    public function initLike(string $columns = '*')
    {
        $this->action = 'like';
        $this->objectTableColumns = $columns;
        $this->join_query = "SELECT ". $columns . " FROM ".$this->objectTableName . " WHERE ";

        return $this;
    }


    public function like(array $like_data)
    {
        $keys = array_keys($like_data);
        $keys_len = count($keys);
        for ($i = 0; $i < $keys_len; $i++){
            $this->join_query .= $keys[$i]. " LIKE :".$keys[$i]. " AND ";
        }
        $this->_likeData = $like_data;
        $this->join_query = substr($this->join_query, 0, strlen($this->join_query) - 4);
        
        return $this;

    }

    
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

        return $this;
        
    }

    
    public function get($callback = NULL){
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
                
            case 'join':
                $data = $this->executeJoinOrLike();
                break;

            case 'like':
                $data = $this->executeJoinOrLike();
                break;

            case 'between':
                $data = $this->executeJoinOrLike();
                break;
            
            case 'query':
                $data = $this->exec_row_query();
                break;
        }

        if (!is_callable($callback) && !empty(trim($callback))) {
            $data = array_to_object($data);
            return  $callback($data);
        }

        if (is_callable($callback)) {
            $data = array_to_object($data);
            return call_user_func_array($callback, [$data]);
        }
        return array_to_object($data);
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
            $this->logError($error->getMessage());
        }
    }

    public function __destruct()
    {
        $this->_conn = $this->connect();
        $this->_conn = null;
    }
}
//End of Database
