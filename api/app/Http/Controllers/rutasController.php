<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL
// use App\compromisos; // USO DEL MODELO.

class rutasController extends Controller
{

	public function agregarRuta(Request $req){
		$addRuta = DB::insert('INSERT INTO rutas(id_vendedor , numruta , nombre, id_compromiso, fecha, estatus)
												VALUES(?,?,?,?,?,?)', [ $req -> id_vendedor  , $req -> numruta , $req -> nombre,     
																							$req -> id_compromiso, $req -> fecha, 0 ]);
		return "La ruta se agrego correctamente.";
	}

	public function getEnRuta(Request $req){
		$Rutas = DB::select(' SELECT r.id, r.id_vendedor, r.numruta, r.nombre, r.fecha as fecha_ruta, r.hora_inicio, r.hora_fin, r.id_compromiso,
																c.tipo_compromiso, c.id_categoria, cat.nombre as nomcatego, c.fecha, c.hora,
																c.id_cliente, cli.nombre as nomcli, cli.tel1, c.enruta
													FROM rutas r LEFT JOIN compromisos c  ON r.id_compromiso = c.id
																		   LEFT JOIN categorias cat ON c.id_categoria  = cat.id
																		   LEFT JOIN clientes cli   ON c.id_cliente    = cli.id
													WHERE c.id_vendedor = ? AND c.fecha = ? ',[$req -> id_vendedor, $req -> fecha]);
		
		return $Rutas;
	}

	public function updateEnRuta($id, Request $req){
		$data = DB::update('UPDATE rutas SET id_vendedor=:id_vendedor, numruta=:numruta, nombre=:nombre, 
																				 id_compromiso=:id_compromiso, fecha=:fecha, estatus=:estatus WHERE id =:id',
													['id_vendedor'		=> $req -> id_vendedor,
													 'numruta'				=> $req -> numruta,
													 'nombre'					=> $req -> nombre,
													 'id_compromiso'  => $req -> id_compromiso, 
													 'fecha'  				=> $req -> fecha, 
													 'estatus'  			=> $req -> estatus, 
													 'id'							=> $id	]
												);
			
			return 'La ruta se actualizo correctamente';
	}


}
