<template>
	<v-content class="pa-0">
		<v-row no-gutters>
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-col cols="12">
				<v-card-actions>
					<v-card-subtitle class="font-weight-bold "> PROYETOS COTIZADOS </v-card-subtitle>
					<v-spacer></v-spacer>
				<v-btn  class="gris" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn>
				</v-card-actions>
			</v-col>

			<v-container fluid v-if="Loading">
				<v-row align="center" justify="center" style="height: 300px;">
					<v-col cols="12" class="text-center"  >  <!-- PROGRES -->
						<v-progress-circular :size="100" :width="7" color="celeste" indeterminate ></v-progress-circular>
					</v-col>	
				</v-row>
			</v-container>	

			<v-col cols="12">
				<v-card v-for="(item, i) in getproyectos" :key ="i" class="mt-2" color="celeste">
					<v-simple-table  flat>
						<template v-slot:default>
							<tbody>
								<tr> <td class="font-weight-bold">Cliente  </td> <td> {{ item.nomcli }}   </td></tr>
								<tr> <td class="font-weight-bold">Observación</td> <td> {{ item.obs_usuario }}</td></tr>
								<tr> <td class="font-weight-bold"></td>
								<td align="right" class="pa-1">
									<v-btn color="celeste" fab dark small @click="VerDetalle(item)"><v-icon>format_line_spacing</v-icon></v-btn> 
								</td></tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			<v-dialog v-model="VerDetalleModal" width="500">
				<v-card class="pa-3">
					<v-card-actions class="headline ">
						PROYECTO
						<v-spacer></v-spacer>
						<v-btn color="rosa" x-small fab dark @click="VerDetalleModal=false"><v-icon  >clear</v-icon></v-btn>
					</v-card-actions>
					<v-divider></v-divider>
					<v-simple-table fixed-header height="auto" >
						<template v-slot:default >
							<thead >
								<tr><th class="text-left ">Cliente		 	  </th><th class="text-left">{{ nomcli }}</th></tr>
								<tr><th class="text-left">Observaciónes </th><th class="text-left ">{{ obs_usuario}}</th></tr>
							</thead>
						</template>
					</v-simple-table>

					<v-row class="pa-2">
						<v-col cols="12" > <v-btn color="morado" rounded dark block @click="validaInfo(4)">Recotizar</v-btn> </v-col>
						<v-col cols="12">  <v-btn color="gris"   rounded dark block @click="validaInfo(8)" >Cerrar Proyecto</v-btn> </v-col>
						<v-col cols="12"> <v-btn color="green "  rounded dark block @click="validaInfo(5)">Mandar a Producción</v-btn> </v-col>
					</v-row>
				</v-card>
			</v-dialog>

			<v-col cols="12"  v-if="!getproyectos.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No hay compromisos pendientes. <br> Actualice la vista o revise sus proximos compromisos.</v-col>
						<v-col class="shrink">
							<v-btn fab small color="celeste" dark :to="{name:'pendientes'}"> <v-icon> calendar_today </v-icon> </v-btn>
						</v-col>
					</v-row>
				</v-alert>
			</v-col>

			<!-- PROCESO GUARDAR| -->
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
			<!-- FIN PROCESO GUARDAR -->

			<v-dialog v-model="confirmaModal" persistent max-width="400" > <!-- CONFIRMACION DE PROCESO  -->
				<v-card :color="colorModal" dark>
					<v-card-title class="">
					 {{ textModal }}
					</v-card-title>
					<v-card-text v-if="fase===8 || fase===4" class="my-1">
						<v-textarea
							v-model="obscierre" label="¿Por qué?" hide-details outlined
							:placeholder="placeholder"
						></v-textarea>
					</v-card-text>
					<v-card-subtitle>
					¿Esta seguro de continuar?
					</v-card-subtitle>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn color="white" text small @click="confirmaModal = false">Cancelar</v-btn>
						<v-btn color="green" dark small @click="validaObs()" v-if="fase===8">Continuar cierre</v-btn>
						<v-btn color="green" dark small @click="FaseDeVenta()" v-else>Continuar</v-btn>

					</v-card-actions>
				</v-card>
			</v-dialog> <!-- CONFIRMACION DE PROCESO  -->

			<v-dialog v-model="numordenModal" persistent max-width="400" > <!-- NUMERO DE ORDEN  -->
				<v-card :color="colorModal" >
					<v-card-title>
						 Por favor capture el número <br> de orden
					</v-card-title>
					<v-card-text>
					<v-text-field v-model="numorden" label="Número de orden" clearable></v-text-field>
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn color=gris text small @click="numordenModal = false">Cancelar</v-btn>
						<v-btn color="green" dark small @click="FaseDeVenta()">Guardar</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog> <!-- FIN NUMERO DE ORDEN  -->
		
		</v-row>
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	import  metodos from '@/mixins/metodos.js';
	export default {
		components: { }, 
		mixins:[metodos],
		data(){
			return{
				// Principales
				Compromisos: [],
				fase:0, 
				nomcli: '',
				obs_usuario:'',
				id_compromiso:null,
				numorden: '',
				obscierre:'',
				placeholder:'',
				// MODALES
				VerDetalleModal: false,
				confirmaModal: false,
				numordenModal:false,
				textModal:'',
				colorModal:'white',

				dialog: false,
				textDialog : "Guardando Información",
				Correcto   : false,
				textCorrecto: '',

				snackbar: false,
				text		: '',
				color		: 'error',
			}
		},

		created(){
			// LLENAR COMPROMISOS
			this.consultar();
		},
		
		computed:{
			...mapGetters('Compromisos'  ,['Loading','getproyectos']), // IMPORTANDO USO DE VUEX 
      ...mapGetters('Usuarios',['getUsuarios']),
		},

		methods:{
			...mapActions('Compromisos'  ,['proyectosCotizados']), // IMPORTANDO USO DE VUEX

			consultar(){ // CONSULTAR COMPROMISOS
				const payload = { id_vendedor: this.getUsuarios.id , fase_venta:3 } // FORMO ARRAY CON id DEL VENDEDOR LOGEADO Y FECHA ACTUAL
				this.proyectosCotizados(payload)
			},

			VerDetalle(item){ // LLENO MODAL CON LA INFORMACION DEL PROYECTO SELECCIONADO
				this.id_compromiso   = item.id
				this.nomcli    		   = item.nomcli;
				this.obs_usuario     = item.obs_usuario;
				this.VerDetalleModal = true
			},

			validaInfo(fase){ // REVISO QUE FASE ESTA EJECUTANDO
				this.limparCampos()
				switch (fase) {
					case 4:
						this.textModal = "El proyecto pasara a recotizarse";
						this.fase      = 4; this.confirmaModal = true; this.colorModal = 'morado'
						this.placeholder = "Escriba por que se recotizara..."
						break;
					case 8:
						this.textModal = "El proyecto pasara a cerrarse";
						this.fase      = 8; this.confirmaModal = true; this.colorModal = 'gris'
						this.placeholder = "Escriba por que se cierra el proyecto..."
						break;
					case 5:
						this.textModal = "El proyecto pasara a produccion";
						this.fase      = 5; this.numordenModal = true; this.colorModal = 'white'
						break;
				}
			},

			validaOrden(){ // VALIDO EL NUMERO DE ORDEN
			 if(!this.numorden){ this.snackbar=true; this.text="Debes de capturar el número de orden"; return }; // SI NUMORDEN ESTA VACIO RETORNO
			 this.FaseDeVenta() // SI TODO ESTA CORRECTO MANDO A GUARDAR LA INFORMACION 
			},
			validaObs(){
				if(!this.obscierre){ this.snackbar=true; this.text="Debes de capturar las observaciones."; return }; // SI NUMORDEN ESTA VACIO RETORNO
			 	this.FaseDeVenta() // SI TODO ESTA CORRECTO MANDO A GUARDAR LA INFORMACION
			},

			FaseDeVenta(){ // ACTUALIZO LA FASE DE VENTA
				// FORMO Object QUE MANDARE
				this.numordenModal= false; 
				const payload = { id :this.id_compromiso,
													fecha: this.traerFechaActual(),
													hora: this.traerHoraActual(),
													fase_venta :this.fase,
													numorden: this.numorden? this.numorden: '',
													aceptado: 0,
													obscierre: this.obscierre? this.obscierre: ''
												}

				console.log('payload', payload);

				this.confirmaModal = false;
				this.dialog = true ; setTimeout(() => (this.dialog = false), 2000)
			
				this.$http.post('fase.venta', payload).then(response =>{
					this.TerminarProceso(response.body);					
				}).catch(error =>{
					console.log('error', error)
				})
				

			},

			TerminarProceso(mensaje){
				var me = this ;
				this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
				setTimeout(() => (this.Correcto = false), 2000)
				setTimeout(() => (this.VerDetalleModal = false), 2000)
				this.consultar() //ACTUALIZAR CONSULTA DE CLIENTES
			},

			limparCampos(){
				this.obscierre = '';
				this.numorden = ''
			}
		}


	}
</script>

<style scoped>

</style>