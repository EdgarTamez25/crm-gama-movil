<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetOffset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('det_offset', function (Blueprint $table) {
            $table->id();
            $table->integer('id_material');
            $table->integer('folio1');
            $table->integer('folio2');
            $table->integer('id_suaje');
            $table->integer('id_pleca');
            $table->integer('det_pantone');
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
