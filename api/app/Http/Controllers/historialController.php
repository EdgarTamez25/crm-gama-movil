<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL


class historialController extends Controller {

	public function VerResumen($id){
		$Resumen =[];
		if(	$compromisos = DB::select('SELECT c.id,c.fase_venta, cli.nombre FROM compromisos c
																		LEFT JOIN clientes cli ON c.id_cliente = cli.id 
																	WHERE id_vendedor =? AND c.fase_venta < 8  ORDER BY c.fecha DESC , c.hora DESC',[$id]) ):

			for($i=0 ; $i< count($compromisos) ; $i++):
				$id_compromiso = $compromisos[$i] -> id;
				$fase_venta	   = $compromisos[$i] -> fase_venta;
				$nomcli	       = $compromisos[$i] -> nombre;

				$historial = DB::select('SELECT id_compromiso,fase_venta, fecha, hora, aceptado , obscierre
																 FROM historial WHERE id_compromiso=?',[$id_compromiso]);
				$contenedor = ["id_compromiso" => $id_compromiso, 
											"fase_venta"     => $fase_venta,
											"nomcli"     		 => $nomcli,
							   			 "historial" 		 => $historial ]; //FORMO OBJECT 
				array_push($Resumen, $contenedor);
			endfor;
			return $Resumen;
		endif;
	}

	public function resumenCompromiso($id){
		$resumen = DB::select('SELECT id,fase_venta, fecha, hora, aceptado , obscierre
															FROM historial WHERE id_compromiso=?',[$id]);
			return $resumen;
		}
	
}
