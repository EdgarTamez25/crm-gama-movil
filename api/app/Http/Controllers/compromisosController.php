<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
// use App\compromisos; // USO DEL MODELO.
use App\historial;

class compromisosController extends Controller
{

	public function addcompromiso(Request $req){
																				
		$id = DB::table('compromisos')->insertGetId(
			[ 'id_vendedor'  => $req -> id_vendedor , 'tipo' 			 => $req -> tipo ,
				'id_categoria' => $req -> id_categoria, 'id_cliente' => $req -> id_cliente, 
				'fecha' 			 => $req -> fecha			  , 'hora' 		 	 => $req -> hora,		   
				'obs'  			   => $req -> obs				  , 'fuente'     => $req -> fuente
			]
		);

		if($id):
			return response("El compromiso se creo correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;
	}

	public function CompromisosxVend(Request $request){
		$compromisos = DB::select('SELECT c.id, c.id_vendedor, v.nombre as nomvend, c.tipo, c.id_categoria, ca.nombre as nomcatego,
																			c.fecha, c.hora, c.id_cliente,cli.nombre as nomcli, cli.tel1, cli.tel2, c.obs, 
																			c.fuente, u.nombre as nomuser, c.obs_usuario, c.cumplimiento, c.confirma_cita
																FROM compromisos c LEFT JOIN users v   	   ON v.id   = c.id_vendedor
																									 LEFT JOIN categorias ca ON ca.id  = c.id_categoria
																									 LEFT JOIN clientes  cli ON cli.id = c.id_cliente
																									 LEFT JOIN users u       ON u.id   = c.fuente
															WHERE c.id_vendedor =  ? AND c.fecha = ? AND c.cumplimiento=0', [$request -> id_vendedor, $request -> fecha]);
		return $compromisos;

	}

	public function Reagendar(Request $req){
		$reagendacion = DB::update('UPDATE compromisos SET fecha=:fecha, hora=:hora, obs=:obs, confirma_cita=:confirma_cita 
																WHERE id=:id', ['fecha' => $req -> fecha,  'hora' 		     => $req -> hora, 
																								'obs' 	=> $req -> obs   , 'confirma_cita' => 0,  								
																								'id' 	  => $req -> id]);
		if($reagendacion):
			return response("El compromiso se reagendo correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;

		return "Reagendado correctamente.";
	}

	public function ConfirmarCita(Request $req){
		$reagendacion = DB::update('UPDATE compromisos SET confirma_cita=:confirma_cita WHERE id=:id',
																	['confirma_cita' => $req -> confirma_cita, 'id'=> $req -> id]);

		return "Cita confirmada correctamente.";
	}

	public function CompromisosHechos(Request $req){
		$compromisosH = DB::select('SELECT c.id, c.fecha, c.hora, c.id_cliente, cli.nombre as nomcli,
																			 c.fecha_cierre, c.hora_cierre, c.obs_usuario
																	FROM compromisos c LEFT JOIN clientes  cli ON cli.id = c.id_cliente
																WHERE c.id_vendedor =  ? AND c.fecha between ? AND ? AND c.cumplimiento = 1
																ORDER BY c.fecha DESC ',[$req -> id, $req -> fecha1 , $req -> fecha2]);
		return $compromisosH;
	}


	
	public function TerminarCompromiso(Request $req){
		$terminar = DB::update('UPDATE compromisos SET  fecha_cierre=:fecha_cierre,hora_cierre=:hora_cierre, 
																									cumplimiento=:cumplimiento, obs_usuario=:obs_usuario
																WHERE id=:id', [ 	
																								'fecha_cierre' 	=> $req -> fecha_cierre, 
																								'hora_cierre' 	=> $req -> hora_cierre,
																								'cumplimiento'  => $req -> cumplimiento, 
																								'obs_usuario'   => $req -> obs_usuario,						
																								'id' 			 			=> $req -> id
																								]);

		if($terminar):
			return response("El compromiso se finalizo correctamente",200);
		else:
			return response("Ocurrio un problema. Intentelo nuevamente.",500);
		endif;

	}
	
	public function proyectosCotizados(Request $req){
		$compromisos = DB::select('SELECT c.id, c.id_vendedor, v.nombre as nomvend, c.tipo_compromiso, c.id_categoria, ca.nombre as nomcatego,
																			c.fecha, c.hora, c.fecha_fin, c.hora_fin, c.id_cliente,cli.nombre as nomcli, cli.tel1, cli.tel2, c.comentarios, c.fase_venta, 
																			c.id_usuario, u.nombre as nomuser, c.obs_usuario, c.cumplimiento, c.estatus, c.confirma_cita
																FROM compromisos c LEFT JOIN users v   	   ON v.id   = c.id_vendedor
																									 LEFT JOIN categorias ca ON ca.id  = c.id_categoria
																									 LEFT JOIN clientes  cli ON cli.id = c.id_cliente
																									 LEFT JOIN users u       ON u.id   = c.id_usuario
															WHERE c.id_vendedor =  ?  AND c.fase_venta=?', [$req -> id_vendedor, $req -> fase_venta]);
		return $compromisos;
	}

	public function FaseVenta(Request $req){
		$id_compromiso = $req -> id; 
		$fecha 				 = $req -> fecha; 
		$hora 				 = $req -> hora; 
		$fase_venta 	 = $req -> fase_venta;
		$numorden        = $req -> numorden;
		$aceptado      = $req -> aceptado;
		$obscierre      = $req -> obscierre;

		
		if($hisorial = $this->Historial($id_compromiso, $fecha, $hora, $fase_venta, $numorden, $aceptado, $obscierre)):
			$PutCompromiso = DB::update('UPDATE compromisos SET fase_venta=:fase_venta 
																		WHERE id=:id',[ 'fase_venta' => $fase_venta, 'id' => $id_compromiso]);
			return "Fase de venta actualizada";
		endif; 
	}

	//================================ FUNCIONES QUE SE EJECUTAN INTERNAMENTE =======================================
		public function HistorialCompromiso($id_compromiso, $fecha, $hora, $fase_venta, $obscierre){ // SOLO FUNCIONA PARA EL PROCESO DE TERMINAR COMPROMISO
			$historial1 = DB::insert('INSERT INTO historial(id_compromiso,fecha,hora,fase_venta, obscierre)
																	VALUES(?,?,?,?,?)',[$id_compromiso, $fecha, $hora, $fase_venta, $obscierre]);
			return $historial1;
		}

	//================================ FUNCIONES QUE SE EJECUTAN INTERNAMENTE =======================================
		public function Historial($id_compromiso, $fecha, $hora, $fase_venta, $numorden, $aceptado, $obscierre){ // FUNCION GLOBAL FASE > 1
			$historial = DB::insert('INSERT INTO historial(id_compromiso,fecha,hora,fase_venta,numorden,aceptado, obscierre)
																	VALUES(?,?,?,?,?,?,?)',[$id_compromiso, $fecha, $hora, $fase_venta,$numorden,$aceptado, $obscierre]);
			return $historial;
		}
}
