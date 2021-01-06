import Vue from 'vue'
import store from '@/store'

export default{
	namespaced: true,
	state:{
	},

	mutations:{
  },
  
	actions:{

		consultaOT(parametros){
      console.log('revibo los params', parametros)
			return new Promise(resolve => {
				Vue.http.post('ordenes.trabajo.vend', parametros).then(response=>{
          console.log('response', response.body)
					resolve(response.body)
				}).catch((error)=>{
					console.log('error',error)
				})
			})
		},

		
  },

	getters:{

	}
}