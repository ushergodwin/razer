<?php
namespace System\Database\Query;

interface Query
{
    public function mainTable(string $table);
    

    /**
     * Add a where clause on the query
     * 
     * The where method builds WHERE AND combination when called multiple times
     *
     * @param string $column column name
     * @param string|int $value value for the colum
     * @param string $operator The operator to use, Defaults to = 
     */
    public function where(string $column, $value, string $operator = '=');


    /**
     * Add an OR clause on after a WHERE clause to the query
     *
     * The where method builds WHERE OR combination when called multiple times
     * @param string $column column name
     * @param string|int $value value for the colum
     * @param string $operator The operator to use, Defaults to = 
     */
    public function orWhere(string $column, $value, string $operator = '=');



    /**
     * Add an INNER JOIN clause to the query
     *
     * @param string $table table to join
     * @param string $first table.primary key
     * @param string $second table.forginkey
     */
    public function join(string $table, string $first, $second);
    

    /**
     * Add a LEFT JOIN clause to the query
     *
     * @param string $table table to join
     * @param string $first table.primary key
     * @param string $second table.forginkey
     */
    public function leftJoin(string $table, string $first, $second);


    /**
     * Add an RIGHT JOIN clause to the query
     *
     * @param string $table table to join
     * @param string $first table.primary key
     * @param string $second table.forginkey
     */
    public function rightJoin(string $table, string $first, $second);


    /**
     * Join tables with a UNION JOIN
     *
     * @param string $table table to join
     * @param array|string $columns table columns
     * @param bool $distinct Set to true if you want distinct values. defaults to false
     */
    public function unionJoin(string $table, $columns = ['*'], bool $distinct = false);


    /**
     * Add a LIKE clause to a query
     * 
     * The method constructs WHERE LIKE value AND 
     *
     * @param string $column column name
     * @param string $value column value
     * @return $this
     */
    public function like(string $column, string $value);


    /**
     * Add a LIKE OR clause to a query
     * 
     * The method constructs WHERE LIKE value OR 
     *
     * @param string $column column name
     * @param string $value column value
     * @return $this
     */
    public function OrLike(string $column, string $value);


    /**
     * Add a BETWEEN clause to a query
     *
     * @param string $column
     * @param string|int $first first value in the range
     * @param string|int $second second value in the range
     */
    public function between(string $column, $first, $second);


    /**
     * Add a DISTINCT clause to a query
     *
     * @param string $column
     */
    public function distinct(string $column);


    /**
     * Add ORDER BY clause to a query
     *
     * @param string $column order column
     * @param string $order order value. defaults to ASC
     * @return $this
     */
    public function orderBy(string $column, string $order = "ASC");


    /**
     * Select one row
     *
     * @param array|string $columns
     */
    public function mainRow($columns = ['*']);


    /**
     * Add a Limit Clause to a query
     *
     * @param integer|null $limit
     */
    public function mainLimit(int $limit = null);


}