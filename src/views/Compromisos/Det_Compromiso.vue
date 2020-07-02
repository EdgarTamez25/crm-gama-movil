<template>
	<v-content class="pa-0">
		<v-row >
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-card-actions class="mx-3 "> <!-- TITILO DE LA VISTA -->
				<v-btn color="gris" dark x-small icon onClick="history.go(-1); return false;">
					<v-icon large >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium font-weight-black">Detalle del Compromiso</v-card-title>	
			</v-card-actions>
		
		  <v-card-text  > <!-- DETALLE DEL PROSPECTO -->
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
											<td > {{ detalle.hora }} </td>
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
			<!-- {{ detalle}} -->
			<v-col cols="6" align="center"> <!-- TELEFONO 1 -->
				<v-btn  color="info" outlined block :disabled="detalle.tel1 === '' "><v-icon left>phone</v-icon>
						<a :href="`tel:${detalle.tel1}`" style="text-decoration:none;">{{ detalle.tel1? detalle.tel1: 'Sin Contacto' }}</a>
						<!-- <a :href="`tel:${detalle.tel1}`">{{phone}}</a>  -->
					</v-btn>
			</v-col>

			<v-col cols="6" align="center" > <!-- TELEFONO 2 -->
				<v-btn  color="info" outlined block :disabled="detalle.tel2 === '' "><v-icon left>phone</v-icon>
					<a :href="`tel:${detalle.tel2}`" style="text-decoration:none;">{{ detalle.tel2? detalle.tel2: 'Sin Contacto' }}</a>
				</v-btn>
			</v-col>

			<v-col cols="12" align="center"> <!-- CONFIRMAR CITA  -->
				<v-btn color="red darken-4" :disabled="dialog" persistent :loading="dialog" block 
																		 dark  @click="confirmarModal = true" v-if="citaConfirmada === 0" >
					CONFIRMAR CITA
				</v-btn>
				<v-btn color="green" block dark  v-else > CITA CONFIRMADA </v-btn>
			</v-col>

			<v-col cols="12" v-if="!activaReagendar" align="center"> <!-- REAGENDAR -->
				<v-btn color="morado" block dense dark @click="activaReagendar= true"> Reagendar </v-btn>
			</v-col>

			<v-col cols="6" v-if="activaReagendar"> <!-- FECHA DE COMPROMISO -->
				<v-dialog ref="fecha_compromiso" v-model="fechamodal" :return-value.sync="fecha" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha" label="Fecha Inicio" append-icon="event" readonly v-on="on"
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
							 v-model="hora" label="Hora Inicio" append-icon="access_time" readonly v-on="on"
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

			<v-col cols="6" v-if="activaReagendar"> <!-- FECHA DE COMPROMISO FIN -->
				<v-dialog ref="fecha_compromiso_fin" v-model="fechamodalfin" :return-value.sync="fecha_fin" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha_fin" label="Fecha fin" append-icon="event" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>
					<v-date-picker v-model="fecha_fin" locale="es-es" color="rosa"  scrollable>
						<v-spacer></v-spacer>
						<v-btn text small color="gris" @click="fechamodalfin = false">Cancelar</v-btn>
						<v-btn dark small color="rosa" @click="$refs.fecha_compromiso_fin.save(fecha_fin)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="6" v-if="activaReagendar"> <!-- HORA DEL COMPROMISO FIN -->
				<v-dialog ref="hora_compromiso_fin" v-model="horamodalfin" :return-value.sync="hora_fin" persistent width="290px" >

					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="hora_fin" label="Hora fin" append-icon="access_time" readonly v-on="on"
							outlined dense hide-details color="celeste"
						></v-text-field>
					</template>

					<v-time-picker v-if="horamodalfin" locale="es-es" color="rosa" v-model="hora_fin" full-width 	>
						<v-spacer></v-spacer>
						<v-btn small text color="gris" @click="horamodalfin = false">Cancel</v-btn>
						<v-btn small dark color="rosa" @click="$refs.hora_compromiso_fin.save(hora_fin)">OK</v-btn>
					</v-time-picker>
				</v-dialog>
			</v-col>
			
			<v-col cols="12" v-if="activaReagendar"> <!-- CANCELAR REAGENDADO * AGENDADO  -->
				<v-card-actions>
					<v-btn small dark color="gris" @click="activaReagendar=false"> Cancelar </v-btn>
					<v-spacer></v-spacer>
					<v-btn small dark color="rosa" :disabled="dialog" persistent :loading="dialog" @click="validaFechas" > Reagendar </v-btn>
				</v-card-actions>
			</v-col>
		</v-row>

		<v-col cols="12" align="center" class="mt-5"> <!-- BOTON PARA TERMINAR COMPROMISO -->
			<v-btn color="rosa" fab dark x-large v-if="citaConfirmada===1 && !Terminar" @click="Terminar = true"  href="#fase" >
				<v-icon>thumb_up_alt</v-icon>
			</v-btn>
		</v-col>

		<v-row v-if="Terminar"> <!-- FASE DE VENTA -->
				<v-col cols="12" align="center">
					<h3>	FASE DEL COMPROMISO </h3>
				</v-col>

				<v-col cols="12"> <!-- FASE DE VENTA -->
					<v-select 
						v-model="fase_venta" :items="['Cotizar','Cerrar Proyecto']"  outlined  
						label="Fase de Venta" return-object hide-details dense
					></v-select>
				</v-col>

				<v-col cols="12" md="6"> <!-- OBSERVACIONES -->
					<v-textarea
						v-model="obs_usuario" label="Observaciónes" hide-details outlined
						placeholder="Escriba las observaciones del compromiso..."
					></v-textarea>
				</v-col>

				<v-col cols="12" align="right" class="mt-0" > <!-- BOTON PARA GUARDAR INFORMACION -->
					<v-card-actions>
						<v-btn color="gris" dark  small @click="Terminar = false">Cancelar</v-btn> <v-spacer></v-spacer> 
						<v-btn color="celeste" dark  small @click="validaInfo">Guardar Información</v-btn> 
					</v-card-actions>
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

		<v-dialog v-model="confirmarModal" persistent max-width="400" > <!-- PROCESO PARA CONFIRMACION  -->
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
		</v-dialog> <!-- FIN DEL PROCESO PARA CONFIRMAR  -->

		<v-dialog v-model="terminarCompromiso" persistent max-width="400" > <!-- PROCESO PARA TERMINAR  -->
			<v-card>
				<v-col cols="12" class="pa-4">
					<strong >EL COMPROMISO SE FINALIZARA COMO TERMINADO</strong> <br> ¿Esta seguro de continuar?
				</v-col>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gris" text small @click="terminarCompromiso = false">Cancelar</v-btn>
					<v-btn color="rosa" dark small @click="GuardarInfo">Continuar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> <!-- FIN DEL PROCESO PARA TERMINAR  -->

		<div id="fase"></div>
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	var moment = require('moment'); 
	export default {
		data(){
			return{
				fase_venta: '',
				obs_usuario: '',
				Terminar: false,
				citaConfirmada: null,
				terminarCompromiso: false,

				// MANEJO DE FECHAS
				fecha						: new Date().toISOString().substr(0, 10),
				fechamodal 			: false,
				fecha_compromiso: false,

				fecha_fin			 		  : '',
				fechamodalfin   		: false,
				fecha_compromiso_fin: false,

				// HORA
				hora 					  	 : null,
        horamodal			  	 : false,
				hora_compromiso 	 : false,
				hora_fin 					 : null,
        horamodalfin			 : false,
				hora_compromiso_fin: false,

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
			moment.locale('es') /// inciar Moment 
			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha = y + "-" + m +"-" + d;  // fecha actual
			this.hora  = n.getHours()+":"+n.getMinutes() // Hora actual
			
			this.detalle 				= this.$route.params.detalle  /// PARAMETROS QUE RECIBO DE LA VISTA 
			this.citaConfirmada = this.detalle.confirma_cita  // CONFIRMACION DE CITA
		},

		watch:{ },
		computed:{ 
			// ...mapGetters('Compromisos'  ,['Loading','getCompromisos']),  
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']),// IMPORTANDO USO DE VUEX

			confirmarCita(){
				this.confirmarModal = false; 
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , confirma_cita: 1 } 			 // FORMAR ARRAY A MANDAR 

				this.$http.post('confirmarcita', payload).then((response)=>{
					this.dialog = false; this.citaConfirmada = 1;
				}).catch((error)=>{
					console.log('error', error)
				})
			},

			validaFechas(){
				var fi = this.fecha + " " + this.hora; 
				var ff = this.fecha_fin + " " + this.hora_fin;
				if(!this.fecha)			{ this.snackbar=true;this.color="error";this.text="No puedes omitir la FECHA INICIAL"	; return}
				if(!this.hora)			{ this.snackbar=true;this.color="error";this.text="No puedes omitir la HORA INICIAL"	; return}
				if(!this.fecha_fin)	{ this.snackbar=true;this.color="error";this.text="No puedes omitir la FECHA FINAL"		; return}
				if(!this.hora_fin)	{ this.snackbar=true;this.color="error";this.text="No puedes omitir la HORA FINAL"		; return}
				if(fi > ff){ this.snackbar=true; this.color="error"; this.text="La FECHA y HORA FINAL no puede ser menor a la INICIAL"; return};
				this.reagendar()
			},

			reagendar(){
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , 
													fecha: this.fecha, 
													hora: this.hora, 
													fecha_fin: this.fecha_fin,
													hora_fin: this.hora_fin
													} // FORMAR ARRAY A MANDAR

				this.$http.post('reagendar', payload).then((response)=>{
					var me = this;
					this.dialog = false; this.Correcto = true ; this.textCorrecto = response.body;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
					this.consultaCompromisos() //ACTUALIZAR CONSULTA DE CLIENTES
				}).catch((error)=>{
					console.log('error', error)
				})
			},
	
			validaInfo(){
				if(!this.fase_venta){ this.snackbar=true; this.color="error"; this.text="Debes seleccionar una FASE DE VENTA"; return}
				if(!this.obs_usuario){ this.snackbar=true; this.color="error"; this.text="Debes agregar alguna OBSERVACIÓN DEL COMPROMISO."; return}
				this.terminarCompromiso = true;
			},

			GuardarInfo(){
				var f = new Date(); var hora = f.getHours(); var minutos = f.getMinutes();
				if(hora<10){ hora = '0'+ hora }; if(minutos<10){ minutos = '0'+ minutos}
				var hora_actual = hora +':'+minutos

				const payload = { fase_venta: this.fase_venta === 'Cotizar'? 2:8,
													obs_usuario: this.obs_usuario,
													cumplimiento: 1,
													fecha_cierre: new Date().toISOString().substr(0, 10), 
													hora_cierre: hora_actual,
													id: this.detalle.id,
													obscierre: this.obs_usuario
												}
												
				this.terminarCompromiso = false; 
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR

				this.$http.post('terminar.compromiso', payload).then(response =>{
					
					var me = this;
					this.dialog = false;this.Correcto = true ; this.textCorrecto = response.body;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
					this.consultaCompromisos() 

				}).catch(error =>{
					console.log('error', error)
				})
			}
			
		}
	}
</script>