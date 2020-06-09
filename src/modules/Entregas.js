import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		entregas: [],
		loading: true,
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		ENTREGAS(state, data){
			state.entregas = data
		},
	},
	actions:{
		
		consultaEntregas({commit}, payload){
			var moment = require('moment'); moment.locale('es') /// inciar Moment 
			commit('LOADING',true); commit('ENTREGAS', []) // Limpio Arreglo y Genero Consulta

			Vue.http.post('entregas',payload).then(response=>{
				var entregas = []
				for(let i =0; i <response.body.length; i++){
					entregas.push({ id: response.body[i].id, 
													id_compromiso: response.body[i].id_compromiso,
													fecha_entrega : moment(response.body[i].fecha_entrega).format('LL'),
													hora_entrega  : response.body[i].hora_entrega,
													direccion: response.body[i].direccion,
													nomcli: response.body[i].nomcli,
													numfac: response.body[i].numfac,
													tel1: response.body[i].tel1,
													tel2: response.body[i].tel2,
													nummovim : response.body[i].nummovim
												})
				}
				commit('ENTREGAS', entregas);
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false))
		},
  },

	getters:{
		Loading(state){
			return state.loading;
		},

		getEntregas(state){
		  return state.entregas;
		},
	}
}