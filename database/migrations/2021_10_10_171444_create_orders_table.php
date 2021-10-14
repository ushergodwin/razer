<?php
use System\Database\Schema\BluePrint;
use System\Database\Schema\Schema;


 class CreateOrdersTable
 { 
		/**
		* Run the Migrations
		*
		* @return void
		*/ 
		public function up()
		{

			Schema::create('orders', function (BluePrint $table) {

				$table->bigIncrements('id');
				$table->string('order_no')->unique();
				$table->integer('total_cost');
				$table->string('status')->default('pending')
				->comment('pending,approved,assigned');
				$table->bigInteger('user_id')->unsigned()->nullable();
				$table->foregin('user_id')->references('users')->on('id')
				->onDelete(Schema::CASCADE);
				$table->timestamps();
				$table->softDeletes();
			}); 

		} 

		public function alter()
		{
			Schema::modify('orders', function (BluePrint $table){

			});
		}

		/**
		* Reverse the migrations.
		*
		* @return void
		*/

		public function down()
		{
			Schema::table('orders', function(Blueprint $table)
			{
				$table->dropForeignId('user_id');
			});
			Schema::dropIfExists('orders');
     
		} 

}