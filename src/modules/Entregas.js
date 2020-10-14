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
				console.log('entregas', response.body)
				for(let i =0; i <response.body.length; i++){
					var ciudad = response.body[i].ciudad ? response.body[i].ciudad + ',' : '' ;
					var estado = response.body[i].estado ? response.body[i].estado + ',' : '' ;
					var pais   = response.body[i].pais   ? response.body[i].pais   + ',' : '' ;
					var cp     = response.body[i].cp     ? response.body[i].cp     + ',' : '' ;
				
					entregas.push({ id: response.body[i].id, 
													id_compromiso: response.body[i].id_compromiso,
													fecha_entrega : moment(response.body[i].fecha_entrega).format('LL'),
													hora_entrega  : response.body[i].hora_entrega,
													direccion: response.body[i].direccion + '' + ciudad + '' +  estado + '' + pais + ''  + cp,
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