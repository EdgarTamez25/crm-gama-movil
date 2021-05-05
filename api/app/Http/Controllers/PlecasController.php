<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class PlecasController extends Controller
{
    //
    public function plecas(Request $req){
        $Plecas = DB::select('SELECT        pls.id, pls.suaje, pls.dientes, pls.cav_eje,
                                            pls.cav_des, pls.estatus
                              FROM    plecas as pls
                              WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addPlecas(Request $req){
		$id = DB::table('plecas')->insertGetId(
                            [
                                'suaje'   => $req -> suaje,
                                'dientes' => $req -> dientes,
                                'cav_eje' => $req -> cav_eje,
                                'cav_des' => $req -> cav_des,
                                'estatus' => $req -> estatus
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro de Pleca",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function Updateplecas($id, Request $req){
        $update = DB::update('UPDATE    plecas SET suaje=:suaje, dientes=:dientes, cav_eje=:cav_eje,
                                        cav_des=:cav_des, estatus=:estatus
                            WHERE id =:id',
                            [
                                'id'      => $id,
                                'suaje'   => $req -> suaje,
                                'dientes' => $req -> dientes,
                                'cav_eje' => $req -> cav_eje,
                                'cav_des' => $req -> cav_des,
                                'estatus' => $req -> estatus
                            ]);
		if($update):
			return response("El registro de la Pleca se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
