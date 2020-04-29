<template>
	<v-content class="pa-0">
		<v-row >
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-card-actions class="mx-3"> <!-- TITILO DE LA VISTA -->
				<v-btn color="gris" dark x-small icon onClick="history.go(-1); return false;">
					<v-icon large >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium font-weight-black">Detalle del Compromiso</v-card-title>	
			</v-card-actions>
		
		  <v-card-text> <!-- DETALLE DEL PROSPECTO -->
				<v-row justify="center" >
					<v-col cols="12">
					  <v-card outlined>
						  <v-simple-table dense >
						    <template v-slot:default>
						      <tbody>
										<tr>
                      <td style="font-weight:bold">Cliente</td>
											<td > {{ detalle.nomcli }}</td>
                    </tr>
                    <tr>
                      <td style="font-weight:bold">Catégoria</td>
						          <td >{{ detalle.nomcatego }}</td>
                    </tr>
										<tr>
                      <td style="font-weight:bold">Hora</td>
											<td >{{ detalle.hora }}</td>
                    </tr>
										<tr>
                      <td style="font-weight:bold">Comentarios</td>
											<td > {{ detalle.comentarios }}</td>
                    </tr>
						      </tbody>
						    </template>
						  </v-simple-table>

					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
			
			<v-col cols="12">  <!-- TELEFONO * CONFIRMACION -->
				<v-card-actions>
					<v-btn  color="info" outlined dark><v-icon left>phone</v-icon>
						<a href="tel:detalle.tel1" style="text-decoration:none;">{{ detalle.tel1 }}</a>
					</v-btn>
					<v-spacer></v-spacer>
						<v-btn color="red darken-4" :disabled="dialog" persistent :loading="dialog" 
																				dark  @click="confirmarModal = true" v-if="citaConfirmada === 0" >
							CONFIRMAR CITA
						</v-btn>
					<v-btn color="green" dark v-else  >
						<v-icon left >check_circle_outline</v-icon>
						CITA CONFIRMADA
					</v-btn>
				</v-card-actions>
			</v-col>

			<v-col cols="12" v-if="!activaReagendar"> <!-- REAGENDAR -->
				<v-btn color="morado" rounded  block dark @click="activaReagendar= true">
					Reagendar
				</v-btn>
			</v-col>

			<v-col cols="6" v-if="activaReagendar"> <!-- FECHA DE COMPROMISO -->
				<v-dialog ref="fecha_compromiso" v-model="fechamodal" :return-value.sync="fecha" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha" label="Fecha" append-icon="event" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>
					<v-date-picker v-model="fecha" locale="es-es" color="rosa"  scrollable>
						<v-spacer></v-spacer>
						<v-btn text small color="gris" @click="fechamodal = false">Cancelar</v-btn>
						<v-btn dark small color="rosa" @click="$refs.fecha_compromiso.save(fecha)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="6" v-if="activaReagendar"> <!-- HORA DEL COMPROMISO -->
				<v-dialog ref="hora_compromiso" v-model="horamodal" :return-value.sync="hora" persistent width="290px" >

					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="hora" label="Hora" append-icon="access_time" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>

					<v-time-picker v-if="horamodal" locale="es-es" color="rosa" v-model="hora" full-width 	>
						<v-spacer></v-spacer>
						<v-btn small text color="gris" @click="horamodal = false">Cancel</v-btn>
						<v-btn small dark color="rosa" @click="$refs.hora_compromiso.save(hora)">OK</v-btn>
					</v-time-picker>
				</v-dialog>
			</v-col>

			<v-col cols="12" v-if="activaReagendar"> <!-- CANCELAR REAGENDADO * AGENDADO  -->
				<v-card-actions>
					<v-btn small dark color="gris" @click="activaReagendar=false"> Cancelar </v-btn>
					<v-spacer></v-spacer>
					<v-btn small dark color="rosa" :disabled="dialog" persistent :loading="dialog" @click="reagendar" > Reagendar </v-btn>
				</v-card-actions>
			</v-col>
			
			<v-col cols="12" v-if="!activaReagendar">
				<v-btn color="celeste" rounded block dark>Iniciar Recorrido</v-btn>
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

		<v-dialog v-model="confirmarModal" persistent max-width="400" > <!-- PROCESO PARA ELIMINAR  -->
			<v-card>
				<v-col cols="12"  class="pa-4">
					<strong >LA CITA SE CONFIRMARA, ¿ESTA SEGURO DE SEGUIR ?</strong>
				</v-col>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gris" text small @click="confirmarModal = false">Cancelar</v-btn>
					<v-btn color="rosa" dark small @click="confirmarCita">Seguir </v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> <!-- FIN DEL PROCESO PARA ELIMINAR  -->

	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	export default {
		data(){
			return{
				citaConfirmada: null,
				// MANEJO DE FECHAS
				fecha: '',

				// FECHA
				fecha						: new Date().toISOString().substr(0, 10),
				menu 						: false,
				fechamodal 			: false,
				fecha_compromiso: false,

				// HORA
				hora 					 : null,
        menu2 				 : false,
        horamodal			 : false,
				hora_compromiso: false,

				activaReagendar: false,
				confirmarModal: false,

				// ALERTAS
				snackbar: false,
				text		: '',
				color		: 'success',
				dialog : false,
				textDialog : "Guardando Información",
				Correcto   : false,
				textCorrecto: '',

			}
		},

		created(){

			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha = y + "-" + m +"-" + d;  // fecha actual
			this.hora  = n.getHours()+":"+n.getMinutes() // Hora actual
			
			this.detalle 				= this.$route.params.detalle  /// PARAMETROS QUE RECIBO DE LA VISTA 
			this.citaConfirmada = this.detalle.confirma_cita  // CONFIRMACION DE CITA

		},

		watch:{
		
		},

		computed:{
			// ...mapGetters('Compromisos'  ,['Loading','getCompromisos']), // IMPORTANDO USO DE VUEX 
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']), 							// IMPORTANDO USO DE VUEX

			confirmarCita(){
				this.confirmarModal = false; 
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , confirma_cita: 1 } 			 // FORMAR ARRAY A MANDAR 

				this.$http.post('confirmarcita', payload).then((response)=>{
					console.log('confirmar cita', response.body)
					this.dialog = false; this.citaConfirmada = 1;
					 
				}).catch((error)=>{
					console.log('error', error)
				})
			},

			reagendar(){
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , fecha: this.fecha, hora: this.hora } // FORMAR ARRAY A MANDAR

				this.$http.post('reagendar', payload).then((response)=>{
					var me = this;
					this.dialog = false; this.Correcto = true ; this.textCorrecto = response.body;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
					this.consultaCompromisos() //ACTUALIZAR CONSULTA DE CLIENTES
				}).catch((error)=>{
					console.log('error', error)
				})
			}
			
		}


	}
</script>