<?php
use System\Database\Schema\BluePrint;
use System\Database\Schema\Schema;


 class CreateProductsTable
 { 
		/**
		* Run the Migrations
		*
		* @return void
		*/ 
		public function up()
		{

			Schema::create('products', function (BluePrint $table) {

				$table->bigIncrements('id');
				$table->string('name');
				$table->string('price');
				$table->text('description');
				$table->text('image');
				$table->string('size',255);
				$table->integer('discount')->nullable();
				$table->boolean('is_out_of_stock')->nullable()->default(false);
				$table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
				$table->timestamps();
				$table->softDeletes();
			}); 

		}

		public function alter()
		{
			Schema::modify('products', function (BluePrint $table){
				$table->renameColumn('is_out_of_stock', 'stock', 'int', 1, false, 0);
			});
		}

		/**
		* Reverse the migrations.
		*
		* @return void
		*/

		public function down()
		{
			Schema::table('products', function(BluePrint $table){
				$table->dropConstrainedForeignId('category_id');
			});

			Schema::dropIfExists('products');
     
		} 

}