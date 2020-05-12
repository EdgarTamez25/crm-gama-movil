import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		compromisos: [],
		loading: true,
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		COMPROMISOS(state, data){
			state.compromisos = data
		},
	},
	actions:{
		consultaCompromisos({commit}, payload){
				commit('LOADING',true); commit('COMPROMISOS', []) // Limpio Arreglo y Genero Consulta

				Vue.http.post('compromisosxvend', payload).then(response=>{
					console.log('compromisos vx', response.body)
					commit('COMPROMISOS', response.body)
					// resolve(true)
				}).catch((error)=>{
					console.log('error',error)
				}).finally(() => commit('LOADING', false))
		
		},
  },

	getters:{
		Loading(state){
			return state.loading
		},

		getCompromisos(state){
		  return state.compromisos
		},

	}
}