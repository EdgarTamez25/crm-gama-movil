<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class suajesController extends Controller
{
    //
    public function suajes(Request $req){
        $suajes = DB::select('SELECT            sj.suaje, sj.ancho_inch, sj.largo_inch,
                                                sj.dientes, sj.cav_eje, sj.cav_des,
                                                sj.especificaciones, sj.tipo_material,
                                                sj.ancho_material, sj.estatus
                            FROM suajes as sj
                            WHERE id = ?',[$req -> id]);
        return $suajes;
    }

    public function addSuajes(Request $req){
		$id = DB::table('suajes')->insertGetId(
                            [
                                'suaje'            => $req -> suaje,
                                'ancho_inch'       => $req -> ancho_inch,
                                'largo_inch'       => $req -> largo_inch,
                                'dientes'          => $req -> dientes,
                                'cav_eje'          => $req -> cav_eje,
                                'cav_des'          => $req -> cav_des,
                                'especificaciones' => $req -> especificaciones,
                                'tipo_material'    => $req -> tipo_material,
                                'ancho_material'   => $req -> anchor_material,
                                'estatus'          => $req -> estatus
                            ]);
        if($id):
			return response("Se creó correctamente el detalle de la solicitud",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateSuajes($id, Request $req){
        $update = DB::update('UPDATE    suajes SET suaje=:suaje, ancho_inch=:ancho_inch, largo_inch=:largo_inch, dientes=:dientes,
                                        cav_eje=:cav_eje, cav_des=:cav_des, especificaciones=:especificaciones,
                                        tipo_material=:tipo_material, ancho_material=:ancho_material, estatus=:estatus
                            WHERE id =:id',
                            [
                                'id'               => $id,
                                'suaje'            => $req -> suaje,
                                'ancho_inch'       => $req -> ancho_inch,
                                'largo_inch'       => $req -> largo_inch,
                                'dientes'          => $req -> dientes,
                                'cav_eje'          => $req -> cav_eje,
                                'cav_des'          => $req -> cav_des,
                                'especificaciones' => $req -> especificaciones,
                                'tipo_material'    => $req -> tipo_material,
                                'ancho_material'   => $req -> ancho_material,
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
