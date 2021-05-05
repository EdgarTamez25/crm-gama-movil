<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL

class ordenesTrabajoController extends Controller
{
    // !CATALOGO DE ORDENES TRABAJO
	public function OrdenesTrabajoVend(Request $req){
		$OT = DB::select('SELECT ot.id, ot.id_cliente, c.nombre as nomcli, ot.id_solicitud,  ot.oc,
														 ot.fecha, ot.hora, ot.estatus, ot.id_solicitante , u.nombre as solicitante,
														 ot.id_creador, us.nombre as creador
												FROM ot LEFT JOIN clientes c ON ot.id_cliente     = c.id
																LEFT JOIN users    u ON ot.id_solicitante = u.id
																LEFT JOIN users   us ON ot.id_creador     = us.id
											WHERE ot.id_solicitante = ? AND ot.estatus  = ? AND ot.fecha BETWEEN ? AND ?
											ORDER BY ot.estatus ASC',[ $req -> id_solicitante, $req -> estatus, $req -> fecha1, $req -> fecha2 ]);
		return $OT ? $OT: $OT = [];
	}

	public function DetalleOT($id){
		$detalleOT = DB::select('SELECT d.id, d.id_ot, d.id_producto, p.codigo as producto, d.cantidad, d.fecha_progra, d.fecha_entrega,
																		d.concepto, d.urgencia, d.razon, d.estatus 
																FROM det_ot d LEFT JOIN prodxcli p ON d.id_producto = p.id
														 WHERE d.id_ot = ?', [ $id]);
		return $detalleOT ? $detalleOT : [];
	}
	

}
