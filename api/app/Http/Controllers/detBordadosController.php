<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detBordadosController extends Controller
{
    //
    public function detBordados(Request $req){
        $bordados = DB::select('SELECT      b.id, b.id_material, b.det_pantone,
                                            b.id_orientacion, b.ancho, b.largo
                                FROM        det_bordados b
                                WHERE id = ?',[$req -> id]);
        return $bordados;
    }

    public function addDetBordados(Request $req){
		$id = DB::table('det_bordados')->insertGetId(
                            [
                                'id_material' => $req -> id_material,
                                'det_pantone' => $req -> det_pantone,
                                'id_orientacion' => $req -> id_orientacion,
                                'ancho' => $req -> ancho,
                                'largo' => $req -> largo
                            ]
                        );
        if($id):
			return response("El registro del Bordado se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetBordados($id, Request $req){
        $update = DB::update('UPDATE    det_bordados SET id_material=:id_material, det_pantone=:det_pantone,
                                        id_orientacion =:id_orientacion, ancho=:ancho, largo=:largo
                                        WHERE id =:id',
                                            [
                                                'id_material' => $req -> id_material,
                                                'det_pantone' => $req -> det_pantone,
                                                'id_orientacion' => $req -> id_orientacion,
                                                'ancho' => $req -> ancho,
                                                'largo' => $req -> largo
                                            ]);
		if($update):
			return response("El detalle de Bordados se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }

}
