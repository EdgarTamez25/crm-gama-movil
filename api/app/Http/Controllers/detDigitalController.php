<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detDigitalController extends Controller
{
    //
    public function detDigital(Request $req){
        $solicitud = DB::select('SELECT     dg.id_material, dg.det_sobre, dg.det_pantone,
                                            dg.det_acabados, dg.estructura, dg.grosor,
                                            dg.ancho, dg.largo
                                FROM        det_digital dg
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addDetDigital(Request $req){
		$id = DB::table('det_digital')->insertGetId(
                            [
                            'id_material'       => $req -> id_material,
                            'det_sobre'         => $req -> det_sobre,
                            'det_pantone'       => $req -> det_pantone,
                            'det_acabados'      => $req -> det_acabados,
                            'estructura'        => $req -> estructura,
                            'grosor'            => $req -> grosor,
                            'ancho'             => $req -> ancho,
                            'largo'             => $req -> largo,
                            ]
                        );
        if($id):
			return response("Se creó correctamente el detalle de Digital",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetDigital($id, Request $req){
        $update = DB::update('UPDATE    det_digital SET  id_material=:id_material, det_sobre=:det_sobre,
                                                            det_pantone=:det_pantone,det_acabados=:det_acabados,
                                                            estructura=:estructura,grosor=:grosor,ancho=:ancho, largo=:largo
                                        WHERE id =:id',
                                            [
                                                'id_material'       => $req -> id_material,
                                                'det_sobre'         => $req -> det_sobre,
                                                'det_pantone'       => $req -> det_pantone,
                                                'det_acabados'      => $req -> det_acabados,
                                                'estructura'        => $req -> estructura,
                                                'grosor'            => $req -> grosor,
                                                'ancho'             => $req -> ancho,
                                                'largo'             => $req -> largo
                                            ]);
		if($update):
			return response("El detalle de Digital se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
