<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class solicitudesController extends Controller
{
    //
    public function Solicitudes(Request $req){
        $solicitud = DB::select('SELECT     s.id, s.id_cliente, s.id_usuario,
                                            s.fecha, s.hora, s.urgencia, s.nota
                                FROM        solicitudes s
                                WHERE id = ?',[$req -> id]);
        return $solicitud;
    }

    public function addSolicitud(Request $req){
		$id = DB::table('solicitudes')->insertGetId(
                            [
                                'id_cliente' => $req -> id_cliente,
                                'id_usuario' => $req -> id_usuario,
                                'fecha'      => $req -> fecha,
                                'hora'       => $req -> hora,
                                'urgencia'   => $req -> urgencia,
                                'nota'       => $req -> nota,
                            ]
                        );
        if($id):
			return response("La solicitud se creó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
    }

	public function UpdateSolicitud($id, Request $req){
        $update = DB::update('UPDATE    solicitudes SET id_cliente=:id_cliente, id_usuario=:id_usuario,
                                        fecha=:fecha, hora=:hora, urgencia=:urgencia, nota=:nota
                                        WHERE id =:id',
                                            [
                                                'id_cliente'	 => $req -> id_cliente,
                                                'id_usuario'	 => $req -> id_usuario,
                                                'fecha'		     => $req -> fecha,
                                                'hora'		     => $req -> hora,
                                                'urgencia'       => $req -> urgencia,
                                                'nota'  		 => $req -> nota,
                                            ]);
		if($update):
			return response("la solicitud se editó correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
        return "editado correctamente.";
    }
}
