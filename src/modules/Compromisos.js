import Vue from 'vue'
import Vuex from 'vuex'

export default{
	namespaced: true,
	state:{
		compromisos: [],
		compromisos_hechos:[],
		loading: true,
		proyectos: []
	},

	mutations:{
		LOADING(state, data){
			state.loading = data; 
		},
		COMPROMISOS(state, data){
			state.compromisos = data
		},
		COMPROMISOSHECHOS(state, data){
			state.compromisos_hechos = data
		},
		PROYECTOS(state, data){
			state.proyectos = data;
		}
	},
	actions:{
		consultaCompromisos({commit}, payload){
				commit('LOADING',true); commit('COMPROMISOS', []) // Limpio Arreglo y Genero Consulta

				Vue.http.post('compromisosxvend', payload).then(response=>{
					commit('COMPROMISOS', response.body)
				}).catch((error)=>{
					console.log('error',error)
				}).finally(() => commit('LOADING', false))
		
		},

		proyectosCotizados({commit}, payload){
			commit('LOADING',true); commit('PROYECTOS', []) // Limpio Arreglo y Genero Consulta

			Vue.http.post('proyectos.cotizados', payload).then(response=>{
				console.log('proyecto cotizado', response.body)
				commit('PROYECTOS', response.body)
			}).catch((error)=>{
				console.log('error',error)
			}).finally(() => commit('LOADING', false))
		},
			
		consultaCompromisoshechos({commit}, id){
			var moment = require('moment'); moment.locale('es') /// inciar Moment 
			commit('LOADING',true); commit('COMPROMISOSHECHOS', []) // Limpio Arreglo y Genero Consulta

			Vue.http.get('compromisos.hechos/'+ id,).then(response=>{
				console.log('compromisos hechos', response.body)
				var copromisosHechos = []
				for(let i =0; i <response.body.length; i++){
					copromisosHechos.push({ id			: response.body[i].id, 
											id_categoria: response.body[i].id_categoria,
											id_cliente  : response.body[i].id_cliente,
											id_usuario  : response.body[i].id_usuario,
											id_vendedor : response.body[i].id_vendedor,
											nomcatego   : response.body[i].nomcatego,
											nomcli      : response.body[i].nomcli,
											nomuser     : response.body[i].nomuser,
											nomvend     : response.body[i].nomvend,
											fecha       : moment(response.body[i].fecha).format('LL'),
											hora        : moment(response.body[i].hora).calendar(),
											fecha_fin   : moment(response.body[i].fecha_fin).format('LL'),
											hora_fin    : moment(response.body[i].hora_fin).calendar(),
											fecha_cierre: moment(response.body[i].fecha_cierre).format('LL'),
											hora_cierre : moment(response.body[i].hora_cierre).calendar(),
											comentarios : response.body[i].comentarios,
											confirma_cita: response.body[i].confirma_cita,
											cumplimiento : response.body[i].cumplimiento,
											estatus 	 : response.body[i].estatus,
											fase_venta   : response.body[i].fase_venta,
											obs_usuario  : response.body[i].obs_usuario,
											tel1         : response.body[i].tel1,
											tel2 		 : response.body[i].tel2,
											tipo_compromiso: response.body[i].tipo_compromiso
										 })
				}

				commit('COMPROMISOSHECHOS', copromisosHechos);
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

		getCompromisosHechos(state){
			return state.compromisos_hechos;
		},

		getproyectos(state){
			return state.proyectos;
		}

	}
}