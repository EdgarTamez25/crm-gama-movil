<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL

class ordenesTrabajoController extends Controller
{
    public function OrdenesTrabajoVend(Request $req){
			
			$ordenes = DB::select('SELECT ot.id, ot.id_depto, ot.id_vendedor, ot.id_cliente, c.nombre as nomcli, ot.oc, ot.referencia,
																		ot.fecha, ot.hora, ot.estatus 
															FROM ot LEFT JOIN clientes c ON ot.id_cliente = c.id
														WHERE ot.id_vendedor = ? AND ot.estatus=? AND ot.fecha BETWEEN ? AND ? ORDER BY ot.estatus ASC' ,
														[$req -> id_vendedor, $req -> estatus, $req -> fecha1 , $req -> fecha2 ]);
													
			return $ordenes ? $ordenes: [];
		}
		
		public function DetalleOT($id){
			$detalleOT = DB::select('SELECT d.id, d.id_ot, d.id_producto, p.codigo, d.cantidad, d.fecha_progra, d.fecha_entrega,
																			d.concepto, d.urgencia, d.razon, d.estatus 
																	FROM det_ot d LEFT JOIN prodxcli p ON d.id_producto = p.id
															 WHERE d.id_ot = ?', [ $id]);
			return $detalleOT ? $detalleOT : [];
		}

}
