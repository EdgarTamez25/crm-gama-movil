<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dxModifController extends Controller
{
    //
    public function dxModif(Request $req){
        $Plecas = DB::select('SELECT        dxm.id, dxm.id_prod_modif, dxm.concepto,
                                            dxm.valor
                            FROM    dx_modif as dxm
                            WHERE id = ?',[$req -> id]);
        return $Plecas;
    }

    public function addDxModif(Request $req){
		$id = DB::table('dx_modif')->insertGetId(
                            [
                                'id_prod_modif' => $req -> id_prod_modif,
                                'concepto' => $req -> concepto,
                                'valor' => $req -> valor
                            ]
                        );
        if($id):
			return response("Se creó correctamente el registro",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDxModif($id, Request $req){
        $update = DB::update('UPDATE    dx_modif SET  id_prod_modif=:id_prod_modif,
                                                        concepto=:concepto, valor=:valor
                            WHERE id =:id',
                            [
                                'id_prod_modif' => $req -> id_prod_modif,
                                'concepto' => $req -> concepto,
                                'valor' => $req -> valor,
                            ]);
		if($update):
			return response("El registro se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
