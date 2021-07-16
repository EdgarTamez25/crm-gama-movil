<template>
	<v-main class="pa-0">
		<v-row justify="center" no-gutters>
			<v-col cols="12">
				<v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
					<strong> {{alerta.text}} </strong>
					<template v-slot:action="{ attrs }">
						<v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
					</template>
				</v-snackbar>

				<v-card-actions class="pa-0" >
					<h3> <strong> {{ param === 1? 'Nuevo prospecto':'Editar prospecto' }}  </strong></h3> 
					<v-spacer></v-spacer>
					<v-btn color="error" small @click="$emit('modal',false)" text><v-icon>clear</v-icon></v-btn>
				</v-card-actions>

				<v-divider class="ma-2"></v-divider>
				<v-row>
					<v-col cols="12" >
						<v-text-field
							append-icon="person"
							label="Nombre"
							placeholder="Nombre del cliente"
							hide-details
							dense
							filled
							clearable
							v-model="nombre"
						></v-text-field>
					</v-col>

					<v-col cols="12" sm="6">
						<v-select
							:items="['Nacional','Internacional']"
							label="Tipo de Cliente"
							placeholder="Tipo de cliente"
							append-icon="gps_fixed"
							dense
							filled
							hide-details
							v-model="tipo_cliente"
						></v-select>
					</v-col>

					<v-col cols="12" sm="6">
						<v-select
							:items="niveles"
							item-value="id"
							item-text="nombre"
							label="Nivel del cliente"
							placeholder="Nivel del cliente"
							append-icon="how_to_reg"
							dense
							filled
							hide-details
							v-model="nivel"
							return-object
						></v-select>
					</v-col>

					<v-col cols="12" sm="6">
						<v-text-field
							append-icon="phone"
							label="Telefono "
							placeholder="Telefono"
							hide-details
							dense
							filled
							clearable
							v-model.number="tel1"
							type="number"
						></v-text-field>
					</v-col>
					
					<v-col cols="12" sm="6">
						<v-text-field
							append-icon="contact_mail"
							label="Contacto"
							placeholder="Contacto"
							hide-details
							dense
							filled
							clearable
							v-model="contacto"
						></v-text-field>
					</v-col>
				</v-row>
				
				<!-- //DIALOG PARA GUARDAR LA INFORMACION -->
				<v-card-actions>
					<v-spacer></v-spacer>
					 <v-btn small :disabled="overlay" persistent :loading="overlay" dark center class="white--text" color="success" @click="validaInfo" v-if="param === 1">
             Confirmar  
          </v-btn>
					<v-btn small :disabled="overlay" persistent :loading="overlay" dark center class="white--text" color="success" @click="validaInfo" v-else>
             Actualizar  
          </v-btn>
				</v-card-actions>
			</v-col>
		</v-row>

    <overlay v-if="overlay"/>

	</v-main >
</template>

<script>
	// import  SelectMixin from '@/mixins/SelectMixin.js';
	import {mapGetters, mapActions} from 'vuex'
	import metodos from '@/mixins/metodos.js';
  import overlay from '@/components/overlay.vue'
	
	export default {
		mixins:[metodos],
	  components: {
      overlay
		},
		props:[
			'param',
			'edit',
	  ],
	  data () {
			return {
				overlay : false,
				textDialog : "Guardando Información",
				Correcto   : false,
				textCorrecto: '',

				// VARIABLES PRINCIPALES
				nombre			: '',
				tipo_cliente: '',
				nivel       : {id:null, nombre:''},
				niveles     : [{id:1 , nombre:'A'},{id:2,nombre:'AA'},{id:3,nombre:'AAA'}],
				tel1        : '',
				contacto    : '',
			
			 // ALERTAS
				alerta: { 
						activo: false,
						text: '',
						color: 'error',
				},
				
				}
		},

		created(){
			this.validarModoVista() // VALIDO EL MODO DE LA VISTA
		},
			
		computed:{
			// IMPORTANDO USO DE VUEX - CLIENTES (GETTERS)
			...mapGetters('Prospectos'  ,['Loading','getProspectos']), // IMPORTANDO USO DE VUEX - Prospectos (GETTERS)
			...mapGetters('Usuarios'    ,['getUsuarios']), // IMPORTANDO USO DE VUEX - Prospectos (GETTERS)
		},

		watch:{
			edit: function(){
				this.validarModoVista();
			}
		},

		methods:{
			...mapActions('Prospectos'  ,['consultaProspectos']),
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),


			validarModoVista(){
				if(this.param === 2){
					// ASIGNAR VALORES AL FORMULARIO
					this.nombre 			= this.edit.nombre;
					this.tipo_cliente = this.edit.tipo_cliente === 1? 'Nacional':'Internacional'
					this.nivel 				= this.niveles[this.edit.nivel -1];
					this.tel1 				= this.edit.tel1;
					this.contacto     = this.edit.contacto;
				}else{
				this.limpiarCampos()
				}
			},

			validaInfo(){
				
				if(!this.nombre)	  	{ this.alerta = { activo: true, text:'NO PUEDES OMITIR EL NOMBRE DEL CLIENTE' , color:'error'}; return }
				if(!this.tipo_cliente){ this.alerta = { activo: true, text:'NO PUEDES OMITIR EL TIPO DE CLIENTE'    , color:'error'}; return }
				if(!this.nivel)				{ this.alerta = { activo: true, text:'NO PUEDES OMITIR EL NIVEL'    					, color:'error'}; return }
				if(!this.tel1)	      { this.alerta = { activo: true, text:'DEBES INGRESAR AL MENOS UN TELEFONO'    , color:'error'}; return }
				this.PrepararPeticion() // MANDO A FORMAR EL OBJETO PARA GUARDAR
			},

			PrepararPeticion(){
				this.overlay = true ;

				// FORMAR ARRAY A MANDAR
				const payload = new Object({
					fuente 			: this.getUsuarios.id,
					nombre			: this.nombre,
					tipo_cliente: this.tipo_cliente === 'Nacional'? 1:2,
					nivel       : this.nivel.id,
					tel1				: this.tel1,
					contacto		: this.contacto,
					prospecto   : 1,
					estatus     : 1,
					fecha       : this.traerFechaActual()
				})
				
				// VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.param === 1 ? this.Crear(payload): this.Actualizar(payload);
			},

			Crear(payload){
				this.$http.post('add.prospecto', payload).then((response)=>{
					this.TerminarProceso(response.body);					
				}).catch(err =>{
					this.alerta = { activo: true, text:err.bodyText , color:'error'};
					console.log('err',err)
				}).finally(()=>{ this.overlay = false})
			},

			Actualizar(payload){
				this.$http.put('update.prospecto/'+ this.edit.id, payload).then((response)=>{
					this.TerminarProceso(response.body);					
				}).catch( err =>{
					this.alerta = { activo: true, text:err.bodyText , color:'error'};
				}).finally(()=>{ this.overlay = false})
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.alerta = { activo: true, text: mensaje  , color:'success'};
				this.consultaProspectos(this.getUsuarios.id) //ACTUALIZAR CONSULTA DE CLIENTES
        this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
				const autorizadas = new Object({
						fecha     : this.traerFechaActual(),
						id_usuario: this.getUsuarios.id,
				});
        this.consultaAutorizados(autorizadas); // traer los pendientes por validar
				setTimeout(() => { me.$emit('modal',false)}, 1500);
				this.limpiarCampos();  //LIMPIAR FORMULARIO
			},

			limpiarCampos(){
				this.nombre = '';
				this.direccion = '',
				this.tipo_cliente = '';
				this.nivel = '';
				this.razon_social = '';
				this.rfc = '';
				this.nivel = {id:null,nombre:''};
				this.zona = {id:null,nombre:''};
				this.cartera = {id:null,nombre:''};
				this.tel1 = '';
				this.tel2 = '';
				this.contacto = '';
				this.diasFact = 0;
			}
		}
	}
</script>