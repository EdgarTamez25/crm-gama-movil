<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetFlexo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('det_flexo', function (Blueprint $table) {
            $table->id();
            $table->integer('id_material');
            $table->integer('id_detacabados');
            $table->integer('id_detpantone');
            $table->integer('etqxrollo');
            $table->integer('etqxpaso');
            $table->string('med_nucleo');
            $table->string('med_desarrollo');
            $table->string('med_eje');
            $table->integer('id_orientacion');
            $table->string('ancho');
            $table->string('largo');
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
