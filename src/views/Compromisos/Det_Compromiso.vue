<template>
	<v-content class="pa-0">
		<v-row >
			<v-snackbar v-model="snackbar" :timeout="1000" top :color="color"> {{text}} 
				<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
			</v-snackbar>

			<v-card-actions class="mx-3 pa-0"> <!-- TITILO DE LA VISTA -->
				<v-btn color="gris" dark x-small icon onClick="history.go(-1); return false;">
					<v-icon large >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium font-weight-black">Detalle del Compromiso</v-card-title>	
			</v-card-actions>
		
		  <v-card-text  > <!-- DETALLE DEL PROSPECTO -->
				<v-row justify="center" >
					<v-col cols="12" class="pa-2">
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
											<td > {{ detalle.obs }}</td>
                    </tr>
						      </tbody>
						    </template>
						  </v-simple-table>

					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
		
			<v-col cols="6" align="center"> <!-- TELEFONO 1 -->
				<v-btn  color="info" outlined block :disabled="detalle.tel1 === '' "><v-icon left>phone</v-icon>
						<a :href="`tel:${detalle.tel1}`" style="text-decoration:none;">{{ detalle.tel1? detalle.tel1: '00-00-00' }}</a>
						<!-- <a :href="`tel:${detalle.tel1}`">{{phone}}</a>  -->
					</v-btn>
			</v-col>

			<v-col cols="6" align="center" > <!-- TELEFONO 2 -->
				<v-btn  color="info" outlined block :disabled="detalle.tel2 === '' "><v-icon left>phone</v-icon>
					<a :href="`tel:${detalle.tel2}`" style="text-decoration:none;">{{ detalle.tel2? detalle.tel2: '00-00-00' }}</a>
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
			<v-col cols="12" v-if="activaReagendar"> <!-- COMENARIO -->
				<v-textarea
					v-model ="obs" outlined label="Comentario" placeholder="Agregar un comentario ..."
					rows="2" hide-details dense color="celeste"
				></v-textarea>
			</v-col>

			<v-col cols="12" v-if="activaReagendar"> <!-- CANCELAR REAGENDADO * AGENDADO  -->
				<v-card-actions>
					<v-btn small dark color="gris" @click="activaReagendar=false"> Cancelar </v-btn>
					<v-spacer></v-spacer>
					<v-btn small dark color="rosa" :disabled="dialog" persistent :loading="dialog" @click="validaFechas" > Reagendar </v-btn>
				</v-card-actions>
			</v-col>
		</v-row>
		<!-- OPCIONES DE ACCION **FINALIZAR**SOLICITAR-PEDIDO*** -->
		<v-row justify="center" v-if="citaConfirmada===1 && !Terminar" >
			<v-col cols="6" class="text-center" >
				<v-btn color="rosa" large dark outlined style="height:100px; width:150px" @click="Terminar=true" href="#fase"> 
					Finalizar <br> compromiso
				</v-btn>
			</v-col>
			<v-col cols="6" class="text-center" >
				<v-btn color="celeste" large dark outlined style="height:100px; width:150px" @click="solicitarModal=true"> 
					Solicitar <br> pedido 
				</v-btn>
			</v-col>
		</v-row>
			
<!-- CAPTURA DE RESULTADOS -->
		<v-row v-if="Terminar"> 
			<v-col cols="12" align="center" class="font-weight-black "> RESULTADO DEL COMPROMISO </v-col>
			<v-col cols="12" md="6"> <!-- OBSERVACIONES -->
				<v-textarea
					v-model="obs_usuario" label="Resultados" hide-details outlined
					placeholder="Escriba el resultado del compromiso..." color="celeste"
				></v-textarea>
			</v-col>

			<v-col cols="12" align="right" class="mt-0" > <!-- BOTON PARA GUARDAR INFORMACION -->
				<v-card-actions>
					<v-btn color="gris" dark  small @click="Terminar = false">Cancelar</v-btn> <v-spacer></v-spacer> 
					<v-btn color="celeste" dark  small @click="validaInfo">Guardar Información</v-btn> 
				</v-card-actions>
			</v-col>
		</v-row>

		<v-dialog v-model="solicitarModal" persistent max-width="400">
			<v-card class="pa-4 ">
        <v-card-text class="font-weight-black my-1 " align="center">SOLICITUD DE PEDIDO</v-card-text>
				<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste"
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos"
				></v-select> 
				<v-text-field 
					v-model="referencia" 
					hide-details dense 
					label="PRODUCTO" 
					filled color="celeste" 
					class="mt-1 font-weight-black"
				/>

				<flexografia :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===1"/>
				<bordados    :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===2"/>
				<digital     :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===3"/>
				<offset 		 :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===4"/>
				<serigrafia  :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===5"/>
				<empaque 		 :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===6"/>
				<sublimacion :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===7"/>
				<tampografia :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===8"/>
				<uv 				 :depto_id="depto.id" @modal="solicitarModal = $event" v-if="activaFormulario===9"/>
			</v-card>
		</v-dialog>

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
<!--  -->
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

		<v-dialog v-model="terminarCompromiso" persistent max-width="400" > <!-- PROCESO PARA TERMINAR COMPROMISO  -->
			<v-card>
				<v-col cols="12" class="pa-4">
					<strong >EL COMPROMISO SE FINALIZARA </strong> <br> ¿Esta seguro de continuar?
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
	import metodos     from '@/mixins/metodos.js'
	import flexografia from '@/views/Formularios/flexografia.vue'
	import bordados    from '@/views/Formularios/bordados.vue'
	import digital     from '@/views/Formularios/digital.vue'
	import offset      from '@/views/Formularios/offset.vue'
	import serigrafia  from '@/views/Formularios/serigrafia.vue'
	import empaque	 	 from '@/views/Formularios/empaque.vue'
	import sublimacion from '@/views/Formularios/sublimacion.vue'
	import tampografia from '@/views/Formularios/tampografia.vue'
	import uv 				 from '@/views/Formularios/uv.vue'
	
	export default {
		mixins:[metodos],
		components: {
			flexografia,
			bordados,
			digital,
			offset,
			serigrafia,
			empaque,
			sublimacion,
			tampografia,
			uv
		},
		data(){
			return{
				referencia  			: '',
				fase_venta  			: '',
				obs_usuario 			: '',
				obs         			: '',
				Terminar    			: false,
				citaConfirmada    : null,
				terminarCompromiso: false,
				// MANEJO DE FECHAS
				fecha						  : new Date().toISOString().substr(0, 10),
				fechamodal 			  : false,
				fecha_compromiso  : false,
				hora 					    : null,
        horamodal			    : false,
				hora_compromiso   : false,
				activaReagendar   : false,
				confirmarModal    : false,
				// FORMLARIOS
				solicitarModal    : false,
				activaFormulario  : 1,
				parametros        : '',
				modoVista         : 0,
				depto             : { id:1, nombre:'FLEXOGRAFÍA'},
				deptos            : [],
				// ALERTAS
				snackbar          : false,
				text		          : '',
				color		          : 'green',
				dialog            : false,
				textDialog        : "Guardando Información",
				Correcto          : false,
				textCorrecto      : '',
			}
		},

		created(){
			if(!this.$route.params.detalle){  window.history.go(-1); } // SI NO OBTENGO LA INFORMACION RETORNO VISTA.
			moment.locale('es') 														           // INICIAR MOMENT 
			this.fecha          = this.traerFechaActual();             // ASIGNAR FECHA ACTUAL
			this.hora           = this.traerHoraActual(); 	           // ASIGNAR HORA ACTUAL
			this.detalle 				= this.$route.params.detalle           // PARAMETROS QUE RECIBO DE LA VISTA 
			this.citaConfirmada = this.detalle.confirma_cita           // CONFIRMACION DE CITA
			this.consultaDeptos()
		},

		watch:{ 
			depto(){
				this.activaFormulario = this.depto.id
			}
		},
		computed:{ 
			// ...mapGetters('Compromisos'  ,['Loading','getCompromisos']),  
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']),// IMPORTANDO USO DE VUEX

			confirmarCita(){
				this.confirmarModal = false; 	this.dialog = true ; 					// ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , confirma_cita: 1 }  // FORMAR ARRAY A MANDAR 
				this.$http.post('confirmarcita', payload).then((response)=>{
					this.citaConfirmada = 1;
				}).catch((error)=>{
					console.log('error', error)
				}).finally(()=> this.dialog= false)
			},

			validaFechas(){
				var fi = this.fecha + " " + this.hora; 	var ff = this.traerFechaActual() + " " + this.traerHoraActual();
				if(!this.fecha)			{ this.snackbar=true;this.color="error";this.text="No puedes omitir la FECHA INICIAL"	; return}
				if(!this.hora)			{ this.snackbar=true;this.color="error";this.text="No puedes omitir la HORA INICIAL"	; return}
				if(ff > fi){ this.snackbar=true; this.color="error"; this.text="La FECHA Y HORA del compromiso no puede ser antes de la actual."; return};
				this.reagendar()
			},

			reagendar(){
				this.dialog = true ; setTimeout(() => (this.dialog = false), 3000) // ACTIVAR DIALOGOS DE GUARDAR
				const payload = { id: this.detalle.id , 
													fecha: this.fecha, 
													hora: this.hora, 
													obs: this.obs
													} // FORMAR ARRAY A MANDAR

				this.$http.post('reagendar', payload).then((response)=>{
					var me = this; this.dialog = false; this.Correcto = true ; this.textCorrecto = response.bodyText;
					this.consultaCompromisos() //ACTUALIZAR CONSULTA DE CLIENTES
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
				}).catch((error)=>{
					console.log('error', error)
					this.mostrarError(error.bodyText)
				}).finally(()=> {
					// this.$router.push({name: 'compromisos' });
					this.dialog = false; 
				})
			},
	
			validaInfo(){
				if(!this.obs_usuario){ this.snackbar=true; this.color="error"; 
															 this.text="Debes agregar alguna OBSERVACIÓN DEL COMPROMISO."; return}
				this.terminarCompromiso = true;
			},

			GuardarInfo(){
				const payload = { id: this.detalle.id,
													obs_usuario: this.obs_usuario,
													cumplimiento: 1,
													fecha_cierre: this.traerFechaActual(),
													hora_cierre: this.traerHoraActual()
												}
												
				this.terminarCompromiso = false; 
				this.dialog = true ; // ACTIVAR DIALOGOS DE GUARDAR

				this.$http.post('terminar.compromiso', payload).then(response =>{
					var me = this;this.dialog = false;this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
					this.consultaCompromisos() 
				}).catch(error =>{
					this.mostrarError(error.bodyText)
					console.log('error', error)
				}).finally(()=> this.dialog = false)
			},

			mostrarError(mensaje){
				this.snackbar=true; this.text=mensaje;
			}
			
		}
	}
</script>