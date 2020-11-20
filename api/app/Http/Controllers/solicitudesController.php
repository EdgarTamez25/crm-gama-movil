<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL

class solicitudesController extends Controller
{
	public function Departamentos(){
		return $info = [['id' => 1, 'nombre' => 'FLEXOGRAFIA'],
										['id' => 2, 'nombre' => 'BORDADOS'],
										['id' => 3, 'nombre' => 'DIGITAL'],
										['id' => 4, 'nombre' => 'OFFSET'],
										['id' => 5, 'nombre' => 'SERIGRAFÍA'],
										['id' => 6, 'nombre' => 'EMPAQUE'],
										['id' => 7, 'nombre' => 'SUBLIMACIÓN'],
										['id' => 8, 'nombre' => 'TAMPOGRAFÍA'],
										['id' => 9, 'nombre' => 'UV'],
									];
	}

  public function addSolicitud(Request $req){
		// !EL REQUEST CONTIENE EL DETALLE DE LA SOLICITUD "req -> detalle"
		// ! 1. CREO EL REGISTRO PARA LA SOLICITUD EN SOLICITUDES
		$id_solicitud  = DB::table('solicitudes')->insertGetId(
														[
																'id_cliente' => $req -> id_cliente,
																'id_usuario' => $req -> id_usuario,
																'fecha'      => $req -> fecha,
																'hora'       => $req -> hora,
																'nota'       => $req -> nota? $req -> nota: '',
														]
												);
		
		$detalle 			 = $req -> detalle; 			// !2. RECUPERAR DETALLE 
		$id_compromiso = $req -> id_compromiso;	// !2. RECUPERAR COMPROMISO
		$fecha         = $req -> fecha; 				// !2. RECUPERAR FECHA CIERRE 
		$hora  				 = $req -> hora;					// !2. RECUPERAR HORA CIERRE 

		for($i=0;$i<count($detalle); $i++): 		// ! GENERAR CICLO PARA RECORRER EL DETALLE DE LA SOLICITUD
				//TODO *** PRODUCTO EXISTENTE *******************************************************************
			if($detalle[$i]["tproducto"] === 1 ):
				if($prod_exist = $this->productoExistente($id_solicitud,$detalle[$i])): 						    // !MANDO A INSERTAR EL PRODUCTO EXISTENTE
					$this -> generaDetalleSoli($id_solicitud,$detalle[$i]["tproducto"], $prod_exist);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
					$this -> actualizaCompromisos($fecha,$hora,$id_compromiso);														// !ACTUALIZO COMPROMISO
				endif; 
			endif;
			
				//TODO *** PRODUCTO POR MODIFICAR **************************************************************
			if($detalle[$i]["tproducto"] === 2 ): 
				if($prod_modif = $this->productoAModificar($id_solicitud,$detalle[$i])): 						    // !MANDO A INSERTAR EL PRODUCTO A MODIFICAR
					$this -> generaDetalleSoli($id_solicitud,$detalle[$i]["tproducto"], $prod_modif);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
					$this -> actualizaCompromisos($fecha,$hora,$id_compromiso);														// !ACTUALIZO COMPROMISO
				endif;
			endif;

			if($detalle[$i]["tproducto"] === 3 ): //! PRODUTO NUEVO
				if($prod_nuevo = $this->productoNuevo($id_solicitud,$detalle[$i])): 						    // !MANDO A INSERTAR EL PRODUCTO NUEVO
					$this -> generaDetalleSoli($id_solicitud,$detalle[$i]["tproducto"], $prod_nuevo);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
					$this -> actualizaCompromisos($fecha,$hora,$id_compromiso);														// !ACTUALIZO COMPROMISO
				endif;
			endif;

		endfor;

		return response("La solicitud se creo correctamente"   ,200);// !SI SE INSERTO CORRECTAMENTE RETORNO RESPUESTA
		// 	return response("Ocurrio un problema. Intentelo nuevamente.",500);
	}	
	// TODO ******************** FUNCIONES PARA PRODUCTOS EXISTENTES *******************************
	public function productoExistente($id_solicitud, $detalle){
		$insertProducto = DB::table('prod_exist')->insertGetId(   //! INSERTO PRODUCTO EXISTENTE
														[
																'id_solicitud' => $id_solicitud,
																'dx' 					 => $detalle["dx"],
																'ft'           => $detalle["referencia"],
																'cantidad'		 => $detalle["cantidad"],
																'tipo_prod'    => $detalle["tproducto"],
														]
												);
		return $insertProducto ? $insertProducto : false;   //!RETORNO ID ENCONTRADO O FALSO SI HAY UN ERROR
	}
	// TODO ******************** FUNCIONES PARA PRODUCTOS NUEVOS ***********************************
	// TODO ****************************************************************************************
	public function productoNuevo($id_solicitud, $detalle){
		$id_dx = null; //!DECLARAR VARIABLE PARA GUARDAR EL ID DE LA TABLA DONDE SE INSERTARA LA CARACTERISTICA
		switch ($detalle["dx"]) {
			case 1:
				$id_dx = $this -> insertarEnFlexo($detalle);
				break;
			case 3:
				$id_dx = $this -> insertarEnDigital($detalle);
				break;
		}

		$insertProducto = DB::table('prod_nuevo')->insertGetId(   //! INSERTO PRODUCTO NUEVO
														[
																'id_solicitud' => $id_solicitud,
																'dx' 					 => $detalle["dx"],
																'id_dx' 			 => $id_dx,
																'ft'           => $detalle["referencia"],
																'cantidad'		 => $detalle["cantidad"],
																'tipo_prod'    => $detalle["tproducto"],
														]
												);
		return $insertProducto ? $insertProducto : false;   //!RETORNO ID ENCONTRADO O FALSO SI HAY UN ERROR
	}

	public function insertarEnFlexo($detalle){
		$id_flexo = DB::table('det_flexo')->insertGetId(   //! INSERTO PRODUCTO NUEVO
			[
					'id_material' 		=> $detalle["id_material"],
					'etqxrollo' 			=> $detalle["etqxrollo"],
					'med_nucleo'  		=> $detalle["med_nucleo"],
					'etqxpaso'		 		=> $detalle["etqxpaso"],
					'med_desarrollo'  => $detalle["med_desarrollo"],
					'med_eje'    			=> $detalle["med_eje"],
					'id_orientacion'  => $detalle["id_orientacion"],
					'ancho'    				=> $detalle["ancho"],
					'largo'    				=> $detalle["largo"],
			]
		);
	  //! MANDO ACTUALIZAR DETALLES DE LA CARACTERISTICAS PARA CONSERVAR EL MISMO ID
		$detalles = DB::update('UPDATE det_flexo SET det_acabados=:det_acabados,det_pantones=:det_pantones
															WHERE id=:id', ['det_acabados' => $id_flexo,'det_pantones' => $id_flexo,'id' => $id_flexo	]);
	
		$this -> ciclaAcabados($id_flexo, $detalle); //!GENERO CICLO PARA INSERTAR ACABADOS
		$this -> ciclaPantones($id_flexo, $detalle); //!GENERO CICLO PARA INSERTAR PANTONES
		return $id_flexo;
	}

	public function insertarEnDigital($detalle){
		$id_digital = DB::table('det_digital')->insertGetId(   //! INSERTO PRODUCTO NUEVO
			[
					'id_material' 		=> $detalle["id_material"],
					'det_sobre' 			=> $detalle["id_material2"],
					'estructura'		 	=> $detalle["estructura"],
					'grosor'  				=> $detalle["grosor"],
					'ancho'    				=> $detalle["ancho"],
					'largo'    				=> $detalle["largo"],
			]
		);
	  //! MANDO ACTUALIZAR DETALLES DE LA CARACTERISTICAS PARA CONSERVAR EL MISMO ID
		$detalles = DB::update('UPDATE det_digital SET det_acabados=:det_acabados,det_pantones=:det_pantones
															WHERE id=:id', ['det_acabados' => $id_digital,'det_pantones' => $id_digital,'id' => $id_digital	]);
	
		$this -> ciclaAcabados($id_digital, $detalle); //!GENERO CICLO PARA INSERTAR ACABADOS
		$this -> ciclaPantones($id_digital, $detalle); //!GENERO CICLO PARA INSERTAR PANTONES
		return $id_digital;
	}
	// TODO ************ FUNCIONES PARA PRODUCTOS POR MODIFICAR ***********************************
	// TODO ***************************************************************************************
	public function productoAModificar($id_solicitud, $detalle){
		$prodmodif = DB::table('prod_modif')->insertGetId(   //!INSERTO PRODUCTO POR MODIFICAR
														[
																'id_solicitud' => $id_solicitud,
																'dx' 					 => $detalle["dx"],
																'ft'           => $detalle["referencia"],
																'cantidad'		 => $detalle["cantidad"],
																'tipo_prod'    => $detalle["tproducto"],
														]
												);
		for($i=0;$i<count($detalle['xmodificar']); $i++):  //! GENERO UN CICLO EN EL ARRAY QUE RECIBO.
			if($detalle['xmodificar'][$i]['tipo'] === 1):    //! EVALUO SI EL OBJECTO SOLO CONTIENE UN ELEMENTO, MANDO A INSERTARLO
				$this -> insertarConcepto($prodmodif, $detalle['xmodificar'][$i]['concepto'], $detalle['xmodificar'][$i]['valor']);
			else:  																					//! SI EL OBJETO CONTIENE UN ARRAY ENTONCES MANDO A CICLARLO PARA INSERTAR
				$this -> ciclarConcepto($prodmodif, $detalle['xmodificar'][$i]['concepto'], $detalle['xmodificar'][$i]['valor']);
			endif; 
		endfor;

		return $prodmodif ? $prodmodif : false; //! RETORNO ID DE LA TABLA DE MODIFICACIONES.

	}
	//!ESTA FUNCION SOLO INSERTA EN LA TABLA DE MODIFICACIONES 
	//! Y SE MANDA A LLAMAR SOLO DE LA FUNCION productoAModificar
	public function insertarConcepto($prod_modif, $concepto, $valor){ 
		$dxmodif = DB::table('dx_modif')->insertGetId(
				[
						'id_prod_modif' => $prod_modif,
						'concepto'		  => $concepto,
						'valor'         => $valor,
				]
		);
	}
	//!ESTA FUNCION CICLA LOS ELEMENTOS DEL CONCEPTO A MODIFICAR
	//!Y LOS INCERTA DE MANERA INDIVIDUAL EN LA TABLA DE MODIFICACIONES.
	public function ciclarConcepto($prod_modif, $concepto, $valor){
		for($i=0;$i<count($valor); $i++):
			$this -> insertarConcepto($prod_modif, $concepto, $valor[$i]);
		endfor;
	}

	// TODO ******************** FUNCIONES PARA TODOS LOS CASOS ***********************************
	// TODO ****************************************************************************************
	public function ciclaAcabados($id_dx, $detalle){
		for($i=0;$i<count($detalle['acabados']); $i++):  
			// id_dx = " el registro en la tabla de caracteristicas;
			// $detalle['acabados'][$i]['id'] = el id del acabado en cuestion;
			// $detalle['dx'] = id correspondiente a la tabla de caracteristicas;
			$this -> insertarAcabado($id_dx, $detalle['acabados'][$i]['id'], $detalle['dx']);
		endfor;
	}
	public function ciclaPantones($id_dx, $detalle){
		for($i=0;$i<count($detalle['pantones']); $i++):  
			// id_dx = " el registro en la tabla de caracteristicas;
			// $detalle['pantones'][$i] = el id del acabado en cuestion;
			// $detalle['dx'] = id correspondiente a la tabla de caracteristicas;
			$this -> insertarPantone($id_dx, $detalle['pantones'][$i], $detalle['dx']);
		endfor;
	}
	public function insertarAcabado($id_dx, $id_acabado, $dx){ 
		$acabado = DB::table('det_acabado')->insertGetId(
				[
						'dx'				 => $dx,
						'id_acabado' => $id_acabado,
						'id_dx'      => $id_dx,
				]
		);
	}
	public function insertarPantone($id_dx, $pantone, $dx){ 
		$pantone = DB::table('det_pantone')->insertGetId(
				[
						'dx'				=> $dx,
						'pantone' 	=> $pantone,
						'id_dx'     => $id_dx,
				]
		);
	}
	public function generaDetalleSoli($id_solicitud, $px, $id_px){
		$insertProducto = DB::table('det_sol')->insertGetId(
			[
					'id_solicitud' => $id_solicitud,
					'px' 					 => $px,
					'id_px'        => $id_px,
			]
		);
	}
	public function actualizaCompromisos($fecha,$hora,$id){
		$actualizaEstatus = DB::update('UPDATE compromisos SET cumplimiento=:cumplimiento, fecha=:fecha, hora=:hora WHERE id=:id',
																				[ 'cumplimiento' => 1, 'fecha' => $fecha, 'hora' => $hora, 'id' => $id ]);
	}

// TODO ******************************************************************************************
// TODO ****************************************************************************************

	public function Solicitudes(Request $req){
		$solicitud = DB::select('SELECT     s.id, s.id_cliente, s.id_usuario,
																				s.fecha, s.hora, s.nota
														FROM        solicitudes s
														WHERE id = ?',[$req -> id]);
		return $solicitud;
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


