<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku',30);
            $table->string('name',120);
            $table->text('description');
            $table->text('short_description');
            
            $table->integer('providers_id')->unsigned();
            $table->foreign('providers_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('brand',30);
            $table->integer('lead_time');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products');
    }
}
