<template>
	<v-container>
		<v-row justify="center">
			<v-col cols="12" >
				
				<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}}
					<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
				</v-snackbar>

				<v-card-actions class="pa-0" >
					<h3> <strong> {{ param === 1? 'Nuevo Compromiso':'Editar Compromiso' }} </strong></h3> 
					<v-spacer></v-spacer>
					<v-btn color="error" small @click="$emit('modal',false)" text><v-icon>clear</v-icon></v-btn>
				</v-card-actions>

				<v-divider class="ma-2"></v-divider>

				<v-row>
					<v-col cols="12" sm="6" > <!-- CLIENTE -->
						<v-autocomplete
							:items="clientes" v-model="cliente" item-text="nombre" item-value="id" label="Clientes" 
							dense filled hide-details color="celeste" append-icon="people" return-object
						></v-autocomplete>
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

				</v-row>

				<!-- //DIALOG PARA GUARDAR LA INFORMACION -->
				<v-card-actions>
					<v-spacer></v-spacer>
					 <v-btn small :disabled="dialog" persistent :loading="dialog" dark center class="white--text" color="success" @click="validaInfo" v-if="param === 1">
             Confirmar  
          </v-btn>
					<v-btn small :disabled="dialog" persistent :loading="dialog" dark center class="white--text" color="success" @click="validaInfo" v-else>
             Actualizar  
          </v-btn>

          <v-dialog v-model="dialog" hide-overlay persistent width="300">
            <v-card color="blue darken-4" dark >
              <v-card-text> <th style="font-size:17px;" align="center">{{ textDialog }}</th>
                <br>
                <v-progress-linear indeterminate color="white" class="mb-0" persistent></v-progress-linear>
              </v-card-text>
            </v-card>
          </v-dialog>

					<v-dialog v-model="Correcto" hide-overlay persistent width="350">
            <v-card color="success"  dark class="pa-3">
							<h3><strong>{{ textCorrecto }} </strong></h3>
            </v-card>
          </v-dialog>

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
				</v-card-actions>

			</v-col>
		</v-row>
	</v-container>
	
</template>

<script>
	import  metodos from '@/mixins/metodos.js';
	import {mapGetters, mapActions} from 'vuex'
	
	export default {
		mixins:[metodos],
	  components: {
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
				// ALERTAS
				snackbar: false,
				text		: '',
				color		: 'error',
				dialog : false,
				textDialog : "Guardando Información",
				Correcto   : false,
				textCorrecto: '',
				confirmaProceso: false
			}
		},
		
		created(){
			this.fecha =this.traerFechaActual();
			this.hora = this.traerHoraActual();
			this.consultar_Clientes()
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
				if(!this.cliente.id)		  { this.snackbar = true; this.text="No puedes omitir el CLIENTE"   		; return }
				if(!this.fecha)						{ this.snackbar = true; this.text="No puedes omitir la FECHA"   			; return }
				if(!this.hora)						{ this.snackbar = true; this.text="No puedes omitir la HORA"   				; return }
				if(fc < fa){ this.snackbar=true; this.text="La FECHA y HORA no puede ser menor a la actual." ; return}
				this.confirmaProceso = true
				// this.PrepararPeticion()
			},

			PrepararPeticion(){
				// FORMAR ARRAY A MANDAR
				const payload = { id_vendedor 		: this.getUsuarios.id,
													tipo_compromiso	: 2,
													id_categoria		: 3,
													id_cliente 		  : this.cliente.id,
													fecha						: this.fecha,
													hora	  				: this.hora,
													fecha_fin				: this.fecha,
													hora_fin	  		: parseInt(this.hora.substr(0, 2)) + 1 + ":" + parseInt(this.hora.substr(3,2)),
													id_usuario      : this.getUsuarios.id, // USUARIO QUE CREA EL REGISTRO
													fase_venta      : 1,
													estatus     		: 1,
													fechaActual     : this.traerFechaActual(),
													horaActual      : this.traerHoraActual(),
												}
													
				console.log('payload', payload)

				// VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.param === 1 ? this.Crear(payload): this.Actualizar(payload);
			},

			Crear(payload){
				// ACTIVO DIALOGO -> GUARDANDO INFO
				this.dialog = true ; setTimeout(() => (this.dialog = false), 2000)
				this.$http.post('addcompromiso', payload).then((response)=>{
					this.TerminarProceso(response.body);					
				}).catch(err =>{ console.log('err', err)})
			},

			Actualizar(payload){
				// ACTIVO DIALOGO -> GUARDANDO INFO
				this.dialog = true ; this.textDialog ="Actualizando Información"
				setTimeout(() => (this.dialog = false), 2000)
				this.$http.put('putcompromiso/'+ this.edit.id, payload).then((response)=>{
					this.TerminarProceso(response.body);					
				}).catch(err =>{ console.log('err', err)})
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
				const payload = { id_vendedor: this.getUsuarios.id , fecha: this.traerFechaActual()}
				this.consultaCompromisos(payload) //ACTUALIZAR CONSULTA DE CLIENTES
				setTimeout(function(){ me.$emit('modal',false)}, 2000);
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