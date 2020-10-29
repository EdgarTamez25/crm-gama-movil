export default{
	namespaced: true,
	state:{
		solicitudes: [],
		consecutivo: 1, 
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
		
  },

	getters:{
		getSolicitudes(state){
		  return state.solicitudes;
		},

		consecutivo(state){
			return state.consecutivo;
		}

	}
}