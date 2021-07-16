import Vue from 'vue'
import store from '@/store'

export default{
	namespaced: true,
	state:{
		notificaciones: [],
		loading: true,
    cant_globos: 0,
		autorizadas: [],
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
    },
		AUTORIZADAS(state, data){
			state.autorizadas = data;
		}

	},
	actions:{

		consultaPendientesxValidar({commit}, id_usuario){
			const payload = new Object({ id_usuario: id_usuario, estatus: 2 });

			return new Promise(resolve => {
        commit('LOADING', true); commit('NOTIFICACIONES', []);
				Vue.http.post('consulta.pendientes.x.validar',payload ).then(response=>{
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

		consultaAutorizados({commit}, payload){
			
			return new Promise(resolve => {
        commit('LOADING', true); commit('AUTORIZADAS', []);
				Vue.http.post('consulta.cot.autorizadas',payload ).then(response=>{
          commit('AUTORIZADAS', response.body)
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

		Autorizadas(state){
			return state.autorizadas;
		},

		Loading(state){
			return state.loading
		},
    
    Globos(state){
      return state.cant_globos
    }
		

	}
}