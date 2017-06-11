<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('document',20);
            $table->string('document2',20);
            $table->string('zip_code',9);
            $table->string('address');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('quarter');
            $table->string('reference');
            $table->string('city');
            $table->string('state',2);
            
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
        Schema::drop('shipping_companies');
    }
}
