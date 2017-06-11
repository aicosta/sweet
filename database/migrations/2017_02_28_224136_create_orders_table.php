<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('old_id')->nullable();
            $table->string('code',50);
            $table->double('total');
            $table->double('freight');
            $table->double('comission');
            $table->string('origin',15);
            $table->datetime('max_date')->nullable();
            $table->integer('order_statuses_id')->unsigned();
            $table->foreign('order_statuses_id')->references('id')->on('order_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('customers_id')->unsigned();
            $table->foreign('customers_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
            $table->text('comments_old',15)->nullable();
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
        Schema::drop('orders');
    }
}
