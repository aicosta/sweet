<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('orders_id')->unsigned();
            $table->foreign('orders_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('shipping_companies_id')->unsigned();
            $table->foreign('shipping_companies_id')->references('id')->on('shipping_companies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('weight');
            $table->string('shipping_code');
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
        Schema::drop('shippings');
    }
}
