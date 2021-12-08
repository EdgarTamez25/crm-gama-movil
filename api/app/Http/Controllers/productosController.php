<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\productos;

class productosController extends Controller
{
    public function getAll()
    {
        $Productos = productos::all();
        return $Productos;
		}

		public function ProdxClixDeptos(Request $req)
		{
			$productos = DB::select('SELECT id, nombre, codigo 
																FROM prodxcli 
															 WHERE id_cliente = ?',[$req -> id_cliente]);
			return $productos? $productos : [];
		}
}
