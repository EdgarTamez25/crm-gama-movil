<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class det_SolController extends Controller
{
    //
    public function detSol(Request $req){
        $Plecas = DB::select('SELECT        ds.id, ds.id_solicitud, ds.px,
                                            ds.id_px
                            FROM    det_sol as ds
                            WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addDetSol(Request $req){
		$id = DB::table('det_sol')->insertGetId(
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'px' => $req -> px,
                                'id_px' => $req -> id_px,
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro de detalle de solicitud",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetSol($id, Request $req){
        $update = DB::update('UPDATE    det_sol SET     id_solicitud=:id_solicitud,
                                                        px=:px, id_px=:id_px
                            WHERE id =:id',
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'px' => $req -> px,
                                'id_px' => $req -> id_px,
                            ]);
		if($update):
			return response("El registro de detalle de solicitud se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
