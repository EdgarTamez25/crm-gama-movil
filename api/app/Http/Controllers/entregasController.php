<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL

class entregasController extends Controller
{
    public function Entregas(Request $req){
        $entregas = DB::select('SELECT e.id, e.id_compromiso, cli.nombre as nomcli, cli.direccion, cli.tel1, 
                                       cli.tel2, e.fecha_entrega, e.hora_entrega, e.numfac, e.nummovim
                                    FROM entregas e LEFT JOIN compromisos c ON e.id_compromiso = c.id
                                                    LEFT JOIN clientes cli  ON c.id_cliente = cli.id
																WHERE e.id_chofer=? AND e.estatus = 0 ',[ $req -> id_chofer]);
				return $entregas;
    }

    public function NumeroMovim(Request $req){
        $numMovim = DB::update('UPDATE entregas SET nummovim=:nummovim 
                                 WHERE id=:id',['nummovim' => $req -> nummovim, 'id' => $req -> id]);
		return "El número de movimiento se agrego correctamente.";
    }


    public function ActualizaEntrega(Request $req){
        $numMovim = DB::update('UPDATE entregas SET obs=:obs,fecha_cierre=:fecha_cierre,hora_cierre=:hora_cierre,estatus=:estatus
																 WHERE id=:id',[ 'obs' 					=> $req -> obscierre, 
																								 'fecha_cierre' => $req -> fecha,
																								 'hora_cierre' 	=> $req -> hora,
																								 'estatus'		  => $req -> estatus,
																								 'id' 				  => $req -> id_entrega
																								]);
																							
		return "El estatus de la entrega se guardo correctamente.";
    
		}
    
}
