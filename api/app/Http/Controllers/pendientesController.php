<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
class pendientesController extends Controller
{
    public function PendientesxVend(Request $request){
		$pendientes = DB::select('SELECT c.id, c.id_vendedor, u.nombre as nomvend, c.tipo, c.id_categoria, ca.nombre as nomcatego,
										 c.fecha, c.hora, c.id_cliente, c.fecha_cierre, c.hora_cierre, cli.nombre as nomcli, cli.tel1, c.obs,
										 u.nombre as nomuser, c.obs_usuario, c.cumplimiento, c.confirma_cita
                                         FROM compromisos c LEFT JOIN users u       ON u.id   = c.id_vendedor
                                                            LEFT JOIN categorias ca ON ca.id  = c.id_categoria
                                                            LEFT JOIN clientes  cli ON cli.id = c.id_cliente

                                         WHERE c.id_vendedor=? AND c.fecha >= ?', [$request -> id_vendedor, $request -> fecha]);
                                         return $pendientes;
    }
}
