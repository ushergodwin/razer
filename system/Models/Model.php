<?php
namespace System\Models;

require_once APPPATH().'/vendor/autoload.php';
use System\Database\DB;

class Model extends DB
{

    private $data = array();
    private $table = '';
    private $massive_data = array();
    private $primary_key = [];

    public function __construct($massive_data = [])
    {
        parent::__construct();
        $table_name = explode("\\", strtolower(get_called_class()));
        $this->table = $table_name[(count($table_name) - 1)];
        $this->massive_data = $massive_data;
    }
    public function __get( $property ) {
        if ( array_key_exists( $property, $this->data ) ) {
            return $this->data[$property];
        }
        return null;
      }
    
      public function __set( $property, $value ) {

          $this->data[$property] = $value;
          
        }
      
      public function save()
      {
        $this->objectTableName = $this->table;
        $this->objectTableData = $this->data;
        if(!empty($massive_data)){
          $this->objectTableData = $this->massive_data;
        }
        return parent::save();
      }
}
