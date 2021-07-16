<template>
	<v-row justify="center" class="pa-3 ">
		<v-col cols="12" class="">
			
			<v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
				<strong> {{alerta.text}} </strong>
				<template v-slot:action="{ attrs }">
					<v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
				</template>
			</v-snackbar>


			<v-card-actions class="">
				<h3> <strong> {{ param === 1? 'Nuevo Compromiso':'Editar Compromiso' }} </strong></h3> 
				<v-spacer></v-spacer>
				<v-btn color="error" small @click="$emit('modal',false)" text><v-icon>clear</v-icon></v-btn>
			</v-card-actions>
			
			<v-row class="mt-1">
				<v-col cols="12" sm="6" > <!-- CLIENTE -->
					<v-autocomplete
						:items="clientes" v-model="cliente" item-text="nombre" item-value="id" label="Clientes" 
						dense filled hide-details color="celeste" append-icon="people" return-object
					></v-autocomplete>
				</v-col>

				<v-col cols="12" sm="6" > 
					<v-select
							v-model="tipo" :items="tipos" item-text="nombre" item-value="id" filled color="celeste"
						dense hide-details  label="Tipo de compromiso" return-object placeholder ="Tipo de compromiso"
					></v-select>
				</v-col> 

				<v-col cols="12" sm="6" > 
					<v-select
						v-model="categoria" :items="categorias" item-text="nombre" item-value="id" label="Categoria"  
							placeholder ="Categorias" filled dense hide-details append-icon="ballot" return-object
					></v-select>
				</v-col>

				<v-col cols="12" sm="6"> <!-- FECHA DE INICIO -->
					<v-dialog ref="fecha_compromiso" v-model="fechamodal" :return-value.sync="fecha" persistent width="290px">
						<template v-slot:activator="{ on }">
							<v-text-field
								v-model="fecha" label="Fecha de compromiso" append-icon="event" readonly v-on="on"
								filled dense hide-details color="celeste"
							></v-text-field>
						</template>
						<v-date-picker v-model="fecha" locale="es-es" color="rosa"  scrollable>
							<v-spacer></v-spacer>
							<v-btn text small color="gris" @click="fechamodal = false">Cancelar</v-btn>
							<v-btn dark small color="rosa" @click="$refs.fecha_compromiso.save(fecha)">OK</v-btn>
						</v-date-picker>
					</v-dialog>
				</v-col>

				<v-col cols="12" sm="6"> <!-- HORA DEL INICIO -->
					<v-dialog ref="hora_compromiso" v-model="horamodal" :return-value.sync="hora" persistent width="290px" >
						<template v-slot:activator="{ on }">
							<v-text-field
								v-model="hora" label="Hora del compromiso" append-icon="access_time" readonly v-on="on"
								filled dense hide-details color="celeste"
							></v-text-field>
						</template>
						<v-time-picker v-if="horamodal" locale="es-es" color="rosa" v-model="hora" full-width 	>
							<v-spacer></v-spacer>
							<v-btn small text color="gris" @click="horamodal = false">Cancel</v-btn>
							<v-btn small dark color="rosa" @click="$refs.hora_compromiso.save(hora)">OK</v-btn>
						</v-time-picker>
					</v-dialog>
				</v-col>

				<v-col cols="12"  > <!-- COMENARIO -->
					<v-textarea
						v-model ="obs" outlined label="Comentario" placeholder="Agregar un comentario ..."
						rows="2" hide-details dense color="celeste"
					></v-textarea>
				</v-col>

			</v-row>

			<!-- //DIALOG PARA GUARDAR LA INFORMACION -->
			<v-card-actions>
				<v-spacer></v-spacer>
					<v-btn small :disabled="overlay" persistent :loading="overlay" dark center  color="success" @click="validaInfo" >
					Guardar Información
				</v-btn>
				<!-- <v-btn small :disabled="dialog" persistent :loading="dialog" dark center class="white--text" color="success" @click="validaInfo" v-else>
						Actualizar  
				</v-btn> -->

				<!-- <v-dialog v-model="overlay" hide-overlay persistent width="300">
					<v-card color="blue darken-4" dark >
						<v-card-text> <th style="font-size:17px;" align="center">{{ textDialog }}</th>
							<br>
							<v-progress-linear indeterminate color="white" class="mb-0" persistent></v-progress-linear>
						</v-card-text>
					</v-card>
				</v-dialog> -->

				<v-dialog v-model="confirmaProceso" persistent max-width="400" > <!-- PROCESO PARA TERMINAR  -->
					<v-card>
						<v-col cols="12" class="pa-4">
							<strong >EL COMPROMISO SE GUARDARA</strong> <br> ¿Esta seguro de continuar?
						</v-col>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn color="gris" text small @click="confirmaProceso = false">Cancelar</v-btn>
							<v-btn color="rosa" dark small @click="PrepararPeticion()">Continuar</v-btn>
						</v-card-actions>
					</v-card>
				</v-dialog> <!-- FIN DEL PROCESO PARA TERMINAR  -->

				<!-- <v-dialog v-model="Correcto" hide-overlay persistent width="350">
					<v-card color="success"  dark class="pa-3">
						<h3><strong>{{ textCorrecto }} </strong></h3>
					</v-card>
				</v-dialog> -->
			</v-card-actions>

		</v-col>
		
		<overlay v-if="overlay"/>
	</v-row>
</template>

<script>
	import  metodos from '@/mixins/metodos.js';
	import {mapGetters, mapActions} from 'vuex'
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
				// FECHA
				fecha						: '',
				fechamodal 			: false,
				fecha_compromiso: false,
				// HORA
				hora 					 : null,
        horamodal			 : false,
				hora_compromiso: false,
				// AUTOCOMPLETE
				cliente: { id:null, nombre:''},
				clientes: [],
				tipo: {id:null, nombre:''},
				tipos:[{id:1, nombre:'Cliente'},{id:2, nombre:'Prospectos'}],
				categoria    : { id:null, nombre:''},
				categorias   : [],
				obs:'',
				// ALERTAS
				alerta: { 
          activo: false,
          text: '',
          color: 'error',
        },

				overlay : false,

				// textDialog : "Guardando Información",
				// Correcto   : false,
				// textCorrecto: '',
				confirmaProceso: false
			}
		},
		
		created(){
			this.fecha =this.traerFechaActual();
			this.hora = this.traerHoraActual();
			this.consultar_Clientes()
			this.consultar_Categorias()
			this.validarModoVista() 	  // VALIDO EL MODO DE LA VISTA
		},
			
		computed:{
      ...mapGetters('Usuarios',['getUsuarios']),
		},

		watch:{
			edit: function(){
				this.validarModoVista();
			}
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']), 
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),


			validarModoVista(){
				if(this.param === 2){
					this.id_cliente 		= this.edit.id_cliente
					this.cliente        = this.edit.nomcli
					this.fecha 					= this.edit.fecha
					this.hora           = this.edit.hora
				}else{
				this.limpiarCampos()
				}
			},

			validaInfo(){

				var fc = this.fecha + " " + this.hora; var fa = this.traerFechaActual() + " " + this.traerHoraActual();
				if(!this.cliente.id)	{ this.alerta = { activo: true, text: 'NO PUEDES OMITIR EL CLIENTE'   , color:'error'}; return }
				if(!this.tipo.id)		  { this.alerta = { activo: true, text: 'NO PUEDES OMITIR EL TIPO'      , color:'error'}; return }
				if(!this.categoria.id){ this.alerta = { activo: true, text: 'NO PUEDES OMITIR LA CATEGORÍA' , color:'error'}; return }
				if(!this.fecha)				{ this.alerta = { activo: true, text: 'NO PUEDES OMITIR LA FECHA'     , color:'error'}; return }
				if(!this.hora)				{ this.alerta = { activo: true, text: 'NO PUEDES OMITIR LA HORA'      , color:'error'}; return }
				if(!this.obs)					{ this.alerta = { activo: true, text: 'DEBES AGREGAR UN COMENTARIO'   , color:'error'}; return }

				if(fc < fa){ this.alerta = { activo: true, text: 'LA FECHA Y HORA NO PUEDEN SER MENOR A LA ACTUAL.', color:'error'}; return}
				this.confirmaProceso = true
				// this.PrepararPeticion()
			},

			PrepararPeticion(){
				this.confirmaProceso = false ; 
				this.overlay = true; 
				// FORMAR ARRAY A MANDAR
				const payload = new Object({
					id_vendedor 		: this.getUsuarios.id,
					tipo						: this.tipo.id,
					id_categoria		: this.categoria.id,
					fecha						: this.fecha,
					hora	  				: this.hora,
					id_cliente 		  : this.cliente.id,
					obs							: this.obs,
					fuente      		: this.getUsuarios.id, // USUARIO QUE CREA EL REGISTRO
				})
													
				// VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.param === 1 ? this.Crear(payload): this.Actualizar(payload);
			},

			Crear(payload){
				this.$http.post('addcompromiso', payload).then((response)=>{
					this.TerminarProceso(response.bodyText);			
				}).catch(err =>{ 
					this.alerta = { activo: true, text: err.bodyText  , color:'error'};
					console.log('err', err)
				}).finally(() => this.overlay = false)
			},

			Actualizar(payload){
				// ACTIVO DIALOGO -> GUARDANDO INFO
				this.overlay = true ; 
				this.textDialog ="Actualizando Información"

				this.$http.put('putcompromiso/'+ this.edit.id, payload).then((response)=>{
					this.TerminarProceso(response.body);					
				}).catch(err =>{
					this.alerta = { activo: true, text: err.bodyText  , color:'error'}
					console.log('err', err)
				}).finally(()=>{ this.overlay = false });
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.alerta = { activo: true, text: mensaje  , color:'success'};
				// this.Correcto = true ; this.textCorrecto = mensaje;
				this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
        this.consultaAutorizados(this.getUsuarios.id); 
				const autorizadas = new Object({
					fecha     : this.traerFechaActual(),
					id_usuario: this.getUsuarios.id,
				});
        this.consultaAutorizados(autorizadas); // traer los pendientes por validar

				const payload = new Object({ 
					id_vendedor: this.getUsuarios.id, 
					fecha      : this.traerFechaActual()  
				});
				this.consultaCompromisos(payload) //ACTUALIZAR CONSULTA DE CLIENTES
				setTimeout(() => { me.$emit('modal',false)}, 1500);
				this.limpiarCampos();  //LIMPIAR FORMULARIO
			},

			limpiarCampos(){
				this.id_cliente 		= ''; 
				this.cliente        = ''; 
				this.fecha =this.traerFechaActual();
				this.hora = this.traerHoraActual();
				this.comentario     = ''; 
			}
		}
	}
</script>