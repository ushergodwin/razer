<?php
namespace System\Database\Schema;
use System\Database\Schema\BluePrint;
use Closure;

class Schema extends BluePrint
{
    /**
     * SET ON DELETE|UPDATE TO CASCADE
     * @var const CASCADE
     */
    public const CASCADE = 'cascade';

    /**
     * SET ON DELETE|UPDATE TO CURRENT_TIMESTAMP
     * 
     * should only be applied on columns with date or datetime datatypes
     * @var const CURRENT_TIMESTAMP
     */
    public const CURRENT_TIMESTAMP = 'current_timestap';

    /**
     * SET ON DELETE|UPDATE TO RESTRICT
     * 
     * @var const RESTRICT
     */
    public const RESTRICT = 'restrict';

    /**
     * SET ON DELETE|UPDATE TO SET NULL
     * 
     * @var const SET NULL
     */
    public const SET_NULL = 'set null';

    /**
     * SET ON DELETE|UPDATE TO NO ACTION
     * 
     * @var const NO ACTION
     */
    public const NO_ACTION = 'no action';

    public const InnoDB = 'InnoDB';
    public const CSV = 'CSV';
    public const MRG_MyISAM = 'MRG_MyISAM';
    public const MEMORY = 'MEMORY';
    public const Aria = 'Aria';
    public const MyISAM = 'MyISAM';
    public const SEQUENCE = 'SEQUENCE';

    
    public static function create(string $table, Closure $callback)
    {
        self::$migration =  "CREATE TABLE IF NOT EXISTS ". $table . "(\n\t";
        self::$table = $table;
        call_user_func_array($callback, [new BluePrint]);
        
    }

    public static function dropIfExists($table)
    {
        self::$rollBackMigration = "DROP TABLE IF EXISTS $table;";
    }

    public static function table(string $table, Closure $callback)
    {
        self::$table = $table;
        call_user_func_array($callback, [new BluePrint]);
    }

    public static function modify(string $table, Closure $callback)
    {
        self::$modify_table = $table;
        call_user_func_array($callback, [new BluePrint]);
    }
}