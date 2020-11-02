<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class suajesController extends Controller
{
    //
    public function suajes(Request $req){
        $suajes = DB::select('SELECT            sj.ancho, sj.largo,
                                                sj.dientes, sj.cav_eje, sj.cav_des,
                                                sj.especificacion, sj.id_material, sj.estatus
                            FROM suajes as sj
                            WHERE id = ?',[$req -> id]);
        return $suajes;
    }

    public function addSuajes(Request $req){
		$id = DB::table('suajes')->insertGetId(
                            [
                                'ancho'            => $req -> ancho,
                                'largo'            => $req -> largo,
                                'dientes'          => $req -> dientes,
                                'cav_eje'          => $req -> cav_eje,
                                'cav_des'          => $req -> cav_des,
                                'especificacion'   => $req -> especificacion,
                                'id_material'      => $req -> id_material,
                                'estatus'          => $req -> estatus
                            ]);
        if($id):
			return response("Se creó correctamente el detalle de la solicitud",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateSuajes($id, Request $req){
        $update = DB::update('UPDATE    suajes SET ancho=:ancho, largo=:largo, dientes=:dientes,
                                        cav_eje=:cav_eje, cav_des=:cav_des, especificacion=:especificacion,
                                        id_material=:id_material, estatus=:estatus
                            WHERE id =:id',
                            [
                                'ancho'            => $req -> ancho,
                                'largo'            => $req -> largo,
                                'dientes'          => $req -> dientes,
                                'cav_eje'          => $req -> cav_eje,
                                'cav_des'          => $req -> cav_des,
                                'especificacion'   => $req -> especificacion,
                                'id_material'      => $req -> id_material,
                                'estatus'          => $req -> estatus
                            ]);
		if($update):
			return response("El registro de Suajes se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
