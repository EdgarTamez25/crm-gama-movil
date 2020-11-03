<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class prodNuevoController extends Controller
{
    //
    //
    public function prodNuevo(Request $req){
        $Plecas = DB::select('SELECT        pn.id, pn.id_solicitud, pn.dx,
                                            pn.ft, pn.tipo_prod
                            FROM    prod_nuevo as pn
                            WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addProdNuevo(Request $req){
		$id = DB::table('prod_nuevo')->insertGetId(
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'id_dx' => $req -> id_dx,
                                'ft' => $req -> ft,
                                'tipo_prod' => $req -> tipo_prod
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro de producto nuevo",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateProdNuevo($id, Request $req){
        $update = DB::update('UPDATE    prod_nuevo SET  id_solicitud=:id_solicitud,
                                                        dx=:dx, ft=:ft, tipo_prod=:tipo_prod
                            WHERE id =:id',
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'id_dx' => $req -> id_dx,
                                'ft' => $req -> ft,
                                'tipo_prod' => $req -> tipo_prod
                            ]);
		if($update):
			return response("El registro de producto nuevo editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
