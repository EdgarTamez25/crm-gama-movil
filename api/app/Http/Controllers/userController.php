<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL


class userController extends Controller
{
  public function IniciarSesion(Request $req){
		$Usuarios =[];
		if($Usuario = DB::select('SELECT * FROM users WHERE correo = ? AND password = ?',[$req -> correo, $req -> contrasenia])):
			return $Usuario;
		else:
			return 	$Usuarios;
		endif;

	}
}
