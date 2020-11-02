<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class uvController extends Controller
{
    //
    public function UV(Request $req){
        $uv = DB::select('SELECT            u.id, u.id_material, u.ancho,
                                            u.largo, u.id_detpantone
                                FROM        uv as u
                                WHERE id = ?',[$req -> id]);
        return $uv;
    }
    public function addUV(Request $req){
		$id = DB::table('uv')->insertGetId(
                            [
                                'id_material'   => $req -> id_material,
                                'ancho'         => $req -> ancho,
                                'largo'         => $req -> largo,
                                'id_detpantone' => $req -> id_detpantone
                            ]
                        );
        if($id):
			return response("El registro de UV se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }
	public function UpdateUV($id, Request $req){
        $update = DB::update('UPDATE    uv SET id_material=:id_material, ancho=:ancho,
                                        largo=:largo, id_detpantone=:id_detpantone
                                        WHERE id =:id',
                                            [
                                                'id_material'   => $req -> id_material,
                                                'ancho'         => $req -> ancho,
                                                'largo'         => $req -> largo,
                                                'id_detpantone' => $req -> id_detpantone
                                            ]);
		if($update):
			return response("lEl registro de UV se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
