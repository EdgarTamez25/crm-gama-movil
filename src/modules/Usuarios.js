import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		usuarios: [],
		loger: false,
	},

	mutations:{
		LOGEAR(state, data){
			state.loger = data; 
		},
		USUARIOS(state, data){
			state.usuarios = data
		},
		SALIR(state){
			state.usuarios = [];
			state.loger = false;
		}
	},
	actions:{

		Login({commit}, payload){
			return new Promise((resolve, reject) => {
			  Vue.http.post('login', payload).then(response =>{
					if(response.body.length){
						resolve(true)
						commit('USUARIOS', response.body[0]);
					}else{
						resolve(false)
					}
			  }).catch((error)=>{
					reject(error)
				})
			})
		},

		Logear({commit}, data){
			commit('LOGEAR', data);
		},

		Salir({commit}){
			commit('SALIR')
		}


  },

	getters:{
		Logeado(state){
			return state.loger;
		},

		getUsuarios(state){
		  return state.usuarios;
		},

	}
}