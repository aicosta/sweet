<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFiscalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fiscals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('products_id')->unsigned();
            $table->foreign('products_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
            $table->string('sku',30);
            $table->string('name',120);
            $table->string('ean',13);
            $table->string('ncm',10);
            $table->string('isbn',13)->nullable();
            $table->integer('origin');
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
        Schema::drop('product_fiscals');
    }
}
