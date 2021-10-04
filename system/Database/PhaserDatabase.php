<?php
namespace System\Database;

use PDO;

interface PhaserDatabase
{
    /**
     * Select One row of data
     *
     * @param string $columns
     * @return Object
     */
    public function _row(string $columns);

    /**
     * Parse specific columns to select
     *
     * @param string $columns
     * @return Object
     */
    public function _select(string $columns);

    /**
     * Select Distinct Values
     *
     * @param string $columns
     * @return Object
     */
    public function _distinct(string $columns);


    /**
     * Get a specific value from a column
     *
     * @param string $column
     * @return string
     */
    public function value(string $column);

    /**
     * @param string $column A column name from where the count will happen
     * @param string $tableName A table name to count from
     * @return int   Number of count returned
     */
    public function _count(string $column = '*');

    /**
     * Maximum Value
     *
     * @param string $column
     * @return int|string
     */
    public function max(string $column);

    /**
     * Minmun value
     *
     * @param string $column
     * @return int|string
     */
    public function min(string $column);

    /**
     * Avergae Value
     *
     * @param string $column
     * @return int|string
     */
    public function avg(string $column);

    /**
     * Sum up values
     *
     * @param string $column column to sum its values
     * @return int|string
     */
    public function sum(string $column);

    /**
     * Value Exits
     * 
     * Call this method after where()
     *
     * @return bool
     */
    public function exists();

    /**
    * @return bool
    *The where method should be called first other every data will be deleted
    */
   public function delete();

   /**
    * Soft delete a record
    *
    * @return bool
    */
   public function trash();

   /**
    * Restore deleted items
    *
    * @return bool
    */
   public function restore();

    /**
     * Execute a custom query for SELECT OR UPDATE
     * @param string $query A string containing a custom prepared query statement to execute. Must be a select Query using placeholders (?) if the condition is used
     * @param array $data The data to bind with the  parameters
     * @param string $fetchMode PDO::FETCH_BOTH or PDO::FETCH_ASSOC
     * @return array|bool An associative array containing rows returned from the query
     */
    public function _query($query, array $data = [], $fetchMode = PDO::FETCH_BOTH);

    /**
     * @param string $column The column name to get the maximum value from
     * @param string $tableName The name of the table to act upon
     * @return int|string The maximum value obtained
     */
    public function aggregate($column, $tableName, string $aggregate = "MAX");


    /**
     * Initialize the join table sequence
     *
     * @param string $table_name The parent table to select data from
     * @param string $column_names The columns to select
     * @return void
     */
    public function initJoin(string $columns = '*');


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
    public function join(string $table_name, string $parent_key, string $foreign_key);


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
    public function leftJoin(string $table_name, string $parent_key, string $foreign_key);

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
    public function rightJoin(string $table_name, string $parent_key, string $foreign_key);


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
    public function unionJoin(array $tables, bool $distinct = false);


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
    public function whereOr(array $where_data, string $operator = "=");


    /**
     * Order by clause
     *
     * @param string $order The colum(s) to order by
     * @param string $mode Defaults ASC
     * @return void
     */
    public function orderBy(string $order, string $mode = "ASC");


    /**
     * SELECT BETWEEN
     *
     * @param array $between_data
     * @param string $columns
     * @return void
     */
    public function between(array $between_data, $columns = '*');


    /**
     * Initialize the LIKE operation in fetching data
     *
     * @param string $columns The columns to select
     * @param string $table The table name to select the data from
     * @return void
     * 
     * Should be followed by a call to the like() method
     */
    public function initLike(string $columns = '*');


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
    public function like(array $like_data);


    /**
     * The OR operator construct
     * Forms LIKE ... OR .. LIKE ... OR
     * @param array $like_data An associate array of column name and value
     * @return void
     * 
     * The like() method should be called fist with at least one pair of key and value
     */
    public function likeOr(array $like_data);


    /**
     *  A helper method to return the data resulting from the execution of
     * 
     * Joins
     * 
     * between
     * 
     * like
     * @param Closuer|bool $callback A callback function or a bool(true) to return an array
     *
     * @return object|array An associative array of data.
     */
    public function get($callback = NULL);

    /**
     * Get a Database Instance for Database operations
     * 
     * @param mixed $tableName The name of the table
     * @param array $tabledata Optioanl data, (used in inserting and updating)
     * @return object Database Instance
     */
    public static function table($tableName, array $tableData = []);

    /**
     * Insert one|many, Update
     *
     * @return bool
     */
    public function save();

    /**
     * @param array $wherePos  An associative array with the where column and the condition
     * @param string $operator Takes the operator to be used. Default is =;
     * for multiple operators, seperate them with commas eg =, >, !=
     * @return object An Instance of the DaB class
     */
    public function _where(array $wherePos, $operator = '=');

    /**
     * @param string $database_name The name of the database to use for querying.
     * Use this function if you intend not use the default database
     * @return Object DB Instance
     */
    public function use($database_name);
}