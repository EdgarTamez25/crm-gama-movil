<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetSerigrafia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
            Schema::create('det_serigrafia', function (Blueprint $table){
            $table->id();
            $table->integer('id_material');
            $table->string('det_pantone');
            $table->string('ancho');
            $table->string('largo');
            $table->integer('id_orientacion');
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
