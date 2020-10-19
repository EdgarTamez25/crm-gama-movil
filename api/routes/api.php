<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login' ,'userController@IniciarSesion')   -> name('IniciarSesion');

////================================== COMPROMISOS ==================================================
	Route::post('compromisosxvend'    ,'compromisosController@CompromisosxVend')   -> name('CompromisosxVend');
	Route::post('reagendar'           ,'compromisosController@Reagendar')          -> name('Reagendar');
	Route::post('confirmarcita'       ,'compromisosController@ConfirmarCita')      -> name('ConfirmarCita');
	Route::post('compromisos.hechos'  ,'compromisosController@CompromisosHechos')  -> name('CompromisosHechos');
	Route::post('addcompromiso'  	  ,'compromisosController@addcompromiso')      -> name('addcompromiso');
	Route::post('terminar.compromiso' ,'compromisosController@TerminarCompromiso') -> name('TerminarCompromiso');
	Route::post('proyectos.cotizados' ,'compromisosController@proyectosCotizados') -> name('proyectosCotizados');
	Route::post('fase.venta'  		  ,'compromisosController@FaseVenta') 		   -> name('FaseVenta');
	Route::post('pendientesxvend'     ,'pendientesController@PendientesxVend')     -> name('PendientesxVend');

////================================== ENTREGAS ========================================================
	Route::post('entregas'               ,'entregasController@Entregas')           -> name('Entregas');
	Route::post('numero.movim'           ,'entregasController@NumeroMovim')        -> name('NumeroMovim');
	Route::post('actualiza.entrega'      ,'entregasController@ActualizaEntrega')   -> name('ActualizaEntrega');
	Route::get('ver.resumen/{id}'   	 ,'historialController@VerResumen')        -> name('VerResumen');
	Route::get('resumen.compromiso/{id}' ,'historialController@resumenCompromiso') -> name('resumenCompromiso');
//==================================== CLIENTES =====================================================
	Route::get('clientes'                ,'clientesController@catClientes')        -> name('catClientes');            //PROBADA
	Route::post('cliente'                ,'clientesController@add')                -> name('addCliente');             //PROBADA
	Route::put('cliente/{id}'            ,'clientesController@update')             -> name('updateCliente');          //PROBADA

//==================================== PROSPECTOS =====================================================
	Route::get('prospectos.id/{id}'      ,'prospectosController@ProspectosxId')    -> name('ProspectosxId');
	Route::post('add.prospecto'          ,'prospectosController@addProspecto')     -> name('addProspecto');
	Route::put('update.prospecto/{id}'   ,'prospectosController@UpdateProspecto')  -> name('UpdateProspecto');
////================================== CATEGORIAS ==================================================
	Route::get('categorias'              ,'categoriasController@categorias')       -> name('categorias');
//==================================== DEPARTAMENTOS ===============================================
	Route::get('departamentos'           ,'departamentosController@Departamentos') -> name('Departamentos');
//==================================== MATERIALES ==================================================
	Route::get('materiales/{dx}'         ,'materialesController@Materiales')       -> name('Materiales');
//==================================== ACABADOS   ==================================================
    Route::get('acabados/{dx}'           ,'acabadosController@Acabados')           -> name('Acabados');
//==================================== SOLICITUDES   ==================================================
    Route::get('solicitudes'             ,'solicitudesController@Solicitudes')     -> name('Solicitudes');         //* PROBADA
    Route::post('add.Solicitud'          ,'solicitudesController@addSolicitud')    -> name('addSolicitud');        //* PROBADA
    Route::put('update.Solicitud/{id}'   ,'solicitudesController@UpdateSolicitud') -> name('UpdateSolicitud');     //* PROBADA

//==================================== DETALLE DE SOLICITUD  ==================================================
    Route::get('detSolicitud'            ,'detSolicitudController@detSolicitud')   -> name('detSolicitud');             //* PROBADA
    Route::post('add.DetSolicitud'       ,'detSolicitudController@addDetSolicitud')-> name('addDetSolicitud');          //* PROBADA
    Route::put('Update.DetSolicitud/{id}','detSolicitudController@UpdateDetSolicitud') -> name('UpdateDetSolicitud');   //! PROBADA


	//  Route::get('compromisos.hechos/{id}'  ,'compromisosController@CompromisosHechos')   -> name('CompromisosHechos');
// 	Route::post('en.ruta'          ,'compromisosController@EnRuta')             -> name('EnRuta');


























