import Vue from 'vue'

export default{
	namespaced: true,
	state:{
		prospectos: [],
		loading: true,
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		PROSPECTOS(state, data){
			state.prospectos = data
		},
	},
	actions:{
		consultaProspectos({commit}, id){
			commit('LOADING',true); commit('PROSPECTOS', []);

			Vue.http.get('prospectos.id/'+ id).then(response=>{
				commit('PROSPECTOS', response.body)
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false)) 
		},
  },

	getters:{
		Loading(state){
			return state.loading
		},
		getProspectos(state){
		  return state.prospectos
		},
	}
}