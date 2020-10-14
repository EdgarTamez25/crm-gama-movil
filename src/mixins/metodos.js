export default {

	data () {
		return {
			
		}
	},
	
	methods: {
		traerFechaActual(){
			var f = new Date(); 
			return f.getFullYear() +'-'+ (f.getMonth() + 1 < 10? '0' + (f.getMonth() + 1): f.getMonth() + 1 ) +'-'+ (f.getDate()<10?'0'+f.getDate():f.getDate());
		},

		traerHoraActual(){
			var f = new Date(); 
			return (f.getHours()<10? '0'+f.getHours(): f.getHours()) + ':' + (f.getMinutes()<10? '0'+ f.getMinutes(): f.getMinutes())
		},

		traerMesActual(){
			var f = new Date();
			var primerDia = new Date(f.getFullYear(), f.getMonth(), 1).toISOString().substr(0, 10);
			var ultimoDia = new Date(f.getFullYear(), f.getMonth() + 1, 0).toISOString().substr(0, 10);
			const fecha = { fechaInicial: primerDia , fechaFinal: ultimoDia}
			return fecha;
		},

		consultar_Clientes(){  // AUTOCOMPLETE -> CLIENTES
			this.$http.get('clientes').then((response)=>{
				this.clientes = response.body //LLENNO ARRAY
			}).catch(error =>{
				console.log('error', error)
			})
		},

		consultar_Categorias(){ // CATEGORIAS
			this.$http.get('categorias').then((response)=>{
				this.categorias = response.body  // LLENO ARREGLO CON RESPUESTA COMPLETA
			}).catch(error =>{
				console.log('error', error)
			})
		},

		consultaDeptos(){
			this.deptos = [	{ id:1, nombre:'FLEXOGRAFÍA'},
											{ id:2, nombre:'BORDADOS'},
											{ id:3, nombre:'DIGITAL'},
											{ id:4, nombre:'OFFSET'},
											{ id:5, nombre:'SERIGRAFÍA'},
											{ id:6, nombre:'EMPAQUE'},
											{ id:7, nombre:'SUBLIMACIÓN'},
											{ id:8, nombre:'TAMPOGRAFÍA'},
											{ id:9, nombre:'UV'}
										]
		},

		consultaMateriales(depto_id){
			this.$http.get('materiales/'+ depto_id).then((response)=>{
				this.materiales = response.body  // LLENO ARREGLO CON RESPUESTA COMPLETA
			}).catch(error =>{
				console.log('error materiales', error)
			})
		},

		consultaAcabados(depto_id){
			this.$http.get('acabados/'+ depto_id).then((response)=>{
				this.acabados = response.body  
			}).catch(error =>{
				console.log('error acabados', error)
			})
		},
		
  }
}