<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class pendientesController extends Controller
{
    public function PendientesxVend(Request $request){
		$pendientes = DB::select('SELECT c.id, c.id_vendedor, v.nombre as nomvend, c.tipo_compromiso, c.id_categoria, ca.nombre as nomcatego,
																			c.fecha, c.hora, c.fecha_fin, c.hora_fin, c.id_cliente,cli.nombre as nomcli, cli.tel1, c.comentarios, c.fase_venta, 
																			c.id_usuario, u.nombre as nomuser, c.obs_usuario, c.cumplimiento, c.estatus, c.confirma_cita
																FROM compromisos c LEFT JOIN users v   	   ON v.id   = c.id_vendedor
																									 LEFT JOIN categorias ca ON ca.id  = c.id_categoria
																									 LEFT JOIN clientes  cli ON cli.id = c.id_cliente
																									 LEFT JOIN users u       ON u.id   = c.id_usuario
															WHERE c.id_vendedor=? AND c.fecha >= ?', [$request -> id_vendedor, $request -> fecha]);

		return $pendientes;

	}
}
