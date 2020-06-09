<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial extends Model
{
    protected $table = "historial";
    protected $fillable = array('id_compromiso',
                                'fecha',
                                'hora',
                                'fase_venta',
                                ); 
}