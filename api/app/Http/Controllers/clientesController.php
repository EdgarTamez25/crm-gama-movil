<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\clientes;

class clientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function catClientes(Request $req)
    {
        $dataClientes = DB::select('SELECT c.id , c.clave, c.nombre, c.id_zona, c.direccion, c.id_ciudad, c.cp,
                                           z.nombre as nomzona, c.razon_social, c.id_giro, c.fuente, c.tipo_cliente,
                                           c.rfc, c.tel1, c.ext1, c.tel2, c.ext2, c.nivel, c.contacto, c.contacto2,
                                           c.diasfact, c.nivel, c.prospecto, c.id_cartera, c.estatus
                                    FROM clientes c LEFT JOIN zonas z ON z.id = c.id_zona
                                    WHERE c.id = ?',[$req -> id]);
        return $dataClientes;
    }

    public function add(Request $req){

        $addcliente = DB::table('clientes')->insertGetId(
			[
              'clave'           => $req -> clave ,      'nombre' 	   => $req -> nombre ,
			  'id_zona'         => $req -> id_zona,     'direccion'    => $req -> direccion,
		      'id_ciudad' 		=> $req -> id_ciudad,   'cp' 	       => $req -> cp,
			  'razon_social'    => $req -> razon_social,'id_giro'      => $req -> id_giro,
              'fuente'          => $req -> fuente,      'tipo_cliente' => $req -> tipo_cliente,
              'rfc'             => $req -> rfc,         'tel1'  	   => $req -> tel1,
              'ext1'            => $req -> ext1,        'tel2'         => $req -> tel2,
              'ext2'            => $req -> ext2,        'contacto'     => $req -> contacto,
              'contacto2'       => $req -> contacto2,   'diasfact'     => $req -> diasfact,
              'nivel'           => $req	-> nivel,       'prospecto'    => $req -> prospecto,
              'id_cartera'      => $req -> id_cartera,  'estatus'      => $req -> estatus
			]
		);
		// $addcliente = clientes::create($request->all());
        if($addcliente):
			return response("El Cliente se ah insertado correctamente",200);
		else:
			return response("Ocurrio un problema al crear el cliente, por favor intentelo mas tarde.",500);
		endif;
    }

    public function update($id, Request $req){
        $addcliente = DB::update('UPDATE clientes SET clave=:clave, nombre=:nombre, id_zona=:id_zona, direccion=:direccion, id_ciudad=:id_ciudad,
                                                      cp=:cp, razon_social=:razon_social, id_giro=:id_giro, fuente=:fuente, tipo_cliente=:tipo_cliente,
                                                      rfc=:rfc, tel1=:tel1, ext1=:ext1, tel2=:tel2, ext2=:ext2, contacto=:contacto, contacto2=:contacto2,
                                                      diasfact=:diasfact, nivel=:nivel, prospecto=:prospecto, id_cartera=:id_cartera, estatus=:estatus
                                            WHERE id=:id'
                                                    ,[
                                                        'id'              => $id,
                                                        'clave'           => $req -> clave ,      'nombre' 	     => $req -> nombre ,
                                                        'id_zona'         => $req -> id_zona,     'direccion'    => $req -> direccion,
                                                        'id_ciudad' 	  => $req -> id_ciudad,   'cp' 	         => $req -> cp,
                                                        'razon_social'    => $req -> razon_social,'id_giro'      => $req -> id_giro,
                                                        'fuente'          => $req -> fuente,      'tipo_cliente' => $req -> tipo_cliente,
                                                        'rfc'             => $req -> rfc,         'tel1'  	     => $req -> tel1,
                                                        'ext1'            => $req -> ext1,        'tel2'         => $req -> tel2,
                                                        'ext2'            => $req -> ext2,        'contacto'     => $req -> contacto,
                                                        'contacto2'       => $req -> contacto2,   'diasfact'     => $req -> diasfact,
                                                        'nivel'           => $req -> nivel,       'prospecto'    => $req -> prospecto,
                                                        'id_cartera'      => $req -> id_cartera,  'estatus'      => $req -> estatus
                                                    ]);
        if($addcliente):
            return response("El cliente se actualizo correctamente",200);
        else:
            return response("Ocurrio un problema al actualizar el cliente, por favor intentelo mas tarde.",500);
        endif;
    }


}
