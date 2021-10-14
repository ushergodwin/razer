<?php
namespace System\Database\Eloquent;

use System\Database\DB;
use System\Database\FluentApi;

class Eloquent extends FluentApi
{

    protected $table;
    protected $massive_data;
    protected $data;

    public static function where(string $column, string $value, string $operator = '=')
    {
        $table_name = explode("\\", strtolower(get_called_class()));
        $table_name = $table_name[(count($table_name) - 1)];
        return DB::table($table_name)->where($column, $value, $operator);
    }

    /**
     * Find a model in the database by ID
     *
     * @param int $id
     * @return \System\Database\DatabaseManager
     */
    public static function find($id)
    {
        $table_name = explode("\\", strtolower(get_called_class()));
        $table_name = $table_name[(count($table_name) - 1)];
        return DB::table($table_name)->row()->where('id', $id);
    }


    /**
     * Get all of the models from the database.
     *
     * @return obejct
     */
    public static function all()
    {
        $table_name = explode("\\", strtolower(get_called_class()));
        $table_name = $table_name[(count($table_name) - 1)];
        return DB::table($table_name)->get();
    }


    /**
     * Save the model to the database.
     *
     * @return bool
     */
    public function save()
    {
      if(empty($this->data))
      {
        $this->data = $this->massive_data;
      }

      if(!empty($this->data) && !empty($this->massive_data))
      {
        $this->data = array_merge($this->massive_data, $this->data);
      }
      return DB::table($this->table)->save($this->data);
    }


    /**
     * Get the last Insert Id of the resource
     *
     * @return int
     */
    public function lastId()
    {
      return DB::lastId();
    }

    /**
     * Get the number of models saved
     *
     * @return int
     */
    public function affectedRows()
    {
      return DB::affectedRows();
    }
}