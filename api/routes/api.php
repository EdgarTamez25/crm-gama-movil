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

Route::post('login' ,'userController@IniciarSesion')   -> name('IniciarSesion');

////================================== COMPROMISOS ==================================================
Route::post('compromisosxvend' ,'compromisosController@CompromisosxVend')   -> name('CompromisosxVend');
Route::post('reagendar'        ,'compromisosController@Reagendar')          -> name('Reagendar');
Route::post('confirmarcita'    ,'compromisosController@ConfirmarCita')      -> name('ConfirmarCita');
// Route::get('compromisos.hechos/{id}'  ,'compromisosController@CompromisosHechos')   -> name('CompromisosHechos');
Route::post('compromisos.hechos'  ,'compromisosController@CompromisosHechos') -> name('CompromisosHechos');  
Route::post('addcompromiso'  		  ,'compromisosController@addcompromiso')     -> name('addcompromiso');


Route::post('terminar.compromiso'	,'compromisosController@TerminarCompromiso')   -> name('TerminarCompromiso'); 
Route::post('proyectos.cotizados' ,'compromisosController@proyectosCotizados')   -> name('proyectosCotizados'); 
Route::post('fase.venta'  				,'compromisosController@FaseVenta') 				   -> name('FaseVenta');

// PENDIENTES}
Route::post('pendientesxvend'  ,'pendientesController@PendientesxVend')   -> name('PendientesxVend');
// Route::post('en.ruta'          ,'compromisosController@EnRuta')             -> name('EnRuta');

////================================== ENTREGAS ========================================================
Route::post('entregas'          ,'entregasController@Entregas')         -> name('Entregas');
Route::post('numero.movim'      ,'entregasController@NumeroMovim')      -> name('NumeroMovim');
Route::post('actualiza.entrega' ,'entregasController@ActualizaEntrega') -> name('ActualizaEntrega');

Route::get('ver.resumen/{id}'   ,'historialController@VerResumen')      -> name('VerResumen');
Route::get('resumen.compromiso/{id}' ,'historialController@resumenCompromiso') -> name('resumenCompromiso');

//==================================== CLIENTES =====================================================
Route::get('clientes'    ,'clientesController@catClientes') -> name('catClientes');            //PROBADA
Route::post('cliente'    ,'clientesController@add')         -> name('addCliente');             //PROBADA
Route::put('cliente/{id}','clientesController@update')      -> name('updateCliente');          //PROBADA

//==================================== PROSPECTOS =====================================================
Route::get('prospectos.id/{id}'   ,'prospectosController@ProspectosxId')   -> name('ProspectosxId');
Route::post('add.prospecto'       ,'prospectosController@addProspecto')    -> name('addProspecto');
Route::put('update.prospecto/{id}','prospectosController@UpdateProspecto') -> name('UpdateProspecto');



























