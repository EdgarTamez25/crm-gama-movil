<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detOffsetController extends Controller
{
    //
    public function detOffset(Request $req){
        $solicitud = DB::select('SELECT     ofs.id, ofs.id_material, ofs.folio1, ofs.folio2,
                                            ofs.id_suaje, ofs.id_pleca, ofs.det_pantone, ofs.ancho, ofs.largo
                                FROM        det_offset ofs
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addDetOffset(Request $req){
		$id = DB::table('det_offset')->insertGetId(
                            [
                            'id_material'       => $req -> id_material,
                            'folio1'            => $req -> folio1,
                            'folio2'            => $req -> folio2,
                            'id_suaje'          => $req -> id_suaje,
                            'id_pleca'          => $req -> id_pleca,
                            'det_pantone'       => $req -> det_pantone,
                            'ancho'             => $req -> ancho,
                            'largo'             => $req -> largo,
                            ]
                        );
        if($id):
			return response("Se creó correctamente el detalle de Offset",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetOffset($id, Request $req){
        $update = DB::update('UPDATE    det_offset SET id_material=:id_material,folio1=:folio1,
                                                        folio2=:folio2,id_suaje=:id_suaje,id_pleca=:id_pleca,
                                                        det_pantone=:det_pantone,ancho=:ancho, largo=:largo
                                        WHERE id =:id',
                                            [
                                                'id_material'       => $req -> id_material,
                                                'folio1'            => $req -> folio1,
                                                'folio2'            => $req -> folio2,
                                                'id_suaje'          => $req -> id_suaje,
                                                'id_pleca'          => $req -> id_pleca,
                                                'det_pantone'       => $req -> det_pantone,
                                                'ancho'             => $req -> ancho,
                                                'largo'             => $req -> largo,
                                            ]);
		if($update):
			return response("El detalle de Offset se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
