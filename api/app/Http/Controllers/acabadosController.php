<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL


class acabadosController extends Controller
{
    public function Acabados(Request $req){
        $Plecas = DB::select('SELECT  ac.nombre, ac.dx, ac.estatus
                              FROM    acabados as ac
                              WHERE id = ?',[$req -> id]);
        return $Plecas;
    }
}
