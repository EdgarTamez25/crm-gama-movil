<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detPantone extends Controller
{
//
    public function detPantone(Request $req){
        $solicitud = DB::select('SELECT     dp.id, dp.id_dx, dp.pantone
                                FROM  det_pantone dp
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addDetPantone(Request $req){
		$id = DB::table('det_pantone')->insertGetId(
                            [
                                'id_dx' => $req -> id_dx,
                                'id_pantone' => $req -> id_pantone,
                            ]);
        if($id):
			return response("El detalle de registro de Pantone se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }


	public function UpdateDetPantone($id, Request $req){
        $update = DB::update('UPDATE   det_pantone SET id_dx=:id_dx, pantone=:pantone
                                        WHERE id =:id',
                                            [
                                                'id_dx'	     => $req -> id_dx,
                                                'pantone'	 => $req -> id_pantone
                                            ]);
		if($update):
			return response("El detalle de registro de Pantone se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
