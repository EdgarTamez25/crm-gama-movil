<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //USAR ESCRITURA SQL


class userController extends Controller
{
  public function IniciarSesion(Request $req){
		$correo 	= $req -> correo;
		$usuario 	= $req -> usuario;
		$contrasenia = $req -> contrasenia;
		$Usuarios =[];

		if($Usuario = DB::select('SELECT * FROM users WHERE correo = ? AND password = ? OR usuario = ? AND password = ?'
			,[$correo,$contrasenia,$usuario,$contrasenia])):
			return $Usuario;
		else:
			return 	$Usuarios;
		endif;
	}
}
