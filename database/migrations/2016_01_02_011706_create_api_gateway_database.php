<?php

//
// NOTE Migration Created: 2016-01-02 01:17:06
// --------------------------------------------------

class CreateapigatewayDatabase {
   //
   // NOTE - Make changes to the database.
   // --------------------------------------------------

   public function up()
   {

      //
      // NOTE -- category
      // --------------------------------------------------

      Schema::create('category', function($table) {
         $table->increments('id')->unsigned();
         $table->unsignedInteger('parent_category_id')->nullable()->unsigned();
         $table->string('name', 50);
         $table->unsignedInteger('order')->default("1")->unsigned();
         $table->tinyInteger('status')->default("1");
      });

      //
      // NOTE -- product
      // --------------------------------------------------

      Schema::create('product', function($table) {
         $table->increments('id')->unsigned();
         $table->unsignedInteger('supplier_id')->nullable()->unsigned();
         $table->dateTime('created_at');
         $table->string('name', 100);
         $table->string('tags', 200)->nullable();
         $table->string('meta_title', 70)->nullable();
         $table->string('meta_tags', 200)->nullable();
         $table->string('meta_description', 200)->nullable();
         $table->text('description')->nullable();
         $table->tinyInteger('featured');
         $table->tinyInteger('status')->default("1");
      });


      //
      // NOTE -- product_category
      // --------------------------------------------------

      Schema::create('product_category', function($table) {
         $table->unsignedInteger('product_id')->unsigned();
         $table->unsignedInteger('category_id')->unsigned();
      });


      //
      // NOTE -- product_sku
      // --------------------------------------------------

      Schema::create('product_sku', function($table) {
         $table->increments('id')->unsigned();
         $table->string('sku', 20)->unique();
         $table->unsignedInteger('product_id')->nullable()->unsigned();
         $table->tinyInteger('showcase');
         $table->string('supplier_ref', 20)->nullable();
         $table->float('price');
         $table->float('cost');
         $table->float('weight');
         $table->float('height');
         $table->float('width');
         $table->float('length');
         $table->unsignedInteger('stock');
         $table->unsignedInteger('stock_min')->unsigned();
         $table->tinyInteger('order');
         $table->tinyInteger('status')->default("1");
      });


      //
      // NOTE -- product_sku_attribute
      // --------------------------------------------------

      Schema::create('product_sku_attribute', function($table) {
         $table->unsignedInteger('sku_id')->unsigned();
         $table->unsignedInteger('attribute_id')->unsigned();
      });


      //
      // NOTE -- product_sku_image
      // --------------------------------------------------

      Schema::create('product_sku_image', function($table) {
         $table->unsignedInteger('sku_id')->unsigned();
         $table->string('image', 150);
         $table->tinyInteger('order');
      });


      //
      // NOTE -- product_variation
      // --------------------------------------------------

      Schema::create('product_variation', function($table) {
         $table->unsignedInteger('product_id')->unsigned();
         $table->unsignedInteger('variation_id')->unsigned();
      });


      //
      // NOTE -- suppliers
      // --------------------------------------------------

      Schema::create('suppliers', function($table) {
         $table->increments('id')->unsigned();
         $table->string('name', 60);
         $table->tinyInteger('status')->default("1");
      });


      //
      // NOTE -- variation
      // --------------------------------------------------

      Schema::create('variation', function($table) {
         $table->increments('id')->unsigned();
         $table->string('name', 150);
         $table->string('label', 30);
         $table->tinyInteger('unique');
      });


      //
      // NOTE -- variation_attribute
      // --------------------------------------------------

      Schema::create('variation_attribute', function($table) {
         $table->increments('id')->unsigned();
         $table->unsignedInteger('variation_id')->unsigned();
         $table->string('value', 30);
         $table->unsignedInteger('order')->unsigned();
         $table->tinyInteger('status')->default("1");
         $table->tinyInteger('unique');
      });


      //
      // NOTE -- category_foreign
      // --------------------------------------------------

      Schema::table('category', function($table) {
         $table->foreign('parent_category_id')->references('id')->on('category');
      });


      //
      // NOTE -- product_foreign
      // --------------------------------------------------

      Schema::table('product', function($table) {
         $table->foreign('supplier_id')->references('id')->on('suppliers');
      });


      //
      // NOTE -- product_category_foreign
      // --------------------------------------------------

      Schema::table('product_category', function($table) {
         $table->foreign('category_id')->references('id')->on('category');
         $table->foreign('product_id')->references('id')->on('product');
      });


      //
      // NOTE -- product_sku_foreign
      // --------------------------------------------------

      Schema::table('product_sku', function($table) {
         $table->foreign('product_id')->references('id')->on('product');
      });


      //
      // NOTE -- product_sku_attribute_foreign
      // --------------------------------------------------

      Schema::table('product_sku_attribute', function($table) {
         $table->foreign('attribute_id')->references('id')->on('variation_attribute');
         $table->foreign('sku_id')->references('id')->on('product_sku');
      });


      //
      // NOTE -- product_sku_image_foreign
      // --------------------------------------------------

      Schema::table('product_sku_image', function($table) {
         $table->foreign('sku_id')->references('id')->on('product_sku');
      });


      //
      // NOTE -- product_variation_foreign
      // --------------------------------------------------

      Schema::table('product_variation', function($table) {
         $table->foreign('product_id')->references('id')->on('product');
         $table->foreign('variation_id')->references('id')->on('variation');
      });


      //
      // NOTE -- variation_attribute_foreign
      // --------------------------------------------------

      Schema::table('variation_attribute', function($table) {
         $table->foreign('variation_id')->references('id')->on('variation');
      });

   }

   //
   // NOTE - Revert the changes to the database.
   // --------------------------------------------------

   public function down()
   {
      Schema::drop('category');
      Schema::drop('product');
      Schema::drop('product_category');
      Schema::drop('product_sku');
      Schema::drop('product_sku_attribute');
      Schema::drop('product_sku_image');
      Schema::drop('product_variation');
      Schema::drop('suppliers');
      Schema::drop('variation');
      Schema::drop('variation_attribute');
   }
}