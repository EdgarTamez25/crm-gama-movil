<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetDigital extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('det_digital', function (Blueprint $table) {
            $table->id();
            $table->integer('id_material');
            $table->integer('det_sobre');
            $table->integer('det_pantone');
            $table->integer('det_acabados');
            $table->integer('estructura');
            $table->string('grosor');
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
