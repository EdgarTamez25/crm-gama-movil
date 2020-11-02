<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DetBordados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('det_bordados', function (Blueprint $table) {
            $table->id();
            $table->integer('id_material');
            $table->integer('det_pantone');
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
