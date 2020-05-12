<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

////================================== COMPROMISOS ==================================================
Route::post('compromisosxvend' ,'compromisosController@CompromisosxVend')   -> name('CompromisosxVend');
Route::post('reagendar'        ,'compromisosController@Reagendar')          -> name('Reagendar');
Route::post('confirmarcita'    ,'compromisosController@ConfirmarCita')      -> name('ConfirmarCita');
Route::post('pendientesxvend'  ,'pendientesController@PendientesxVend')   -> name('PendientesxVend');


Route::post('en.ruta'          ,'compromisosController@EnRuta')             -> name('EnRuta');

////================================== RUTAS ========================================================
Route::post('getenruta'      ,'rutasController@getEnRuta')   -> name('getEnRuta');
Route::post('agregar.ruta'   ,'rutasController@agregarRuta') -> name('agregarRuta');
Route::put('put.en.ruta/{id}','rutasController@updateEnRuta') -> name('updateEnRuta');












