<template>
	<v-content class="pa-0">
		<v-row no-gutters>
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-col cols="12">
				<v-card-actions>
					<v-card-title> ENTREGAS DEL DÍA </v-card-title>
					<v-spacer></v-spacer>
				<v-btn  class="gris" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn>
				</v-card-actions>
			</v-col>

			<v-col cols="12" class="text-center" v-if="Loading" >  <!-- PROGRES -->
				<v-progress-circular :size="100" :width="7" color="celeste" indeterminate ></v-progress-circular>
			</v-col>	

			<v-col cols="12">
				<v-card v-for="(item, i) in getEntregas" :key ="i" class="mt-2" color="celeste">
					<v-simple-table dense flat>
						<template v-slot:default>
							<tbody>
								<tr> <td class="font-weight-bold">Cliente   </td> <td> {{ item.nomcli }}   </td></tr>
								<tr> <td class="font-weight-bold">Factura   </td> <td> {{ item.numfac }}</td></tr>
								<tr> <td class="font-weight-bold">Dirección </td> <td> {{ item.direccion }}</td></tr>
								<tr> <td class="font-weight-bold">Entregar</td> <td> {{ item.fecha_entrega }}</td></tr>
								<tr> <td class="font-weight-bold">Hora </td> <td> {{ item.hora_entrega }}</td></tr>

								<tr> <td class="font-weight-bold"></td>
								<td align="right" class="pa-1">
									<v-btn color="green" class="ma-1" fab dark small v-if="item.nummovim" @click="detalle(item)"><v-icon>visibility</v-icon></v-btn> 
									<v-btn color="celeste" class="ma-1" fab dark small @click="VerDetalle(item)" v-else><v-icon>offline_pin</v-icon></v-btn> 
								</td></tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			<v-col cols="12"  v-if="!getEntregas.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No hay entregas pendientes.</v-col>
						<v-col class="shrink">
							<v-btn fab small color="celeste" dark > <v-icon> airport_shuttle </v-icon> </v-btn>
						</v-col>
					</v-row>
				</v-alert>
			</v-col>

			<v-dialog v-model="VerDetalleModal" width="500">
				<v-card class="pa-3">
					<v-card-actions class="headline ">
						Movimiento en almacén
						<v-spacer></v-spacer>
						<v-btn color="rosa" x-small fab dark @click="VerDetalleModal=false"><v-icon  >clear</v-icon></v-btn>
					</v-card-actions>
					<v-divider class="mt-1"></v-divider>
					<v-card-actions>
						<v-text-field 
							v-model.number="nummovim" 
							hide-details 
							label="Número de Movimiento" 
							outlined dense 
							type="number"
							clearable
							color="rosa"
						></v-text-field>
					</v-card-actions>
					<v-card-actions>
						 <v-btn color="green" dark block @click="confirmaModal=true" :disabled="NUMMOVIM">Guardar</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>

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
				<v-card>
					<v-card-title >El número de movimiento <br> se guardara </v-card-title>
					<v-card-subtitle class="font-weight-bold mt-1">  ¿Esta seguro de continuar? </v-card-subtitle>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn color="gria" text small @click="confirmaModal = false">Cancelar</v-btn>
						<v-btn color="green" dark small @click="addNumMovim()">Continuar</v-btn>

					</v-card-actions>
				</v-card>
			</v-dialog> <!-- CONFIRMACION DE PROCESO  -->
		
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
				nummovim:'',
				// Principales
				Compromisos: [],
				id_entrega: null,
				id_compromiso:null,
				numorden: '',
				obscierre:'',

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
			if(!this.$route.params.detalle){   }
			this.consultar();
		},
		
		computed:{
			...mapGetters('Entregas'  ,['Loading','getEntregas']), // IMPORTANDO USO DE VUEX 
      ...mapGetters('Usuarios',['getUsuarios']),

			NUMMOVIM:function(){
				return this.nummovim? false:true;
			}
		},

		methods:{
			...mapActions('Entregas'  ,['consultaEntregas']), // IMPORTANDO USO DE VUEX
			

			addNumMovim(){
				const payload = { id: this.id_entrega, nummovim: this.nummovim }
				this.confirmaModal = false;
				this.dialog = true ; this.textDialog ="Guardando Información"
				setTimeout(() => (this.dialog = false), 2000)

				this.$http.post('numero.movim', payload).then(response =>{
						this.TerminarProceso(response.body);			
				}).catch(error =>{
					console.log('error', error)
				})
			},

			consultar(){ // CONSULTAR COMPROMISOS
				var me = this;
				
				const payload = { id_chofer: this.getUsuarios.id } // FORMO ARRAY CON id DEL VENDEDOR LOGEADO Y FECHA ACTUAL
				this.consultaEntregas(payload);
			},

			VerDetalle(item){ // LLENO MODAL CON LA INFORMACION DEL PROYECTO SELECCIONADO
				this.id_entrega     = item.id
				this.id_compromiso  = item.id_compromiso
				this.VerDetalleModal = true
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
				setTimeout(() => (this.Correcto = false), 2000)
				setTimeout(() => (this.VerDetalleModal = false), 2000)
				this.consultar() //ACTUALIZAR CONSULTA DE CLIENTES
			},

			detalle(item){  // VER DETALLE DEL COMPROMISO
				this.$router.push({name: 'det.entrega', params:{ detalle:item }})
			},
		}


	}
</script>

<style scoped>

</style>