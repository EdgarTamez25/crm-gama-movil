<template>
	<v-content class="pa-0">
		<v-row >
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-card-actions class="mx-3 "> <!-- TITILO DE LA VISTA -->
				<v-btn color="gris" dark x-small icon hide-details onClick="history.go(-1); return false;">
					<v-icon large >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium font-weight-black">Detalle de la Entrega</v-card-title>	
			</v-card-actions>
		
		  <v-card-text > <!-- DETALLE DEL PROSPECTO -->
				<v-row justify="center" >
					<v-col cols="12">
					  <v-card outlined>
						  <v-simple-table  flat>
								<template v-slot:default>
									<tbody>
										<tr> <td class="font-weight-bold">Cliente   </td> <td> {{ detalle.nomcli }}   </td></tr>
										<tr> <td class="font-weight-bold">Factura   </td> <td> {{ detalle.numfac }}</td></tr>
										<tr> <td class="font-weight-bold">Dirección </td> <td> {{ detalle.direccion }}</td></tr>
										<tr> <td class="font-weight-bold">Entregar</td> <td> {{ detalle.fecha_entrega }}</td></tr>
										<tr> <td class="font-weight-bold">Hora </td> <td> {{ detalle.hora_entrega }}</td></tr>
										<!-- <tr> <td class="font-weight-bold"></td> -->
									</tbody>
								</template>
							</v-simple-table>
					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
		</v-row>

		<v-row>
			<v-col cols="12" align="center" class="mt-5"> <!-- BOTON PARA TERMINAR COMPROMISO -->
				<v-btn color="green darken-4" rounded block dark large @click="EntregarProducto(true)">
					SE ACEPTO
				</v-btn>
			</v-col>
			<v-col cols="12" align="center" > <!-- BOTON PARA TERMINAR COMPROMISO -->
				<v-btn color="red darken-4" rounded block dark large @click="EntregarProducto(false)">
					SE RECHAZO
				</v-btn>
			</v-col>
		</v-row>

				<!-- //DIALOG PARA GUARDAR LA INFORMACION -->
		<v-card-actions>
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
		</v-card-actions>

		<v-dialog v-model="confirmaModal" persistent max-width="400" > <!-- CONFIRMACION DE PROCESO  -->
			<v-card dark :color="aceptacion?'green darken-4':'red darken-4'">
				<v-card-title >
					{{ textModal }}
				</v-card-title>
				<v-form ref="form" v-model="valid" >
					<v-card-text v-if="!aceptacion" >
						<v-textarea
							v-model="obs" label="Observaciónes" hide-details outlined autofocus
							placeholder="Escriba por que se rechazo..." :rules="obsRules"
						></v-textarea>
					</v-card-text>
				</v-form>
				<v-card-subtitle class="my-1">
				¿Esta seguro de continuar?
				</v-card-subtitle>
				<v-card-actions >
					<v-spacer></v-spacer>
					<v-btn color="white" text small @click="confirmaModal = false">Cancelar</v-btn>
					<v-btn color="gris"  dark small v-if="!aceptacion" :disabled="!valid" @click="FaseDeVenta()">Continuar</v-btn>
					<v-btn color="gris " dark small @click="FaseDeVenta()" v-else>Continuar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> <!-- CONFIRMACION DE PROCESO  -->

	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	import  metodos from '@/mixins/metodos.js';
	var moment = require('moment'); 
	export default {
		mixins:[metodos],
		data(){
			return{
				// ALERTAS
				valid:true,
				obs:'',
				obsRules:[v => !!v || 'Requerido.'],
				fase: null,
				snackbar: false,
				text		: '',
				color		: 'success',
				aceptacion: null,
				dialog : false,
				textDialog : "Guardando Información",

				Correcto   : false,
				textCorrecto: '',

				textModal:'',
				confirmaModal:false,

			}
		},

		created(){
			if(!this.$route.params.detalle){  window.history.go(-1); }
			this.detalle = this.$route.params.detalle  /// PARAMETROS QUE RECIBO DE LA VISTA 
		},

		computed:{ 
		},

		methods:{
			EntregarProducto(aceptacion){
				this.aceptacion = aceptacion
				aceptacion? this.textModal="Se acepto el producto":this.textModal="Se rechazo el producto"
				aceptacion? this.fase = 8 :this.fase = 7

				this.confirmaModal = true;
			},

			FaseDeVenta(){ // ACTUALIZO LA FASE DE VENTA
				// FORMO Object QUE MANDARE
				this.numordenModal= false; 
				const payload = { id :this.detalle.id_compromiso,
													fecha: this.traerFechaActual(),
													hora: this.traerHoraActual(),
													fase_venta : this.aceptacion? 8:7,
													aceptado: this.aceptacion? 1:0,
													obscierre: this.obs? this.obs: '',
													id_entrega: this.detalle.id,
													numorden:'',
													estatus: this.aceptacion? 1:2
													}

				this.confirmaModal = false;
				this.dialog = true ; this.textDialog ="Guardando Información"
				setTimeout(() => (this.dialog = false), 2000)
			
				this.$http.post('fase.venta', payload).then(response =>{
					this.$http.post('actualiza.entrega',payload).then(response =>{
						this.TerminarProceso(response.body);					
					})
				}).catch(error =>{
					console.log('error', error)
				})
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.dialog = false;this.Correcto = true ; this.textCorrecto = mensaje;
				setTimeout(function(){ me.$router.push({name: 'entregas' })}, 1000);
			},
		}
	}
</script>