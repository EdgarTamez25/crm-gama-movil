<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login' ,'userController@IniciarSesion')   -> name('IniciarSesion');

//================================== COMPROMISOS ==================================================
	Route::post('compromisosxvend'           ,'compromisosController@CompromisosxVend')   -> name('CompromisosxVend');
	Route::post('reagendar'                  ,'compromisosController@Reagendar')          -> name('Reagendar');
	Route::post('confirmarcita'              ,'compromisosController@ConfirmarCita')      -> name('ConfirmarCita');
	Route::post('compromisos.hechos'         ,'compromisosController@CompromisosHechos')  -> name('CompromisosHechos');
	Route::post('addcompromiso'  	         ,'compromisosController@addcompromiso')      -> name('addcompromiso');
	Route::post('terminar.compromiso'        ,'compromisosController@TerminarCompromiso') -> name('TerminarCompromiso');
	Route::post('proyectos.cotizados'        ,'compromisosController@proyectosCotizados') -> name('proyectosCotizados');
	Route::post('fase.venta'  		         ,'compromisosController@FaseVenta') 		  -> name('FaseVenta');
	Route::post('pendientesxvend'            ,'pendientesController@PendientesxVend')     -> name('PendientesxVend');
//==================================== ENTREGAS ========================================================
	Route::post('entregas'                   ,'entregasController@Entregas')           -> name('Entregas');
	Route::post('numero.movim'               ,'entregasController@NumeroMovim')        -> name('NumeroMovim');
	Route::post('actualiza.entrega'          ,'entregasController@ActualizaEntrega')   -> name('ActualizaEntrega');
	Route::get('ver.resumen/{id}'   	     ,'historialController@VerResumen')        -> name('VerResumen');
	Route::get('resumen.compromiso/{id}'     ,'historialController@resumenCompromiso') -> name('resumenCompromiso');
//==================================== CLIENTES =====================================================
	Route::get('clientes'                    ,'clientesController@catClientes')        -> name('catClientes');            //PROBADA
	Route::post('cliente'                    ,'clientesController@add')                -> name('addCliente');             //PROBADA
	Route::put('cliente/{id}'                ,'clientesController@update')             -> name('updateCliente');          //PROBADA
//==================================== PROSPECTOS =====================================================
	Route::get('prospectos.id/{id}'          ,'prospectosController@ProspectosxId')    -> name('ProspectosxId');
	Route::post('add.prospecto'              ,'prospectosController@addProspecto')     -> name('addProspecto');
	Route::put('update.prospecto/{id}'       ,'prospectosController@UpdateProspecto')  -> name('UpdateProspecto');
//==================================== CATEGORIAS ==================================================
	Route::get('categorias'                  ,'categoriasController@categorias')       -> name('categorias');
//==================================== DEPARTAMENTOS ===============================================
	Route::get('departamentos'               ,'departamentosController@Departamentos') -> name('Departamentos');
//==================================== MATERIALES ==================================================
	Route::get('materiales/{dx}'             ,'materialesController@Materiales')       -> name('Materiales');
//==================================== ACABADOS   ==================================================
    Route::get('acabados/{dx}'               ,'acabadosController@Acabados')           -> name('Acabados');
//==================================== SOLICITUDES   ==================================================
    Route::get('solicitudes'                 ,'solicitudesController@solicitudes')     -> name('solicitudes');         //* PROBADA
    Route::post('add.Solicitud'              ,'solicitudesController@addSolicitud')    -> name('addSolicitud');        //* PROBADA
    Route::put('update.Solicitud/{id}'       ,'solicitudesController@UpdateSolicitud') -> name('UpdateSolicitud');     //! PROBADA
//==================================== DETALLE DE ACABADOS   =================================================
    Route::get('acabados/{dx}'           ,'acabadosController@Acabados')           -> name('Acabados');
//==================================== DETALLE DE SOLICITUD  ==================================================
    Route::get('detSolicitud'                ,'detSolicitudController@detSolicitud')       -> name('detSolicitud');             //* PROBADA
    Route::post('add.DetSolicitud'           ,'detSolicitudController@addDetSolicitud')    -> name('addDetSolicitud');          //* PROBADA
    Route::put('Update.DetSolicitud/{id}'    ,'detSolicitudController@UpdateDetSolicitud') -> name('UpdateDetSolicitud');       //! PROBADA
//==================================== DETALLE DE FLEXOGRAFÍA   ==================================================
    Route::get('detFlexografia'              ,'detFlexografiaController@detFlexografia') -> name('detFlexografia');     //* PROBADA
    Route::post('add.DetFlexo'               ,'detFlexografiaController@addDetFlexo')    -> name('addDetFlexo');        //* PROBADA
    Route::put('update.DetFlexo/{id}'        ,'detFlexografiaController@UpdateDetFlexo') -> name('UpdateDetFlexo');     //! PROBADA
//==================================== DETALLE DE DIGITAL   ==================================================
    Route::get('detDigital'                  ,'detDigitalController@detDigital')       -> name('detDigital');          //* PROBADA
    Route::post('add.DetDigital'             ,'detDigitalController@addDetDigital')    -> name('addDetDigital');       //* PROBADA
    Route::put('update.DetDigital/{id}'      ,'detDigitalController@UpdateDetDigital') -> name('UpdateDetDigital');    //! PROBADA
//==================================== DETALLE DE BORDADOS   ==================================================
    Route::get('detBordados'                 ,'detBordadosController@detBordados')     -> name('detBordados');             //* PROBADA
    Route::post('add.DetBordados'            ,'detBordadosController@addDetBordados')    -> name('addDetBordados');        //* PROBADA
    Route::put('update.DetBordados/{id}'     ,'detBordadosController@UpdateDetBordados') -> name('UpdateDetBordados');     //! PROBADA
//==================================== DETALLE DE OFFSET   ==================================================
    Route::get('detOffset'                   ,'detOffsetController@detOffset')       -> name('detOffset');              //* PROBADA
    Route::post('add.DetOffset'              ,'detOffsetController@addDetOffset')    -> name('addDetOffset');           //* PROBADA
    Route::put('update.DetOffset/{id}'       ,'detOffsetController@UpdateDetOffset') -> name('UpdateDetOffset');        //! PROBADA
//==================================== DETALLE DE SERIGRAFIA   ==================================================
    Route::get('detSerigrafia'               ,'detSerigrafiaController@detSerigrafia')       -> name('detSerigrafia');           //* PROBADA
    Route::post('add.DetSerigrafia'          ,'detSerigrafiaController@addDetSerigrafia')    -> name('addDetSerigrafia');        //* PROBADA
    Route::put('update.DetSerigrafia/{id}'   ,'detSerigrafiaController@UpdateDetSerigrafia') -> name('UpdateDetSerigrafia');     //! PROBADA
//==================================== DETALLE DE SUBLIMACIÓN   =================================================
    Route::get('sublimacion'                 ,'sublimacionController@sublimacion')       -> name('sublimacion');           //* PROBADA
    Route::post('add.Sublimacion'            ,'sublimacionController@addSublimacion')    -> name('addSublimacion');        //* PROBADA
    Route::put('update.Sublimacion/{id}'     ,'sublimacionController@UpdateSublimacion') -> name('UpdateSublimación');     //! PROBADA
//==================================== UV   =================================================
    Route::get('UV'                          ,'uvController@UV')       -> name('UV');           //* PROBADA
    Route::post('add.UV'                     ,'uvController@addUV')    -> name('addUV');        //* PROBADA
    Route::put('update.UV/{id}'              ,'uvController@UpdateUV') -> name('UpdateUV');     //! PROBADA
//==================================== SUAJES   =================================================
    Route::get('suajes'                      ,'suajesController@Suajes')       -> name('suajes');           //* PROBADA
    Route::post('add.Suajes'                 ,'suajesController@addSuajes')    -> name('addSuajes');        //* PROBADA
    Route::put('update.Suajes/{id}'          ,'suajesController@UpdateSuajes') -> name('UpdateSuajes');     //! PROBADA
//==================================== PLECAS   =================================================
    Route::get('plecas'                      ,'plecasController@plecas')       -> name('plecas');           //* PROBADA
    Route::post('add.Plecas'                 ,'plecasController@addPlecas')    -> name('addPlecas');        //* PROBADA
    Route::put('update.Plecas/{id}'          ,'plecasController@UpdatePlecas') -> name('UpdatePlecas');     //! PROBADA
	//  Route::get('compromisos.hechos/{id}'  ,'compromisosController@CompromisosHechos')   -> name('CompromisosHechos');
// 	Route::post('en.ruta'          ,'compromisosController@EnRuta')             -> name('EnRuta');


