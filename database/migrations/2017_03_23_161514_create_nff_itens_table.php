<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNffItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nff_itens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('ean',13);
            $table->string('name');
            $table->string('ncm');
            $table->string('cfop');
            $table->string('quantity');
            $table->string('price');
            $table->string('discount');
            $table->boolean('finded')->default(0);
            $table->integer('used')->default(0);
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
        Schema::drop('nff_itens');
    }
}
