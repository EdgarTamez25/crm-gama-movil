<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class detFlexografiaController extends Controller
{
    //
    public function detFlexografia(Request $req){
        $solicitud = DB::select('SELECT     df.id_material, df.id_detacabados, df.id_detpantone,
                                            df.etqxrollo, df.etqxpaso, df.med_desarrollo, df.med_eje,
                                            df.id_orientacion, df.ancho, df.largo
                                FROM        det_flexo as df
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addDetFlexo(Request $req){
		$id = DB::table('det_flexo')->insertGetId(
                            [
                                'id_material'	     => $req -> id_material,
                                'id_detacabados'	 => $req -> id_detacabados,
                                'id_detpantone'		 => $req -> id_detpantone,
                                'etqxrollo'		     => $req -> etqxrollo,
                                'etqxpaso'           => $req -> etqxpaso,
                                'med_nucleo'         => $req -> med_nucleo,
                                'med_desarrollo'  	 => $req -> med_desarrollo,
                                'med_eje'  		     => $req -> med_eje,
                                'id_orientacion'     => $req -> id_orientacion,
                                'ancho'  		     => $req -> ancho,
                                'largo'  		     => $req -> largo
                            ]
                        );
        if($id):
			return response("El detalle de Flexografia se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateDetFlexo($id, Request $req){
        $update = DB::update('UPDATE    det_flexo SET   id_material=:id_material, id_detacabados=:id_detacabados,
                                                        id_detpantone=:id_detpantone, etqxrollo=:etqxrollo, etqxpaso=:etqxpaso,
                                                        med_desarrollo=:med_desarrollo, med_eje=:med_eje, id_orientacion=:id_orientacion,
                                                        ancho=:ancho, largo=:largo
                                        WHERE id =:id',
                                            [
                                                'id_material'	     => $req -> id_material,
                                                'id_detacabados'	 => $req -> id_detacabados,
                                                'id_detpantone'		 => $req -> id_detpantone,
                                                'etqxrollo'		     => $req -> etqxrollo,
                                                'etqxpaso'           => $req -> etqxpaso,
                                                'med_desarrollo'  	 => $req -> med_desarrollo,
                                                'med_eje'  		     => $req -> med_eje,
                                                'id_orientacion'     => $req -> id_orientacion,
                                                'ancho'  		     => $req -> ancho,
                                                'largo'  		     => $req -> largo
                                            ]);
		if($update):
			return response("El detalle de Flexografia se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
