<?php
use System\Database\Schema\BluePrint;
use System\Database\Schema\Schema;


 class CreatePasswordResetsTable
 { 
		/**
		* Run the Migrations
		*
		* @return void
		*/ 
		public function up()
		{

			Schema::create('password_resets', function (BluePrint $table) {

				$table->id();
				$table->string('email', 35);
				$table->string('token');
				$table->timestamps(); 
			}); 

		} 

		/**
		* Modify Migrations
		*
		* @return void
		*/ 
		public function alter()
		{

			Schema::modify('password_resets', function (BluePrint $table) {

				 
			}); 

		} 

		/**
		* Reverse the migrations.
		*
		* @return void
		*/

		public function down()
		{

			Schema::dropIfExists('password_resets');
     
		} 

}