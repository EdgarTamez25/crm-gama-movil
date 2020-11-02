<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detSerigrafiaController extends Controller
{
    //
    public function detSerigrafia(Request $req){
        $solicitud = DB::select('SELECT     sg.id, sg.id_material, sg.det_pantone,
                                            sg.ancho, sg.largo, sg.id_orientacion
                                FROM        det_serigrafia sg
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addDetSerigrafia(Request $req){
		$id = DB::table('det_serigrafia')->insertGetId(
                            [
                            'id_material'      => $req -> id_material,
                            'det_pantone'      => $req -> det_pantone,
                            'ancho'            => $req -> ancho,
                            'largo'            => $req -> largo,
                            'id_orientacion'   => $req -> id_orientacion,
                            ]
                        );
        if($id):
			return response("Se creó correctamente el detalle de Serigrafía",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetSerigrafia($id, Request $req){
        $update = DB::update('UPDATE    det_Serigrafia SET id_material=:id_material, det_pantone=:det_pantone,
                                        ancho=:ancho, largo=:largo
                                        WHERE id =:id',
                                            [
                                            'id_material' => $req -> id_material,
                                            'det_pantone' => $req -> det_pantone,
                                            'ancho'      => $req -> ancho,
                                            'largo'       => $req -> largo,
                                            ]);
		if($update):
			return response("El detalle de Serigrafia se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
