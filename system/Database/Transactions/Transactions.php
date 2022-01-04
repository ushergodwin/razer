<?php

namespace System\Database\Transactions;

use System\Database\Connection\Connection;

class Transactions extends Connection
{

    protected function bindQueryData(string $query, array $data)
    {
        $stmt = $this->db->prepare($query);
        array_unshift($data, 0);
        $data_count = count($data);

        if($data_count > 1)
        {
            for($i = 1; $i < $data_count; $i++) {
                $stmt->bindParam($i, $data[$i]);
            }
        }
        return $stmt;
    }

    /**
     *Initiates a transaction
    *
    *Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object instance are not committed until you end the transaction by calling $this->commit()
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    protected function beginTrasaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    protected function commit()
    {
        return $this->db->commit();
    }

    /**
     * Roll back an transaction
     *
     * @return bool TRUE on success or FALSE on failure
     */
    protected function rollBack()
    {
        return $this->db->rollBack();
    }

    /**
     * Establish a new connection
     *
     * @return \PDO
     */
    protected function createNewConnection()
    {
        $this->db = null;
        $this->db = $this->connect();
        return $this->db;
    }

    /**
     * Get a new connection instance
     *
     * @return \PDO
     */
    protected function getNewConnectionInstance()
    {
        $this->db = null;
        $this->db = $this->connect();
        return $this->db;
    }


    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     * @return int|false
     */
    protected function executeStatement(string $statement)
    {
        return $this->db->exec($statement);
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $data The string to be quoted
     * @return string|false a quoted string that is safe to pass into an SQL statement. Returns FALSE if the driver does not support quoting in this way.
     */
    protected function quote($value)
    {
        return $this->db->quote($value);
    }
    


    /**
     * Prepare query for execution
     *
     * @param string $query
     * @return \PDOStatement|false
     */
    protected function prepareTransaction(string $query)
    {
        $stmt = $this->db->prepare($query);
        return $stmt;
    }
}