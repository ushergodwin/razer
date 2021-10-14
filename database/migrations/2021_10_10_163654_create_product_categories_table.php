<?php
use System\Database\Schema\BluePrint;
use System\Database\Schema\Schema;


 class CreateProductCategoriesTable
 { 
		/**
		* Run the Migrations
		*
		* @return void
		*/ 
		public function up()
		{

			Schema::create('product_categories', function (BluePrint $table) {

				$table->bigIncrements('id');
				$table->string('name');
				$table->timestamps();
				$table->softDeletes();
				$table->engine(Schema::InnoDB);
			}); 

		}


		public function alter()
		{
			Schema::modify('product_categories', function (BluePrint $table){
				$table->addColumn('category_size', 'varchar', 20)->after('name');
				$table->addColumn('category_owner', 'int', 10)->after('category_size');
				$table->dropColumn('name');
				$table->renameColumn('created_at', 'created_on', 'DATETIME', null, true);
			});
		}

		/**
		* Reverse the migrations.
		*
		* @return void
		*/

		public function down()
		{

			Schema::dropIfExists('product_categories');
     
		} 

}