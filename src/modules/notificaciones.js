import Vue from 'vue'
import store from '@/store'

export default{
	namespaced: true,
	state:{
		notificaciones: [],
		loading: true,
    cant_globos: 0,
	},

	mutations:{
		NOTIFICACIONES(state, data){
			state.notificaciones = data;
		},
		LOADING(state, data){
			state.loading = data; 
		},
    CANT_NOTIFICACIONES(state, data){
      state.cant_globos = data;
    }

	},
	actions:{

		consultaPendientesxValidar({commit}, id_usuario){
			return new Promise(resolve => {
        commit('LOADING', true); commit('NOTIFICACIONES', []);
				Vue.http.get('consulta.pendientes.x.validar/' + id_usuario ).then(response=>{
          commit('NOTIFICACIONES', response.body)
          commit('CANT_NOTIFICACIONES', response.body.length)
					// resolve(response.body)
				}).catch((error)=>{
					console.log('error',error)
				}).finally(()=>{
          commit('LOADING', false);
        })
			})
		},
  },

	getters:{
		Notificaciones(state){
		  return state.notificaciones;
		},

		Loading(state){
			return state.loading
		},
    
    Globos(state){
      return state.cant_globos
    }
		

	}
}