<?php
namespace System\Models;

require_once APPPATH().'/vendor/autoload.php';

use System\Database\Eloquent\Eloquent;

class Model extends Eloquent
{


    public function __construct($massive_data = [])
    {
        $table_name = explode("\\", strtolower(get_called_class()));
        $this->table = self::decamelize($table_name[(count($table_name) - 1)]);
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
      

      /**
       * Save the model to the database.
       *
       * @return bool
       */
        public function save()
        {

          return parent::save();

        }
}
