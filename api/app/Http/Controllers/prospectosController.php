<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
use App\prospectos;



class prospectosController extends Controller {
	public function ProspectosxId($id, Request $req){
        $dataProspectos = DB::select('SELECT c.id , c.clave, c.nombre, c.id_zona, c.direccion, c.id_ciudad, c.cp,
                                      c.razon_social, c.id_giro, c.fuente, c.tipo_cliente, c.rfc, c.tel1, c.ext1,
                                      c.tel2, c.ext2, c.nivel, c.contacto, c.contacto2, c.diasfact, c.nivel, c.prospecto,
                                      c.id_cartera, c.estatus
                                      FROM clientes c
                                      WHERE c.id = ?',[$req -> id]);
        return $dataProspectos;
	// return $prospectosbyId = DB::select('SELECT * FROM clientes WHERE fuente = ? AND prospecto = 1',[$id]);
	}

	public function addProspecto(Request $req){
        $addprospecto = DB::table('clientes')->insertGetId(
                        [
                            'clave'           => $req -> clave ,
                            'nombre'          => $req -> nombre ,
                            'id_zona'         => $req -> id_zona,
                            'direccion'       => $req -> direccion,
                            'id_ciudad'       => $req -> id_ciudad,
                            'cp'              => $req -> cp,
                            'razon_social'    => $req -> razon_social,
                            'id_giro'         => $req -> id_giro,
                            'fuente'          => $req -> fuente,
                            'tipo_cliente'    => $req -> tipo_cliente,
                            'rfc'             => $req -> rfc,
                            'tel1'            => $req -> tel1,
                            'ext1'            => $req -> ext1,
                            'tel2'            => $req -> tel2,
                            'ext2'            => $req -> ext2,
                            'contacto'        => $req -> contacto,
                            'contacto2'       => $req -> contacto2,
                            'diasfact'        => $req -> diasfact,
                            'nivel'           => $req -> nivel,
                            'prospecto'       => $req -> prospecto,
                            'id_cartera'      => $req -> id_cartera,
                            'estatus'         => $req -> estatus
                        ]
                    );
        if($addprospecto):
			return response("El prospecto se ah insertado correctamente",200);
		else:
			return response("Ocurrio un problema al crear el prospecto, por favor intentelo mas tarde.",500);
		endif;
	}

	public function UpdateProspecto($id, Request $req){
		$data = DB::update('UPDATE clientes SET nombre=:nombre, tipo_cliente=:tipo_cliente, nivel=:nivel,
												tel1=:tel1,contacto=:contacto
							WHERE id=:id'
                        ,[
                            'id'			=> $id,
                            'nombre'		=> $req -> nombre, 'tipo_cliente'	=> $req	-> tipo_cliente,
                            'nivel'			=> $req	-> nivel,  'tel1'			=> $req -> tel1,
                            'contacto'	    => $req -> contacto
                        ]
                    );
        // 		$data = DB::update('UPDATE clientes SET c.id , c.clave, c.nombre, c.id_zona, c.direccion, c.id_ciudad, c.cp,
        //                                              c.razon_social, c.id_giro, c.fuente, c.tipo_cliente, c.rfc, c.tel1, c.ext1,
        //                                              c.tel2, c.ext2, c.nivel, c.contacto, c.contacto2, c.diasfact, c.nivel, c.prospecto,
        //                                              c.id_cartera, c.estatus
        // 		WHERE id=:id'
        // 	,[
        //         'id'              => $id,
        //         'clave'           => $req -> clave ,      'nombre' 	    => $req -> nombre ,
        //         'id_zona'         => $req -> id_zona,     'direccion'    => $req -> direccion,
        //         'id_ciudad' 	     => $req -> id_ciudad,   'cp' 	        => $req -> cp,
        //         'razon_social'    => $req -> razon_social,'id_giro'      => $req -> id_giro,
        //         'fuente'          => $req -> fuente,      'tipo_cliente' => $req -> tipo_cliente,
        //         'rfc'             => $req -> rfc,         'tel1'  	    => $req -> tel1,
        //         'ext1'            => $req -> ext1,        'tel2'         => $req -> tel2,
        //         'ext2'            => $req -> ext2,        'contacto'     => $req -> contacto,
        //         'contacto2'       => $req -> contacto2,   'diasfact'     => $req -> diasfact,
        //         'nivel'           => $req -> nivel,       'prospecto'    => $req -> prospecto,
        //         'id_cartera'      => $req -> id_cartera,  'estatus'      => $req -> estatus
        // 	]
        // );

		return 'El prospecto se actualizo correctamente';
	}


}
