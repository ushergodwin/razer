<?php
use System\Database\Schema\BluePrint;
use System\Database\Schema\Schema;


 class CreateUsersTable
 { 
		/**
		* Run the Migrations
		*
		* @return void
		*/ 
		public function up()
		{

			Schema::create('users', function (BluePrint $table) {

				$table->bigIncrements('id');
				$table->string('first_name');
				$table->string('last_name');
				$table->string('phone_number1');
				$table->string('phone_number2')->nullable();
				$table->string('address')->nullable();
				$table->string('email')->unique();
				$table->string('img_url')->nullable();
				$table->timestamp('email_verified_at')->nullable();
				$table->string('password')->nullable();
				$table->boolean('is_super')->default(false);
				$table->boolean('is_admin')->default(false);
				$table->boolean('is_customer')->default(true);
				$table->timestamps();
				$table->softDeletes();
			}); 

		} 


		public function alter()
		{
			Schema::modify('users', function (BluePrint $table){
				$table->renameColumn('first_name', 'fname', 'varchar', 30);
			});
		}


		/**
		* Reverse the migrations.
		*
		* @return void
		*/

		public function down()
		{

			Schema::dropIfExists('users');
     
		} 

}