<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
use App\prospectos;



class prospectosController extends Controller {
	public function ProspectosxId($id){
		return $prospectosbyId = DB::select('SELECT * FROM clientes WHERE fuente = ? AND prospecto = 1',[$id]);
	}

	public function addProspecto(Request $request){
		$addprospecto = prospectos::create($request->all());
		
		if($addprospecto):
			return "El prospecto se ah insertado correctamente";
		else:
			return "Ocurrio un problema al crear el prospecto, por favor intentelo mas tarde.";
		endif;

	}

	public function UpdateProspecto($id, Request $req){
		$data = DB::update('UPDATE clientes SET nombre=:nombre, tipo_cliente=:tipo_cliente, nivel=:nivel,
																					 tel1=:tel1,contacto=:contacto	
												WHERE id=:id'
											,['nombre'		=> $req -> nombre, 		'tipo_cliente'	=> $req	-> tipo_cliente, 
											  'nivel'			=> $req	-> nivel,  		'tel1'					=> $req -> tel1,
											  'contacto'	=> $req -> contacto, 	'id'						=> $id 
											]
										);
	
		return 'El prospecto se actualizo correctamente';
	}


}
