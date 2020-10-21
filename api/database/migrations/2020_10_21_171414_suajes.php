<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Suajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suajes', function (Blueprint $table){
            $table->id();
            $table->string('ancho');
            $table->string('largo');
            $table->integer('dientes');
            $table->integer('cav_eje');
            $table->integer('cav_des');
            $table->string('especificacion');
            $table->integer('id_material');
            $table->integer('estatus');
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
        //
    }
}
