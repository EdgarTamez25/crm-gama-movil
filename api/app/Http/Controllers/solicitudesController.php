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

	public function Solicitudes(Request $req){
		$solicitud = DB::select('SELECT     s.id, s.id_cliente, c.nombre as nomcli, s.id_usuario, u.nombre as nomusuario,
																				s.fecha, s.hora, s.nota, s.estatus 
															FROM  solicitudes s LEFT JOIN clientes c ON s.id_cliente = c.id
																									LEFT JOIN users    u ON s.id_usuario = u.id
														WHERE s.id_usuario = ? AND s.fecha BETWEEN ? AND ? ORDER BY s.estatus ASC' ,
														[$req -> id_usuario, $req -> fecha1 , $req -> fecha2 ]);
														
		return $solicitud ? $solicitud : [];
	}

	//! CONSULTAR DETALLE DE LA SOLICITUD
	public function DetalleSolicitud($id){
		// ! IR A DET_SOL PARA OBTENER TODOS LOS PRODUCTOS DE LA SOLICITUD
		$detalle = DB::select('SELECT * FROM	det_sol WHERE id_solicitud = ?', [$id]);
		$detSol = [];

		for($i=0;$i<count($detalle);$i++ ):
			if($detalle[$i] -> px === 1):
				$existente = $this -> consultaProdExistente($detalle[$i] -> id_px);
				array_push($detSol,  $existente[0]);
			endif;
			if($detalle[$i] -> px === 2):
				$Modificacion = $this -> consultaProdModif($detalle[$i] -> id_px);
				array_push($detSol, $Modificacion[0]);
			endif;
			if($detalle[$i] -> px === 3):
				$Nuevos = $this -> consultaProdNuevo($detalle[$i] -> id_px);
				array_push($detSol, $Nuevos[0]);
			endif; 
		endfor;
		
		return $detSol ? $detSol: $detSol = [];
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

	// ! OBTENER CARACTERISTICAS POR FORMULARIO PRODUCTOS NUEVOS
	public function Caracteristicas(Request $req){
		switch ($req['dx'] ) {
			case 1:
				return $this -> Flexografia($req['id_dx'], $req['dx'] );
				break;
			case 3:
				return $this -> Digital($req['id_dx'], $req['dx'] );
				break;
		}
	}

	//! OBTENER MODIFICACIONES DE UNA SOLICITUD
	public function Modificaciones($id){
		return  DB::select('SELECT * FROM dx_modif WHERE id_prod_modif =?',[$id]);
  }

	// TODO CARACTERISTICAS POR FORMULARIO******************************************
	// TODO ************************************************************************
	public function Flexografia($id_dx, $dx){
		$arrayTemporal = [];

		$flexo    = DB::select('SELECT * FROM det_flexo WHERE id =?',[$id_dx]);
		$pantones = $this -> obtenerPantones($flexo[0] -> det_pantones, $dx);
		$acabados = $this -> obtenerAcabados($flexo[0] -> det_acabados, $dx);

		$arrayTemporal = ["id" 					   => $flexo[0] -> id, 
										  "id_material"    => $flexo[0] -> id_material,
										  "det_acabados"   => $flexo[0] -> det_acabados,
										  "etqxrollo"      => $flexo[0] -> etqxrollo,
										  "etqxpaso" 		   => $flexo[0] -> etqxpaso,
										  "med_nucleo" 	   => $flexo[0] -> med_nucleo, 
										  "med_desarrollo" => $flexo[0] -> med_desarrollo,
										  "med_eje" 			 => $flexo[0] -> med_eje,
										  "id_orientacion" => $flexo[0] -> id_orientacion,
										  "ancho" 			 	 => $flexo[0] -> ancho,
										  "largo" 			   => $flexo[0] -> largo,
										  "det_pantones"   => $flexo[0] -> det_pantones,
										  "pantones" 		   => $pantones,
										  "acabados" 		   => $acabados
										  ];

		return $arrayTemporal;
	}

	public function obtenerPantones($id_pantone, $dx){
		return DB::select('SELECT * FROM det_pantone WHERE id_dx = ? AND dx=?', [$id_pantone, $dx]);
	}

	public function obtenerAcabados($id_acabados , $dx){
		$acabados_modificados = [];
		$acabados = DB::select('SELECT d.id as id_key, d.id_dx, d.dx, d.id_acabado as id, a.nombre
													FROM det_acabado d LEFT JOIN acabados a ON d.id_acabado = a.id
												WHERE d.id_dx = ? AND d.dx = ?', [$id_acabados , $dx]);

		for($i=0;$i<count($acabados);$i++ ):
			$acabado = ["id_key" => $acabados[$i] -> id_key, 
										"id_dx"  => $acabados[$i] -> id_dx,
										"dx"     => $acabados[$i] -> dx, 
										"id"     => intval($acabados[$i] -> id), 
										"nombre" => $acabados[$i] -> nombre
			];
			array_push($acabados_modificados, $acabado);
		endfor;

		return $acabados_modificados;

	}
	
	public function Digital($id_dx, $dx){
		$arrayTemporal = [];
		$digital       = DB::select('SELECT * FROM det_digital WHERE id =?',[$id_dx]);
		$pantones      = $this -> obtenerPantones($digital[0] -> det_pantones, $dx);
		$acabados      = $this -> obtenerAcabados($digital[0] -> det_acabados, $dx);

		$arrayTemporal = ["id" 					 => $digital[0] -> id, 
										  "id_material"  => $digital[0] -> id_material,
										  "det_sobre"    => $digital[0] -> det_sobre,
										  "det_pantones" => $digital[0] -> det_pantones,
										  "det_acabados" => $digital[0] -> det_acabados, 
										  "estructura"   => $digital[0] -> estructura,
										  "grosor" 			 => $digital[0] -> grosor,
										  "ancho" 			 => $digital[0] -> ancho,
										  "largo" 			 => $digital[0] -> largo,
										  "pantones" 		 => $pantones,
										  "acabados" 		 => $acabados
										  ];

		return $arrayTemporal;
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

	// TODO -> CONSULTAS CORRESPONDIENTES AL DETALLE DE SOLICITUD ******************
	// TODO ************************************************************************
	public function consultaProdExistente($id_px){
		return DB::select('SELECT * FROM prod_exist WHERE id =?',[$id_px]);
	}
	public function consultaProdModif($id_px){
		return DB::select('SELECT * FROM prod_modif WHERE id =?',[$id_px]);
	}
	public function consultaProdNuevo($id_px){
		return DB::select('SELECT * FROM prod_nuevo WHERE id =?',[$id_px]);
	}


	// TODO ******************** FUNCIONES PARA TODOS LOS CASOS ***********************************
	// TODO ****************************************************************************************
	public function ciclaAcabados($id_dx, $detalle){
		for($i=0;$i<count($detalle['acabados']); $i++):  
			// id_dx = " el registro en la tabla de caracteristicas;
			// $detalle['acabados'][$i]['id'] = el id del acabado en cuestion;
			// $detalle['dx'] = id correspondiente a la tabla de caracteristicas;
			$this -> insertarAcabado($id_dx, $detalle['acabados'][$i], $detalle['dx']);
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
	public function eliminaPantones($pantones){
		for($i=0;$i<count($pantones); $i++):  
			DB::delete('DELETE FROM det_pantone WHERE id = ?', [ $pantones[$i]['id']]);
		endfor;
	}
	public function eliminaAcabados($acabados){
		for($i=0;$i<count($acabados); $i++):  
			DB::delete('DELETE FROM det_acabado	 WHERE id = ?', [ $acabados[$i]['id_key']]);
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
		$actualizaEstatus = DB::update('UPDATE compromisos SET cumplimiento=:cumplimiento, fecha_cierre=:fecha_cierre, hora_cierre=:hora_cierre 
																			WHERE id=:id', [ 'cumplimiento' => 1, 
																											 'fecha_cierre' => $fecha, 
																											 'hora_cierre'  => $hora, 
																											 'id' 					=> $id 
																											]);
	}


	// TODO ******************** FUNCIONES PARA ACTUALIZAR PRODUCTOS ***********************************
	// TODO ********************************************************************************************
	public function ActualizaProducto(Request $req){
		if($req -> tproducto === 1):
			$this -> actualizaProdExist($req);
		endif;
		if($req -> tproducto === 2):
			$this -> actualizaProdModif($req);
			$this -> eliminaDxModif($req -> conceptosAEliminar);
			$this -> actualizaDxModif($req -> id_partida, $req -> xmodificar );
		endif;
		if($req -> tproducto === 3):
			$this -> actualizaProdNuevo($req);
			$this -> actualizaDx($req);
		endif;

		return response("EL producto se actualizo correctamente.");
	}
	public function actualizaProdExist($data){
		$actualizaProdExist = DB::update('UPDATE prod_exist SET ft=:ft, cantidad=:cantidad WHERE id=:id', 
																						[ 'ft' 			 => $data['referencia'], 
																							'cantidad' => $data['cantidad'], 
																							'id' 		   => $data['id_partida']  
																						]);
	}
	public function actualizaProdModif($data){
		$actualizaProdNuevo = DB::update('UPDATE prod_modif SET ft=:ft, cantidad=:cantidad WHERE id=:id', 
																						[ 'ft' 			 => $data['referencia'], 
																							'cantidad' => $data['cantidad'], 
																							'id' 		   => $data['id_partida']  
																						]);
	}
	public function actualizaProdNuevo($data){
		$actualizaProdNuevo = DB::update('UPDATE prod_nuevo SET ft=:ft, cantidad=:cantidad WHERE id=:id', 
																						[ 'ft' 			 => $data['referencia'], 
																							'cantidad' => $data['cantidad'], 
																							'id' 		   => $data['id_partida']  
																						]);
	}
	public function actualizaDx($data){
		if($data['dx'] === 1){
			$this -> actualizaDetFlexo($data);
		}
		if($data['dx'] === 3){
			$this -> actualizaDetDigital($data);
		}

	}
	public function actualizaDetFlexo($data){
		DB::update('UPDATE det_flexo SET id_material=:id_material, etqxrollo=:etqxrollo, etqxpaso=:etqxpaso, 
																		 med_nucleo=:med_nucleo,med_desarrollo=:med_desarrollo, med_eje=:med_eje, 
																	   id_orientacion=:id_orientacion, ancho=:ancho, largo=:largo WHERE id=:id', 
											[ 'id_material'    => $data['id_material'], 
												'etqxrollo' 	   => $data['etqxrollo'], 
												'etqxpaso'       => $data['etqxpaso'],
												'med_nucleo'     => $data['med_nucleo'],
												'med_desarrollo' => $data['med_desarrollo'],
												'med_eje'        => $data['med_eje'],
												'id_orientacion' => $data['id_orientacion'],
												'ancho'    			 => $data['ancho'],
												'largo'    			 => $data['largo'],
												'id' 		   			=> $data['id_caracter']  
											]);
		$this -> eliminaPantones($data['pantonesAEliminar']);
		$this -> eliminaAcabados($data['acabadosAEliminar']);
		$this -> ciclaPantones(  $data['id_caracter'], $data);
		$this -> ciclaAcabados(  $data['id_caracter'], $data);
	}
	public function actualizaDetDigital($data){
		DB::update('UPDATE det_digital SET id_material=:id_material, det_sobre=:det_sobre, estructura=:estructura, 
																		 grosor=:grosor, ancho=:ancho, largo=:largo WHERE id=:id', 
											[ 'id_material' => $data['id_material'], 
												'det_sobre' 	=> $data['id_material2'], 
												'estructura'  => $data['estructura'],
												'grosor'      => $data['grosor'],
												'ancho'    		=> $data['ancho'],
												'largo'    		=> $data['largo'],
												'id' 		   		=> $data['id_caracter']  
											]);
		$this -> eliminaPantones($data['pantonesAEliminar']);
		$this -> eliminaAcabados($data['acabadosAEliminar']);
		$this -> ciclaPantones(  $data['id_caracter'], $data);
		$this -> ciclaAcabados(  $data['id_caracter'], $data);
	}
	public function eliminaDxModif($conceptosAEliminar){
		for($i=0;$i<count($conceptosAEliminar); $i++):  
			DB::delete('DELETE FROM dx_modif WHERE id = ?', [ $conceptosAEliminar[$i] ]);
		endfor;
	}
	public function actualizaDxModif($prodmodif, $detalle){
		for($i=0;$i<count($detalle['xmodificar']); $i++):  //! GENERO UN CICLO EN EL ARRAY QUE RECIBO.
			if($detalle['xmodificar'][$i]['tipo'] === 1):    //! EVALUO SI EL OBJECTO SOLO CONTIENE UN ELEMENTO, MANDO A INSERTARLO
				$this -> insertarConcepto($prodmodif, $detalle['xmodificar'][$i]['concepto'], $detalle['xmodificar'][$i]['valor']);
			else:  																					//! SI EL OBJETO CONTIENE UN ARRAY ENTONCES MANDO A CICLARLO PARA INSERTAR
				$this -> ciclarConcepto($prodmodif, $detalle['xmodificar'][$i]['concepto'], $detalle['xmodificar'][$i]['valor']);
			endif; 
		endfor;
	}

	// TODO ******************** FUNCIONES PARA ELIMINAR PRODUCTOS *************************************
	// TODO ********************************************************************************************
	public function EliminaProducto(Request $req){
		if($req -> tipo_prod === 1):
			$this -> eliminaPartidaPxExist($req -> id_px );
		endif;

		if($req -> tipo_prod === 2):
			$this -> eliminaPartidaPxModif($req -> id_px);
			$this -> eliminaParidasDxModif($req -> id_px);
		endif;

		if($req -> tipo_prod === 3):
			$this -> eliminaPartidaPxNuevo($req -> id_px);
			$this -> eliminaPartidaDetDx($req);
		endif;

		$this -> eliminaPartidaDelSol($req);
		$this -> validaEstatusSolicitud($req);
		return response("EL producto se elimino correctamente.");
	}

	public function eliminaPartidaPxExist($id_px){
		DB::delete('DELETE FROM prod_exist WHERE id=?', [$id_px]);
	} 
	public function eliminaPartidaPxModif($id_px){
		DB::delete('DELETE FROM prod_modif WHERE id=?', [$id_px]);
	} 
	public function eliminaPartidaPxNuevo($id_px){
		DB::delete('DELETE FROM prod_nuevo WHERE id=?', [$id_px]);
	} 
	public function eliminaPartidaDetDx($detalle){
		if($detalle -> dx === 1):
			DB::delete('DELETE FROM det_flexo WHERE id=?',[$detalle -> id_dx ]);
		endif;

		if($detalle -> dx === 3):
			DB::delete('DELETE FROM det_digital WHERE id=?',[$detalle -> id_dx ]);
		endif;
	}
	public function eliminaParidasDxModif($id_px){
		$prodModif = DB::select('SELECT * FROM dx_modif WHERE id_prod_modif =?',[$id_px]);
		for($i=0;$i<count($prodModif); $i++):  
			DB::delete('DELETE FROM dx_modif WHERE id = ?',[$prodModif[$i] -> id ]);
		endfor;
	}
	public function eliminaPartidaDelSol($detalle){
		 $id_det_sol = DB::select('SELECT id FROM det_sol WHERE id_px=? AND px=? AND id_solicitud=?',
																[$detalle['id_px'],$detalle['tipo_prod'],$detalle['id_solicitud'] ]);
		 DB::delete('DELETE FROM det_sol WHERE id=?', [$id_det_sol[0] -> id]);
	}

	// TODO ******************** FUNCIONES PARA AGREGAR PRODUCTOS *************************************
	// TODO ********************************************************************************************
	public function AgregarProductoSol(Request $req){

		if($req -> tproducto === 1 ):
			if($prod_exist = $this->productoExistente($req -> id_solicitud, $req)): 						    // !MANDO A INSERTAR EL PRODUCTO EXISTENTE
				$this -> generaDetalleSoli($req -> id_solicitud,$req -> tproducto, $prod_exist);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
			endif; 
		endif;
		
		// 	//TODO *** PRODUCTO POR MODIFICAR **************************************************************
		if($req -> tproducto === 2 ): 
			if($prod_modif = $this->productoAModificar($req -> id_solicitud, $req -> xmodificar)): 	
				$this -> generaDetalleSoli($req -> id_solicitud,$req -> tproducto, $prod_modif);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
			endif;
		endif;

		if($req -> tproducto === 3 ): 
			if($prod_nuevo = $this->productoNuevo($req -> id_solicitud, $req)): 						    // !MANDO A INSERTAR EL PRODUCTO NUEVO
				$this -> generaDetalleSoli($req -> id_solicitud,$req -> tproducto, $prod_nuevo);     // !MANDO A CREAR DETALLE DE LA SOLICITUD
			endif;
		endif;
		
		$this -> validaEstatusSolicitud($req);
		return response("El producto se agrego correctamente", 200);

	}

	// TODO ******************** FUNCIONES PARA VALIDAR ESTATUS DE SOLICITUD ***************************
	// TODO ********************************************************************************************
	public function	validaEstatusSolicitud($data){
		$cero=0;$uno=0;$dos=0; $tres=0; $cuatro=0;
	  $detalle = $this -> DetalleSolicitud($data['id_solicitud']);

		if(!$detalle): 
			$this -> actualizaEstatusSolicitud($data['id_solicitud'],1);
			return ;
		endif;

		for($i=0;$i<count($detalle); $i++): 
			if($detalle[$i] -> estatus === 0):
				$cero++;
			elseif($detalle[$i] -> estatus === 1 ):
				$uno++;
			elseif($detalle[$i] -> estatus === 2):
				$dos++;
			elseif($detalle[$i] -> estatus === 3):
				$tres++;
			elseif($detalle[$i] -> estatus === 4):
				$cuatro++;
			endif;
		endfor;
		
		if($cero > 0):
			$this -> actualizaEstatusSolicitud($data['id_solicitud'],1);
		elseif($uno > 0):
			$this -> actualizaEstatusSolicitud($data['id_solicitud'],1);
		elseif($dos > 0):
			 $this -> actualizaEstatusSolicitud($data['id_solicitud'],2);
		elseif($tres > 0):
			 $this -> actualizaEstatusSolicitud($data['id_solicitud'],3);
		elseif($cuatro > 0):
			 $this -> actualizaEstatusSolicitud($data['id_solicitud'],4);
		endif;
	}
	public function actualizaEstatusSolicitud($id_solicitud, $estatus){
		DB::update('UPDATE solicitudes SET estatus=:estatus 
								 WHERE id=:id',['estatus' => $estatus, 'id' => $id_solicitud]); 
  }


  public function cancelarSolicitud(Request $req){
  	if( $this -> validaCancelacion($req) ):
  		DB::update('UPDATE solicitudes SET estatus=:estatus WHERE id=:id',
																			[ 'estatus' => $req -> estatus, 
																				'id' 		  => $req -> id_solicitud  
																			]);

  		return response("La solicitud se cancelo correctamente.",200);
  	else:
  		return response("La solicitud no se puede cancelar, por que tiene movimientos realizados", 500 );
  	endif;

  }

  public function validaCancelacion($data){
  	$detalle = $this -> DetalleSolicitud($data['id_solicitud']);
  	$respuesta = true;
  	for($i=0;$i<count($detalle); $i++): 
			if($detalle[$i] -> estatus > 1):
				$respuesta = false;
			endif;
		endfor;

		return $respuesta;
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


