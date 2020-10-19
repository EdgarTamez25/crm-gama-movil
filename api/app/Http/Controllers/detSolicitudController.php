<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detSolicitudController extends Controller
{
    public function detSolicitud(Request $req){
        $detSolicitud = DB::select('SELECT      ds.id, ds.id_solicitud, ds.dx,
                                                ds.id_dx, ds.referencia, ds.tipo
                                    FROM        det_Solicitud as ds
                                    WHERE id = ?',[$req -> id]);
        return $detSolicitud;
    }

    public function addDetSolicitud(Request $req){
		$id = DB::table('det_solicitud')->insertGetId(
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx'           => $req -> dx,
                                'id_dx'        => $req -> id_dx,
                                'referencia'   => $req -> referencia,
                                'tipo'         => $req -> tipo,
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
