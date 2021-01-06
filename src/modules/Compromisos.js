import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		compromisos: [],
		compromisos_hechos:[],
		loading: true,
		proyectos: [],
		seguimiento:[]
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		COMPROMISOS(state, data){
			state.compromisos = data
		},
		COMPROMISOSHECHOS(state, data){
			state.compromisos_hechos = data
		},
		PROYECTOS(state, data){
			state.proyectos = data;
		},
		SEGUIMIENTO(state, data){
			state.seguimiento = data 
		}
	},

	actions:{
		consultaCompromisos({commit}, payload){
				commit('LOADING',true); commit('COMPROMISOS', []) // Limpio Arreglo y Genero Consulta
				Vue.http.post('compromisosxvend', payload).then(response=>{
					commit('COMPROMISOS', response.body)
				}).catch((error)=>{
					console.log('error',error)
				}).finally(() => commit('LOADING', false))
		
		},

		proyectosCotizados({commit}, payload){
			commit('LOADING',true); commit('PROYECTOS', []) // Limpio Arreglo y Genero Consulta

			Vue.http.post('proyectos.cotizados', payload).then(response=>{
				// console.log('proyecto cotizado', response.body)
				commit('PROYECTOS', response.body)
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false))
		},
			
		consultaCompromisoshechos({commit}, payload){
			var moment = require('moment'); moment.locale('es') /// inciar Moment 
			commit('LOADING',true); commit('COMPROMISOSHECHOS', []) // Limpio Arreglo y Genero Consulta

			Vue.http.post('compromisos.hechos',payload).then(response=>{
				// let copromisosHechos = []
			
				// for(let i =0; i <response.body.length; i++){
				// 	copromisosHechos.push({ id			: response.body[i].id, 
																	
				// 													id_cliente  : response.body[i].id_cliente,
				// 													nomcli      : response.body[i].nomcli,
				// 													fecha       : moment(response.body[i].fecha).format('LL'),
				// 													hora        : response.body[i].hora,

				// 												})
				// }

				commit('COMPROMISOSHECHOS', response.body);
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false))
		},
		
		// consultaSeguimiento({commit}, id){
		// 	var resumen = [];
		// 	commit('LOADING',true); commit('SEGUIMIENTO', [])

		// 	Vue.http.get('ver.resumen/'+id).then(response =>{
		// 		for(let i=0;i<response.body.length;i++){
		// 			resumen.push({ id_compromiso : response.body[i].id_compromiso,
		// 										 fase_venta 	 : response.body[i].fase_venta,
		// 										 historial     : response.body[i].historial,
		// 									   nomcli        : response.body[i].nomcli,
		// 										 show          : false
		// 										})
		// 		}
		// 		// console.log('resumen', resumen)
				
		// 		commit('SEGUIMIENTO', resumen)
		// 	}).catch(err =>{
		// 		console.log('err', err)
		// 	}).finally(() => commit('LOADING', false))
		// },
		
  },

	getters:{
		Loading(state){
			return state.loading
		},

		getCompromisos(state){
		  return state.compromisos
		},

		getCompromisosHechos(state){
			return state.compromisos_hechos;
		},

		getproyectos(state){
			return state.proyectos;
		},

		getSeguimiento(state){
			return state.seguimiento;
		}

	}
}