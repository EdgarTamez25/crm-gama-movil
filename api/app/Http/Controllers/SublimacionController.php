<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class SublimacionController extends Controller
{
    //
    public function sublimacion(Request $req){
        $sublimacion = DB::select('SELECT      s.id, s.id_material, s.tamanio
                                    FROM        sublimacion s
                                    WHERE id = ?',[$req -> id]);
        return $sublimacion;
    }

    public function addSublimacion(Request $req){
		$id = DB::table('sublimacion')->insertGetId(
                            [
                                'id_material' => $req -> id_material,
                                'tamanio' => $req -> tamanio
                            ]
                        );
        if($id):
			return response("El registro de Sublimacion se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

    public function UpdateSublimacion($id, Request $req){
        $update = DB::update('UPDATE    sublimacion SET id_material=:id_material, tamanio=:tamanio
                                        WHERE id =:id',
                                            [
                                                'id_material' => $req -> id_material,
                                                'tamanio' => $req -> tamanio,
                                            ]);
		if($update):
			return response("El registro de Sublimacion se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
