<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
// use App\compromisos; // USO DEL MODELO.

class compromisosController extends Controller
{
	public function CompromisosxVend(Request $request){
		$compromisos = DB::select('SELECT c.id, c.id_vendedor, v.nombre as nomvend, c.tipo_compromiso, c.id_categoria, ca.nombre as nomcatego, c.enruta,
																			c.fecha, c.hora, c.fecha_fin, c.hora_fin, c.id_cliente,cli.nombre as nomcli, cli.tel1, c.comentarios, c.fase_venta, 
																			c.id_usuario, u.nombre as nomuser, c.obs_usuario, c.cumplimiento, c.estatus, c.confirma_cita
																FROM compromisos c LEFT JOIN users v   	   ON v.id   = c.id_vendedor
																									 LEFT JOIN categorias ca ON ca.id  = c.id_categoria
																									 LEFT JOIN clientes  cli ON cli.id = c.id_cliente
																									 LEFT JOIN users u       ON u.id   = c.id_usuario
															WHERE c.id_vendedor =  ? AND c.fecha = ? AND c.enruta != 1', [$request -> id_vendedor, $request -> fecha]);

		return $compromisos;

	}

	public function Reagendar(Request $req){
		$reagendacion = DB::update('UPDATE compromisos SET fecha=:fecha, hora=:hora WHERE id=:id',
																	['fecha' => $req -> fecha, 'hora' => $req -> hora, 'id'=> $req -> id]);
		return "Reagendado correctamente.";
	}

	public function ConfirmarCita(Request $req){
		$reagendacion = DB::update('UPDATE compromisos SET confirma_cita=:confirma_cita WHERE id=:id',
																	['confirma_cita' => $req -> confirma_cita, 'id'=> $req -> id]);

		return "Cita confirmada correctamente.";
	}

	public function EnRuta(Request $req){
		$enRuta = DB::update('UPDATE compromisos SET enruta=:enruta WHERE id=:id',
														['enruta' => $req -> enruta, 'id' => $req -> id ]);
		return "Compromiso en Ruta";
	}

}
