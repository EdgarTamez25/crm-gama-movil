import Vue from 'vue'
import store from '@/store'

export default{
	namespaced: true,
	state:{
		solicitudes: [],
		consecutivo: 1, 
		loading: true,
		solicitudes_vend: [],
		partidas: []
	},

	mutations:{
		SOLICITUDES(state, data){
			state.solicitudes.push(data)
			state.consecutivo = state.consecutivo + 1
		},
		PUTSOLICITUDES(state, payload){

			for(let i =0; i < state.solicitudes.length;i++){
				if(state.solicitudes[i].id === payload.id){
					state.solicitudes[i] = payload;
				}
			}
			//!NUNCA HAGAN ESTO AMIGITOS - SOLCION POCO OPTIMA
			var temporal = state.solicitudes;
			state.solicitudes = [];
			state.solicitudes = temporal;
		},
		DELETE_SOLICITUD(state,id){
			for(let i =0; i < state.solicitudes.length;i++){
				if(state.solicitudes[i].id === id){
					state.solicitudes.splice(i,1);
				}
			}
		},
		VACIAR_SOLICITUD(state){
			state.solicitudes = [];
			state.consecutivo = 1; 
		},
		LOADING(state, data){
			state.loading = data; 
		},
		SOLICITUDES_VEND(state, data){
			state.solicitudes_vend = data
		},
		PARTIDAS(state, data){
			state.partidas = data;
		}

	},
	actions:{
		agregaProducto({commit}, payload){
			return new Promise(resolve => {
        commit('SOLICITUDES', payload);
        resolve(true)
			})
		},

		actualizaProducto({commit},payload){
			return new Promise(resolve => {
        commit('PUTSOLICITUDES', payload);
        resolve(true)
			})
		},

		eliminaProducto({commit}, id){
			return new Promise(resolve => {
        commit('DELETE_SOLICITUD', id);
        resolve(true)
			})
		},
		vaciaSolicitudes({commit}){
			commit('VACIAR_SOLICITUD');
		},

		consultaSolicitudes({commit}, parametros){
			return new Promise(resolve => {
				Vue.http.post('solicitudes.vend', parametros).then(response=>{
					resolve(response.body)
				}).catch((error)=>{
					console.log('error',error)
				})
			})
		},

		PartidasEncontradas({ commit }, payload){
			return new Promise(resolve => {
        commit('PARTIDAS', payload);
        resolve(true)
			})
		}

		
  },

	getters:{
		getSolicitudes(state){
		  return state.solicitudes;
		},

		getPartidas(state){
			return state.partidas;
		},

		consecutivo(state){
			return state.consecutivo;
		},

		Loading(state){
			return state.loading
		},

		

	}
}