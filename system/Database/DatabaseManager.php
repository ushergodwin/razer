<?php
namespace System\Database;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use System\Database\Query\QueryBuilder;

class DatabaseManager extends QueryBuilder
{
    private $result;
    
    
    protected function result()
    {
        return $this->result;
    }

    public static function use(string $database, string $table)
    {
        $db = new self;
        $db->database = $database;
        $db->createNewConnection();
        return $db->mainTable($table);

    }


    /**
     * Begin a database transaction on a table
     *
     * @param string $table table name
     * @return \System\Database\DatabaseManager
     */
    public static function table(string $table)
    {
        $db = new self;
        if(!empty(self::$newdbConnection))
        {
          $db->database = self::$newdbConnection;
          $db->createNewConnection();  
        }
        return $db->mainTable($table);
    }


    /**
     * Add a Limit Clause to a query
     *
     * @param integer|null $limit
     * @return $this
     */
    public function limit(?int $limit = 25)
    {
        return $this->mainLimit($limit);
    }


    /**
     * Select data between the provided range of rows;
     * 
     * Suitable for pagenation data
     * 
     *
     * @param integer|null $limit
     * @return $this
     */
    public function range(int $first, int $second)
    {
        $this->mainRange($first, $second);
        return $this;
    }


    /**
     * Count the number of rows in a table or elements in an object
     *
     * @param string|null $column Optional column to perform a count on
     * @return int
     */
    public function count(string $column = null)
    {
        if($column !== null)
        {
            $column = "COUNT($column)";
            return $this->get($column)->{$column};
        }
        return count($this->get());
    }


    /**
     * Find the lowest value among data elements
     *
     * @param string $column Column to peform the action upon
     * @return int
     */
    public function min(string $column)
    {
        $this->is_aggregate = true;
        $column = "MIN($column)";
        return $this->get($column)[0]->{$column};
    }


    /**
     * Find the maximun value among data elements
     *
     * @param string $column Column to peform the action upon
     * @return int
     */
    public function max(string $column)
    {
        $this->is_aggregate = true;
        $column = "MAX($column)";
        return $this->get($column)[0]->{$column};
    }

    /**
     * Find the average value among data elements
     *
     * @param string $column Column to peform the action upon
     * @return int
     */
    public function avg(string $column)
    {
        $this->is_aggregate = true;
        $column = "AVG($column)";
        return $this->get($column)[0]->{$column};
    }


    /**
     * Find the total sum of values on a data element
     *
     * @param string $column Column to peform the action upon
     * @return int
     */
    public function sum(string $column)
    {
        $this->is_aggregate = true;
        $column = "SUM($column)";
        return $this->get($column)[0]->{$column};
    }

    
    /**
     * Get a value for a single column
     *
     * @param string $column column to get the value from
     * @return string
     */
    public function value(string $column)
    {
        $this->single_column = $column;

        $this->get($column);
        return $this->single_column;
        
    }

    
    /**
     * Get the result data from the executed transaction
     *
     * @param array|string $columns
     * @return object
     */
    public function get($columns = ['*'])
    { 

        if(is_array($columns))
        {
            $columns = implode(',', $columns);
        }

        if(!empty(trim($this->row_query)))
        {
            if(strpos($this->row_query, "SELECT") !== false)
            {
                $stmt = $this->bindQueryData($this->row_query, $this->queryData);
                $this->execute($stmt);
                return $this->result();
            }
        }

        if($this->is_select)
        {
            $this->addSoftDeletes();
            
            if($this->is_where)
            {
                if($this->is_join)
                {
                    $this->addJoinClause($columns);

                }else
                {
                    $this->addWhereClause($columns);
                }
                
                
            }

            if($this->is_like)
            {
                $this->addLikeClause($columns);
            }

            
            if(!$this->is_where)
            {
                
                if($this->is_aggregate)
                {
                    
                    $this->query = str_replace('*', $columns, $this->query);
                }
            }
            
            if($this->is_row)
            {
                $stmt = $this->bindQueryData($this->query, $this->queryData);
                $this->executeRow($stmt);
                return $this->result();
            }

            if(!empty(trim($this->single_column)))
            {
                
                $stmt = $this->bindQueryData($this->query, $this->queryData);
                $this->executeOne($stmt);
                return;
            }
            $stmt = $this->bindQueryData($this->query, $this->queryData);
            $this->execute($stmt);
            return $this->result();
        }
           
    }


    protected function execute(PDOStatement $stmt)
    {
        try{
            $stmt->execute();
            $this->result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e)
        {
          $this->logError($e->getMessage());   
        }
    }


    protected function executeRow(PDOStatement $stmt)
    {
        try{
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $stmt->execute();
            $this->result = $stmt->fetch();
        } catch(PDOException $e)
        {
          $this->logError($e->getMessage());   
        }
    }


    protected function executeOne(PDOStatement $stmt)
    {
        try{

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $value = $stmt->fetch();
            
            if(!empty($value))
            {
                $this->single_column = $value[$this->single_column];
                return;
            }
            $this->single_column = '<span style="color:red;">not found</span>';
        } catch(PDOException $e)
        {
          $this->logError($e->getMessage()); 
          $this->single_column = '<span style="color:red;">not found</span>';  
        }
    }


    /**
     * Indicate that you want to Select one row
     *
     * Call to the get method after this returns a Data Object
     * @return $this
     */
    public function row()
    {
       return $this->mainRow();
    }



    /**
     * Save data into the database
     *
     * @param array $data associtaive array of keys(columns) and value to insert
     * 
     * For multiple insert parse an array of arrays of data
     * 
     * Get the affected row: DB::affectedRows();
     * 
     * Get the last Insert Id: DB::lastId()
     * @return bool true on success, false on failuer
     */
    public function save(array $data)
    {
        $bindings = $data;

        if(gettype(reset($data)) !== "string")
        {
            $bindings = $data[0];
            $this->queryData = $data;
        }
        else 
        {
            $this->queryData = array($data);
        }

        $this->prepareInsert($bindings);
        $stmt = $this->prepareTransaction($this->query);
        
        return $this->executeInsert($stmt);
    }


    /**
     * Update data in a database table
     *
     * @param array $data an associtaive array of keys(columns) and value to update
     * 
     * call DB::affected() to get the number of affected rows
     * @return bool True on success, FALSE on failure
     */
    public function update(array $data)
    {   
        if(empty($this->queryData))
        {
            throw new Exception('Expected the where() method to be called before update!');
        }
        $this->prepareUpdate($data);
        $update_value = array_merge(array_values($data), $this->queryData);
        $stmt = $this->bindQueryData($this->query, $update_value);
        return $this->executeUpdate($stmt);
    }


    /**
     * Delete a resource from the database table
     *
     * Call to this method directly on a table DB::table('tableName')->delete(); deletes all the resourses.
     * If soft delets are not enabled, resources will be deleted parmanently
     * @return bool True on success, False on failure
     */
    public function delete()
    {   
        if(empty($this->queryData))
        {
            $this->prepareDelete(true);
            $rows = $this->executeStatement($this->query);
            session(['affectedRows' => $rows]);
            return $rows > 0;
        }

        if($this->isSoftDeletes())
        {
            $data = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];
            return $this->update($data);
        }
        $this->prepareDelete();
        $stmt = $this->bindQueryData($this->query, $this->queryData);
        return $this->executeUpdate($stmt);
    }


    /**
     * Parmanently remove a resource from a table with soft deletes enabled.
     * 
     *Call to this method directly on a table DB::table('tableName')->forceDelete(); deletes all the resourses.
     * @return bool True on success, False on failure
     */
    public function forceDelete()
    {
        if(empty($this->queryData))
        {
            $this->prepareDelete(true, true);
        }
        else 
        {
            $this->prepareDelete();  
        }

        $stmt = $this->bindQueryData($this->query, $this->queryData);
        return $this->executeUpdate($stmt);
    }

    /**
     * Restore deleted resources
     * 
     * If called dirrectly on DB::table(), the method will attempt to restore all deleted resources on a table
     *
     * Only works on tables with soft deletes enabled
     * @return bool True on success, False on failure
     */
    public function restore()
    {
        if(!$this->isSoftDeletes())
        {
            throw new Exception("The current table({$this->tableName}) does not have soft deleted enabled.
            Please enable soft deletes in order to use this method.");
        }

        $data = [
            'deleted_at' => NULL
        ];
        
        if(empty($this->queryData))
        {
           $this->prepareRestore();
           $rows = $this->executeStatement($this->query);
           session(['affectedRows' => $rows]);
           return $rows > 0;
        }

        return $this->update($data);
    }

    /**
     * Get the number of affected rows after an insert or update tranasction
     *
     * @return int
     */
    public static function affectedRows()
    {
        $rows = session('affectedRows', 0);
        unset($_SESSION['affectedRows']);
        return $rows;
    }


    /**
     * Get the last id after an insert tranasction
     *
     * @return int
     */
    public static function lastId()
    {
        $id = session('lastId');
        unset($_SESSION['lastId']);
        return $id;
    }


    protected function executeInsert(PDOStatement $stmt)
    {
        try {
            
            if(!empty($this->queryData))
            {
                $this->beginTrasaction();
                $rows = 0;
                $last_id = 0;
                foreach($this->queryData as $row) {

                    $stmt->execute($row);
                    $rows += $stmt->rowCount();
                    $last_id = $this->db->lastInsertId();
                }
                $this->commit();
                session([
                    'lastId' => $last_id,
                    'affectedRows' => $rows
                ]);
                return $rows > 0;
            }

        } catch (PDOException $e) {
            $this->rollBack();
            $this->logError($e->getMessage());
            return false;
        }
    }
    


    protected function executeUpdate(PDOStatement $stmt)
    {
        try {
            
            if(!empty($this->queryData))
            {
                $this->beginTrasaction();
                $stmt->execute();
                $this->commit();
                session(['affectedRows' => $stmt->rowCount()]);
                return $stmt->rowCount() > 0;
            }

        } catch (PDOException $e) {
            $this->rollBack();
            $this->logError($e->getMessage());
        }
    }

    
    /**
     * Execute row query
     *
     * @param string $query A valid SQL statement
     * @return $this
     */
    public static function query(string $query)
    {
        $db = new self;
        $db->row_query = $query;
        return $db;
    }


    /**
     * Add query data binds for the prepared statement
     *
     * @param array $binding an indexed array of values
     * @return $this
     */
    public function bindings(array $binding)
    {
        $this->queryData = $binding;
        return $this;
    }


    /**
     * Determine if a resource exists in a database
     * 
     *
     * @return bool True if the resource exists, False otherwise
     */
    public function exists()
    {   
        if(empty($this->queryData))
        {
            throw new Exception('Expected the where() method to be called before exists()!');
        }
        $this->is_row = true;
        return !empty($this->get());
    }


    /**
     * Determine if a resource does not exist in a database
     * 
     *
     * @return bool True if the resource does not exist, False otherwise
     */
    public function doesNotExist()
    {
        $this->addSoftDeletes();
        
        if(empty($this->queryData))
        {
            throw new Exception('Expected the where() method to be called before doesNotExist()!');
        }
        $this->is_row = true;
        return empty($this->get());
    }

    /**
     * Get a single resource from the database as a result of joining 2 models
     *
     * @param int|string $id
     * @param string $column defaults to id
     * @return object
     */
    public function find($id, string $column = 'id')
    {
        $this->is_row = true;
        return $this->where($column, $id)->get();
    }

    /**
     * Execute a database statment
     *
     * @param string $statement
     * @return int|false
     */
    public static function exec(string $statement)
    {
        return (new self)->executeStatement($statement);
    }

    public function __call($name, $arguments)
    {
        return $this->{$name(...$arguments)};
    }

    
    public function __destruct()
    {
        $this->db = null;
        $this->query = '';
        $this->is_select = false;
        $this->is_row = false;
        $this->result = array();
        $this->single_column = '';
        $this->is_aggregate = false;
        $this->is_join = false;
        $this->is_like = false;
        $this->row_query = '';
    }
}