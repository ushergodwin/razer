<?php
namespace System\Database\Query;

use PDO;
use System\Database\Transactions\Transactions;

class QueryBuilder extends Transactions implements Query
{
    protected $query = "";

    protected $queryData = array();

    protected $tableName = "";

    protected $is_where = false;

    protected $is_select = false;

    protected $is_row = false;

    protected $single_column = '';

    protected $is_aggregate = false;

    protected $is_join = false;

    protected $is_like = false;

    protected $row_query = '';

    protected static $affectedRows = 0;

    protected static $lastInsertId = 0;

    protected $orderby = '';

    public function mainTable(string $table)
    {
        $this->query = "SELECT * FROM $table "; 
        $this->tableName = $table;
        $this->is_select = true;
        return $this;
    }


    public function where(string $column, $value, string $operator = '=')
    {
        if(strpos($column, '.') === false)
        {
            $column = $this->tableName . "." . $column;
        }
        $this->is_where = true;
        $this->query .= " AND $column $operator ? ";
        $this->queryData[] = $value;
        return $this;
    }

    protected function addWhereClause(string $columns)
    {
        $sub_query = "SELECT * FROM {$this->tableName} AND ";
        if(strpos($this->query, "DISTINCT") !== false)
        {//dd($columns);
            $sub_query = "SELECT DISTINCT {$columns} FROM {$this->tableName} AND ";
            $sub_query_len = strlen($sub_query);
            $this->query = substr($this->query, $sub_query_len, strlen($this->query) - $sub_query_len);
    
            $this->query = "SELECT DISTINCT {$columns} FROM {$this->tableName} WHERE " . $this->query;
            return $this->query = trim($this->query);
        }
        $sub_query_len = strlen($sub_query);
        $this->query = substr($this->query, $sub_query_len, strlen($this->query) - $sub_query_len);

        $this->query = "SELECT $columns FROM {$this->tableName} WHERE " . $this->query;
        return $this->query = trim($this->query);
    }

    public function orWhere(string $column, $value, string $operator = '=')
    {
        $this->query .= " OR $column $operator ? ";
        $this->queryData[] = $value;
        return $this;
    }


    public function join(string $table, string $first, $second)
    {   $this->is_join = true;
        $this->query .= " INNER JOIN $table ON $first = $second ";
        return $this;

    }

    protected function prepareInsert(array $data)
    {
        $keys = array_keys($data);
        $named_keys = array();

        foreach($keys as $value)
        {
            $named_keys[] = ":".$value;
        }

        $this->query = "INSERT INTO {$this->tableName}(" . implode(', ', $keys) . ")";
        $this->query .= " VALUES (". implode(", ", $named_keys) . ")";

        
    }


    protected function prepareUpdate(array $data)
    {
        $keys = array_keys($data);

        $update_named_keys = array();

        foreach($keys as $value)
        {
            $update_named_keys[] = $value . " = ?";
        }
        
        $slect_query_count = strlen("SELECT * FROM {$this->tableName} AND ");
        $where_clause = substr($this->query, $slect_query_count, strlen($this->query) - $slect_query_count);
        $where_clause = " WHERE " . $where_clause;
        $this->query = "UPDATE {$this->tableName} SET " . implode(', ', $update_named_keys);
        $this->query .= $where_clause;
      
    }



    protected function prepareDelete(bool $all = false, bool $ignore = false)
    {
        $slect_query_count = strlen("SELECT * FROM {$this->tableName} AND ");
        $where_clause = substr($this->query, $slect_query_count, strlen($this->query) - $slect_query_count);
        $where_clause = " WHERE " . $where_clause;
        $this->query = "DELETE FROM {$this->tableName} " . $where_clause;

        if($all)
        {
            $this->query = "TRUNCATE TABLE {$this->tableName}";

            if($this->isSoftDeletes())
            {
                if(!$ignore)
                {
                    $this->query = "UPDATE {$this->tableName} SET deleted_at = "
                    .$this->quote(date('Y-m-d H:i:s'));
                }
            }
        }
      
    }

    protected function prepareRestore()
    {
        $this->query = "UPDATE {$this->tableName} SET deleted_at = NULL";
    }


    /**
     * Add soft deletes on a query if enabled
     *
     * @return void
     */
    protected function addSoftDeletes()
    {
        $stmt = $this->db->prepare("DESCRIBE {$this->tableName}");
        $stmt->execute();
        $columns = array();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $stmt->fetch()){
            $columns[] = $row['Field'];
        }

        if(in_array('deleted_at', $columns))
        {
            if($this->is_where)
            {
                $this->query .= " AND {$this->tableName}.deleted_at IS NULL";
            }else {
                $this->is_where = true;
                $this->query .= " AND {$this->tableName}.deleted_at IS NULL";
            }
        }

        if(!empty(trim($this->orderby)))
        {
            $this->query .= $this->orderby;
        }
    }


    
    /**
     * Check if the current table has soft deletes enabled
     *
     * @return boolean
     */
    protected function isSoftDeletes()
    {
        $stmt = $this->db->prepare("DESCRIBE {$this->tableName}");
        $stmt->execute();
        $columns = array();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $stmt->fetch()){
            $columns[] = $row['Field'];
        }

        if(in_array('deleted_at', $columns))
        {
            return true;
        }

        return false;
    }


    protected function prepareTrancate()
    {
        $this->query = "TRUNCATE TABLE {$this->tableName}";
      
    }


    public function leftJoin(string $table, string $first, $second)
    {
        $this->is_join = true;
        $this->query .= " LEFT JOIN $table ON $first = $second ";
        return $this;

    }

    public function rightJoin(string $table, string $first, $second)
    {
        $this->is_join = true;
        $this->query .= " RIGHT JOIN $table ON $first = $second ";
        return $this;

    }


    protected function addJoinClause(string $columns)
    {
        $pos = strpos($this->query, "AND");
        $join = substr($this->query, 0, $pos);
        $where = substr($this->query, $pos, strlen($this->query) - $pos);
        $where = substr($where, 3, strlen($where) - 3);
        $where = " WHERE " . $where;
        $this->query = str_replace('*', $columns, $join . $where);
        $this->query = trim($this->query);
    }

    public function unionJoin(string $table, $columns = ['*'], bool $distinct = false)
    {
        $union = $distinct ? " UNION ALL ": " UNION ";
        $this->query .= " $union SELECT " . implode(',', $columns) . " FROM $table ";
        return $this;
    }

    public function like(string $column, string $value)
    {
        $this->is_like = true;
        $value = htmlentities($value);
        $this->query .= " AND $column LIKE '$value' ";
        return $this;
    }


    public function OrLike(string $column, string $value)
    {
        $this->is_like = true;
        $value = htmlentities($value);
        $this->query .= " OR $column LIKE '$value' ";
        return $this;
    }

    public function between(string $column, $first, $second)
    {   
        $this->is_like = true;
        $this->query .= " AND $column BETWEEN ? AND ? ";
        $this->queryData[] = $first;
        $this->queryData[] = $second;
        return $this;
    }


    protected function addLikeClause(string $columns)
    {
        $sub_query = "SELECT * FROM {$this->tableName} AND ";
        $sub_query_len = strlen($sub_query);
        $this->query = substr($this->query, $sub_query_len, strlen($this->query) - $sub_query_len);

        $this->query = "SELECT $columns FROM {$this->tableName} WHERE " . $this->query;
        $this->query = trim($this->query);
    }


    protected function addBetweenClause(string $columns)
    {
        $sub_query = "SELECT * FROM {$this->tableName} AND ";
        $sub_query_len = strlen($sub_query);
        $this->query = substr($this->query, $sub_query_len, strlen($this->query) - $sub_query_len);

        $this->query = "SELECT $columns FROM {$this->tableName} WHERE " . $this->query;
        $this->query = trim($this->query);
    }

    public function distinct(?string $column)
    {
        $this->query = "SELECT DISTINCT $column FROM {$this->tableName} ";
        return $this;
    }

    public function orderBy(string $column, string $order = "ASC")
    {
        $this->orderby .= " ORDER BY $column $order ";
        return $this;
    }

    public function mainRow($columns = ['*'])
    {
        $this->is_row = true;

        return $this;
    }

    public function mainLimit(?int $limit = null)
    {
        $this->query .= " LIMIT $limit ";
        
        return $this;
    }

    public function mainRange(?int $first, int $second)
    {
        $this->query .= " LIMIT $first, $second ";
        
        return $this;
    }
    
}