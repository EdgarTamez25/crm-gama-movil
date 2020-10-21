<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlecasController extends Controller
{
    //
    public function Plecas(Request $req){
        $Plecas = DB::select('SELECT        pls.id, ds.dientes, pls.cav_eje,
                                            pls.cav_des, pls.estatus,
                                    FROM    plecas as pls
                                    WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addDetSolicitud(Request $req){
		$id = DB::table('det_solicitud')->insertGetId(
                            [
                                'dientes' => $req -> dientes,
                                'cav_eje'           => $req -> cav_eje,
                                'cav_des'        => $req -> cav_des,
                                'estatus'   => $req -> estatus,
                            ]
                        );
        if($id):
			return response("Se creó correctamente el detalle de la solicitud",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetSolicitud($id, Request $req){
        $update = DB::update('UPDATE    det_solicitud SET id_solicitud=:id_solicitud, dx=:dx,
                                        id_dx=:id_dx, referencia=:referencia, tipo=:tipo,
                            WHERE id =:id',
                            [
                                'id_solicitud'	 => $req -> id_solicitud,
                                'dx'	         => $req -> dx,
                                'id_dx'		     => $req -> id_dx,
                                'referencia'     => $req -> referencia,
                                'tipo'           => $req -> tipo,
                            ]);
		if($update):
			return response("El detalle de la solicitud se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
