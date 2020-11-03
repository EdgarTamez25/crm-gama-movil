<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class prodExistController extends Controller
{
    //
    public function prodExist(Request $req){
        $Plecas = DB::select('SELECT        pe.id, pe.id_solicitud, pe.dx,
                                            pe.ft, pe.cantidad, pe.tipo_prod
                            FROM    prod_exist as pe
                            WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addProdExist(Request $req){
		$id = DB::table('prod_exist')->insertGetId(
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'ft' => $req -> ft,
                                'cantidad' => $req -> cantidad,
                                'tipo_prod' => $req -> tipo_prod
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro de producto existente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateProdExist($id, Request $req){
        $update = DB::update('UPDATE    prod_exist SET  id_solicitud=:id_solicitud,
                                                        dx=:dx, ft=:ft, tipo_prod=:tipo_prod
                            WHERE id =:id',
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'ft' => $req -> ft,
                                'cantidad' => $req -> cantidad,
                                'tipo_prod' => $req -> tipo_prod
                            ]);
		if($update):
			return response("El registro de producto existente editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
