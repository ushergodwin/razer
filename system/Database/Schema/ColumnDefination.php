<?php
namespace System\Database\Schema;

class ColumnDefination
{
    use Modifications;

    protected static $migration;
    protected static $table;
    protected static $is_engine_specified = '';
    protected static $dropForeignkey = array();
    protected static $rollBackMigration = '';
    protected static $is_bool = false;

    /**
     * Create a new auto-incrementing big integer (8-byte) column on the table
     *
     * @param string $column
     * @return void
     */
    public function id(string $column = 'id')
    {
        self::$migration .= "`".$column . "` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,";
    }

    public function bigIncrements(string $column)
    {
        self::$migration .= "`".$column . "` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,";
    }

    /**
     * Create a new integer (4-byte) column on the table.
     *
     * @param string $column
     * @param boolean $autoIncrement
     * @param boolean $unsigned
     * @return \System\Database\Schema\ColumnDefination
     */
    public function integer(string $column, bool $unsigned = false)
    {
        self::$migration .= "\n\t`$column` INT(11) ";
        if($unsigned){
            self::$migration .= "UNSIGNED ";
        }

        self::$migration .= "NOT NULL, ";

        return $this;
    }


    /**
     * Create a new unsigned integer (4-byte) column on the table.
     *
     * @param string $column
     * @param boolean $autoIncrement
     * @return \System\Database\Schema\ColumnDefination
     */
    public function unsignedInteger(string $column)
    {
        self::$migration .= "\n\t`$column` INT(11) UNSIGNED NOT NULL, ";
        return $this;
    }


    /**
     * Create a new big integer (8-byte) column on the table.
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function bigInteger(string $column)
    {
        self::$migration .= "\n\t`".$column . "` BIGINT NOT NULL, ";
        return $this;
    }

    public function decimal(string $column)
    {
        self::$migration .= "\n\t`".$column . "` DECIMAL NOT NULL, ";
        return $this;
    }

    public function float(string $column)
    {
        self::$migration .= "\n\t`".$column . "` FLOAT NOT NULL,";
        return $this;
    }

    public function double(string $column)
    {
        self::$migration .= "\n\t`".$column . "` DOUBLE NOT NULL, ";
        return $this;
    }

    public function boolean(string $column)
    {
        self::$migration .= "\n\t`".$column . "` TINYINT(1) NOT NULL, ";
        self::$is_bool = true;
        return $this;
    }


    /**
     * Create a new VARCHAR(100) column on a table
     *
     * @param string $column
     * @param integer $length Defaults to 100
     * @return \System\Database\Schema\ColumnDefination
     */
    public function string(string $column, int $length = 100)
    {
        self::$migration .= "\n\t`$column` VARCHAR($length) NOT NULL, ";
        return $this;
    }

    /**
     * Create a new text column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function text(string $column)
    {
        self::$migration .= "\n\t`$column` TEXT NOT NULL, ";
        return $this;
    }

    /**
     * Create a new LONGTEXT column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function bigText(string $column)
    {
        self::$migration .= "\n\t`$column` LONGTEXT NOT NULL, ";
        return $this;
    }

    /**
     * Create a new Char column on a table
     *
     * @param string $column
     * @param integer|null $length
     * @return \System\Database\Schema\ColumnDefination
     */
    public function char(string $column, int $length = null)
    {
        if($length !== null)
        {
            self::$migration .= "`$column` CHAR($length) NOT NULL, ";
            return $this; 
        }
        self::$migration .= "`$column` CHAR NOT NULL, ";
        return $this;
    }

    /**
     * Create a new enum column on a table
     *
     * @param string $column
     * @param array $allowed
     * @return \System\Database\Schema\ColumnDefination
     */
    public function enum(string $column, array $allowed)
    {
        $allowed_count = count($allowed);

        self::$migration .= "\n\t`$column` ENUM(";

        for($i = 0; $i < $allowed_count; $i++){
            self::$migration .= "'" . $allowed[$i] . "',";
        }
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 1);
        self::$migration .=  ") NOT NULL, ";
        return $this;
    }

    /**
     * Create a new Date column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function date(string $column)
    {
        self::$migration .= "\n\t`$column` DATE NOT NULL, ";
        return $this;
    }


    /**
     * Create a new datetime column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function dateTime(string $column)
    {
        self::$migration .= "\n\t`$column` DATETIME NOT NULL, ";
        return $this;
    }


    /**
     * Create a new time column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function time(string $column)
    {
        self::$migration .= "\n\t`$column` TIME NOT NULL, ";
        return $this;
    }


    /**
     * Create a new timestamp column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function timestamp(string $column)
    {
        self::$migration .= "\n\t`". $column. "` TIMESTAMP NOT NULL, ";
        return $this;
    }


    /**
     * Add nullable creation and update timestamps to the table
     *
     * @param integer $precesion
     * @return void
     */
    public function timestamps()
    {
        self::$migration .= "\n\t`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP, ";
        self::$migration .= "\n\t`update_at` DATETIME NULL DEFAULT NULL, ";
    }

    /**
     * Create a new year column on a table
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function year(string $column)
    {
        self::$migration .= "`$column` YEAR NOT NULL, ";
        return $this;
    }


    /**
     * Add Default value as CURRENT_TIMESTAMP
     *
     * @return void
     */
    public function currentTimeStamp()
    {
        self::$migration .= " DEFAULT CURRENT_TIMESTAMP, ";
    }
    

    /**
     * Specify a "default" value of a column
     *
     * @param mixed $value
     * @return $this
     */
    public function default($value)
    {
        if($value === false)
        {
            $value = 0;
        }
        if($value === true)
        {
            $value = 1;
        }
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2)
        ." DEFAULT '$value', ";
        return $this;
    }

    /**
     * Allow NULL values to be inserted into the column
     *
     * @return $this
     */
    public function nullable()
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 10);
        self::$migration .= " NULL, ";
        $this->is_null = true;
        return $this;
    }

    /**
     * Set an Integer column as UNSIGNED (MySQL)
     *
     * @return $this
     */
    public function unsigned()
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 10);
        self::$migration .= "UNSIGNED NOT NULL, ";
        $this->is_null = true;
        return $this;
    }

    /**
     * Add a unique index
     *
     * @return $this
     */
    public function unique()
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 10);
        self::$migration .= "UNIQUE NOT NULL, ";
        return $this;
    }

    /**
     * Add AUTO_INCREMENT and PRIMARY KEY constraints to Integer column
     *
     * @return void
     */
    public function autoIncrement()
    {
        self::$migration .= "PRIMARY_KEY, AUTO_INCREMENT,";
    }

    /**
     * Add a comment to the column (MySQL/PostgreSQL)
     *
     * @param string $comment
     * @return $this
     */
    public function comment(string $comment)
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2)
        . " COMMENT '$comment',";
        return $this;
    }

    /**
     * Specify a foreign key for the table.
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function foreign(string $column)
    {
        $constraint = self::$table . "_" . $column;
        self::$migration .= "\n\t CONSTRAINT fk_" ."$constraint FOREIGN KEY(`".$column . "`) ";
        return $this;
    }


    /**
     * Specify the referenced table
     *
     * @param string $table
     * @return \System\Database\Schema\ColumnDefination
     */
    public function references(string $table)
    {
        self::$migration .= "REFERENCES $table(`";
        return $this;
    }

    /**
     * Specify the referenced column
     *
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function on(string $column)
    {
        self::$migration .= "$column`), ";
        return $this;
    }


    /**
    * Add ON DELETE action
    *
    * @param string $action
    * @return $this
    */
    public function onDelete(string $action)
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2);
        self::$migration .= " ON DELETE ". strtoupper($action) . ", ";
        return $this;
    }

    /**
     * Add ON DELETE CASCADE action on a columns
     *
     * @return void
     */
    public function cascadeOnDelete()
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2);
        self::$migration .= " ON DELETE CASCADE, ";
    }

    
    /**
     * Add ON UPDATE action
     *
     * @param string $action
     * @return $this
     */
    public function onUpdate(string $action = 'cascade')
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2);
        self::$migration .= " ON UPDATE ". strtoupper($action) . ",";
        return $this;
    }

    /**
     * Add ON UPDATE CASCADE action on a column
     *
     * @return void
     */
    public function cascadeOnUpdate()
    {
        self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2);
        self::$migration .= " ON UPDATE CASCADE, ";
    }


    /**
     * Create a new unsigned big integer (8-byte) column on the table.
     *
     * @param string $column
     * @return $this
     */
    public function foreignId(string $column, bool $null = false)
    {
        self::$migration .= "\n\t`".$column . "` BIGINT UNSIGNED ";

        if($null){
            self::$migration .= "NULL, ";
        }
        self::$migration .= "NOT NULL, ";

        $constraint = self::$table . "_" . $column;
        self::$migration .= "\n\tCONSTRAINT fk_" ."$constraint FOREIGN KEY(`". $column . "`) REFERENCES ";
        return $this;
    }


    /**
     * Create a foreign key constraint on this column referencing the "id" column of the conventionally related table.
     *
     * @param string|null $table
     * @param string $column
     * @return \System\Database\Schema\ColumnDefination
     */
    public function constrained($table = null, string $column = 'id')
    {
        self::$migration .= $table . "(`$column`)  ";
        return $this;
    }

    public function engine(string $engine)
    {
        self::$is_engine_specified = $engine;
    }

    /**
     * Indicate that the given foreign key should be dropped.
     *
     * @param string $key
     * @return void
     */
    public function dropForeignId(string $key){
        $table = self::$table;
        $key = $table . "_" . $key;
        self::$dropForeignkey[] = "ALTER TABLE `$table` DROP FOREIGN KEY fk_".$key.";";
    }

    /**
     * Indicate that the given column and foreign key should be dropped.
     *
     * @param string $column
     * @return void
     */
    public function dropConstrainedForeignId($column) {
        $table = self::$table;
        $constraint = $table . "_" . $column;
        self::$dropForeignkey[] = "ALTER TABLE `$table` DROP FOREIGN KEY fk_".$constraint.";";
        self::$dropForeignkey[] = "ALTER TABLE `$table` DROP COLUMN ".$column.";";
    }

    public function softDeletes(string $column = 'deleted_at'){
        self::$migration .= "\n\t`$column` DATETIME NULL DEFAULT NULL, ";
        return $this;
    }

    public function __destruct()
    {
        if(!empty(trim(self::$is_engine_specified))) {
            self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2)
            . "\n ) ENGINE = ". self::$is_engine_specified . ";";
            self::$is_engine_specified = '';
        }else {
            self::$migration = substr(self::$migration, 0, strlen(self::$migration) - 2) . "\n );";
        }
    }
}