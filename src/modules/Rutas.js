import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		loading: true,
		en_ruta:[],
		rutasDisponibles:[{ id: 1 , nombre: 'Ruta 1' },
											{ id: 2 , nombre: 'Ruta 2' },
											{ id: 3 , nombre: 'Ruta 3' },
											{ id: 4 , nombre: 'Ruta 4' }],
		ruta1: [],
		ruta2: [],
		ruta3: [],
		ruta4: [],	
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		ENRUTA(state, data){
			state.en_ruta = data
		},
		RUTA1(state, ruta1){
			state.ruta1 = ruta1  
		},
		RUTA2(state, ruta2){
			state.ruta2 = ruta2  
		},
		RUTA3(state, ruta3){
			state.ruta3 = ruta3  
		},
		RUTA4(state, ruta4){
			state.ruta4 = ruta4  
		}
	},

	actions:{
		consultaEnRuta({commit,dispatch}, payload){
			commit('LOADING',true); commit('ENRUTA', []) // Limpio Arreglo y Genero Consulta
			
			Vue.http.post('getenruta', payload).then(response=>{
				// console.log('en ruta vuex', response.body)
				dispatch('formarRuta', response.body)
				commit('ENRUTA', response.body)
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false)) 
		},

		formarRuta({commit}, rutas){
			var ruta1 =[]; var ruta2=[]; var ruta3=[]; var ruta4=[];
			for(var i =0; i< rutas.length; i++){
				if(rutas[i].numruta === 1){
					ruta1.push({	enruta				 : rutas[i].enruta,
												fecha					 : rutas[i].fecha,
												fecha_ruta	   : rutas[i].fecha_ruta,
												hora					 : rutas[i].hora,
												hora_fin			 : rutas[i].hora_fin,
												hora_inicio		 : rutas[i].hora_inicio,
												id						 : rutas[i].id,
												id_categoria	 : rutas[i].id_categoria,
												id_cliente		 : rutas[i].id_cliente,
												id_compromiso  : rutas[i].id_compromiso,
												id_vendedor		 : rutas[i].id_vendedor,
												nombre				 : rutas[i].nombre,
												nomcatego			 : rutas[i].nomcatego,
												nomcli				 : rutas[i].nomcli,
												numruta				 : rutas[i].numruta,
												tel1					 : rutas[i].tel1,
												tipo_compromiso: rutas[i].tipo_compromiso})
				}else if(rutas[i].numruta === 2){
					ruta2.push({	enruta				 : rutas[i].enruta,
												fecha					 : rutas[i].fecha,
												fecha_ruta	   : rutas[i].fecha_ruta,
												hora					 : rutas[i].hora,
												hora_fin			 : rutas[i].hora_fin,
												hora_inicio		 : rutas[i].hora_inicio,
												id						 : rutas[i].id,
												id_categoria	 : rutas[i].id_categoria,
												id_cliente		 : rutas[i].id_cliente,
												id_compromiso  : rutas[i].id_compromiso,
												id_vendedor		 : rutas[i].id_vendedor,
												nombre				 : rutas[i].nombre,
												nomcatego			 : rutas[i].nomcatego,
												nomcli				 : rutas[i].nomcli,
												numruta				 : rutas[i].numruta,
												tel1					 : rutas[i].tel1,
												tipo_compromiso: rutas[i].tipo_compromiso})
				}else if(rutas[i].numruta === 3){
					ruta3.push({	enruta				 : rutas[i].enruta,
												fecha					 : rutas[i].fecha,
												fecha_ruta	   : rutas[i].fecha_ruta,
												hora					 : rutas[i].hora,
												hora_fin			 : rutas[i].hora_fin,
												hora_inicio		 : rutas[i].hora_inicio,
												id						 : rutas[i].id,
												id_categoria	 : rutas[i].id_categoria,
												id_cliente		 : rutas[i].id_cliente,
												id_compromiso  : rutas[i].id_compromiso,
												id_vendedor		 : rutas[i].id_vendedor,
												nombre				 : rutas[i].nombre,
												nomcatego			 : rutas[i].nomcatego,
												nomcli				 : rutas[i].nomcli,
												numruta				 : rutas[i].numruta,
												tel1					 : rutas[i].tel1,
												tipo_compromiso: rutas[i].tipo_compromiso})
				}else{
					ruta4.push({	enruta				 : rutas[i].enruta,
												fecha					 : rutas[i].fecha,
												fecha_ruta	   : rutas[i].fecha_ruta,
												hora					 : rutas[i].hora,
												hora_fin			 : rutas[i].hora_fin,
												hora_inicio		 : rutas[i].hora_inicio,
												id						 : rutas[i].id,
												id_categoria	 : rutas[i].id_categoria,
												id_cliente		 : rutas[i].id_cliente,
												id_compromiso  : rutas[i].id_compromiso,
												id_vendedor		 : rutas[i].id_vendedor,
												nombre				 : rutas[i].nombre,
												nomcatego			 : rutas[i].nomcatego,
												nomcli				 : rutas[i].nomcli,
												numruta				 : rutas[i].numruta,
												tel1					 : rutas[i].tel1,
												tipo_compromiso: rutas[i].tipo_compromiso})
				}
			}
			commit('RUTA1', ruta1)
			commit('RUTA2', ruta2)
			commit('RUTA3', ruta3)
			commit('RUTA4', ruta4)
		}
  },

	getters:{
		Loading(state){
			return state.loading
		},

		rutasDisponible(state){
			return state.rutasDisponibles;
		},

		getEnRuta(state){
			return state.en_ruta
		},

		ruta1(state){
			return state.ruta1
		},
		ruta2(state){
			return state.ruta2
		},
		ruta3(state){
			return state.ruta3
		},
		ruta4(state){
			return state.ruta4
		},
	}
}