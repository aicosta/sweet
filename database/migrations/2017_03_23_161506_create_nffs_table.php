<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->integer('number');
            $table->integer('serie');
            $table->string('document');
            $table->string('documen2');
            $table->string('name');
            $table->string('signature');
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
        Schema::drop('nffs');
    }
}
