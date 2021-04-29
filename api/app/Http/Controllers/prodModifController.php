<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class prodModifController extends Controller
{
    //

    public function prodModif(Request $req){
        $Plecas = DB::select('SELECT        pm.id, pm.id_solicitud, pm.dx,
                                            pm.ft, pm.cantidad, pm.tipo_prod
                            FROM    prod_modif as pm
                            WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addProdModif(Request $req){
		$id = DB::table('prod_modif')->insertGetId(
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'ft' => $req -> ft,
                                'cantidad' => $req -> cantidad,
                                'tipo_prod' => $req -> tipo_prod
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro de producto a modificar",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateProdModif($id, Request $req){
        $update = DB::update('UPDATE    prod_modif SET  id_solicitud=:id_solicitud, dx=:dx,
                                        ft=:ft, cantidad=:cantidad,tipo_prod=:tipo_prod
                            WHERE id =:id',
                            [
                                'id_solicitud' => $req -> id_solicitud,
                                'dx' => $req -> dx,
                                'ft' => $req -> ft,
                                'cantidad' => $req -> cantidad,
                                'tipo_prod' => $req -> tipo_prod
                            ]);
		if($update):
			return response("El registro de producto a modificar editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
