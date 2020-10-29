<template>
	<v-content class="pa-0">
		<v-row >
			<!-- //! SNACK BAR  -->
			<v-snackbar v-model="snackbar" multi-line :timeout="2000" top :color="color"> {{text}}
				<template v-slot:action="{ attrs }">
					<v-btn color="white" text @click="snackbar = false" v-bind="attrs"> Cerrar </v-btn>
				</template>
			</v-snackbar>
			
			<!--//! TITILO DE LA VISTA -->
			<v-card-actions class="mx-3 pa-0"> 
				<v-btn color="gris" dark small fab @click="evualuaRetorno()">
					<!-- onClick="history.go(-1); return false;" -->
					<v-icon  >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium">Detalle del Compromiso</v-card-title>	
			</v-card-actions>
			<!--//! DETALLE DEL PROSPECTO -->
		  <v-card-text  class="my-0 py-0"> 
				<v-row justify="center" >
					<v-col cols="12" class="pa-1">
					  <v-card outlined>
						  <v-simple-table dense >
						    <template v-slot:default>
						      <tbody >
										<tr >
                      <td class="font-weight-black">Cliente</td>
											<td class="overline "> {{ detalle.nomcli }}</td>
                    </tr>
                    <tr>
                      <td class="font-weight-black">Catégoria</td>
						          <td class="overline">{{ detalle.nomcatego }}</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Hora</td>
											<td class="overline">
												{{ detalle.hora >='12:00'? detalle.hora +' '+'pm': detalle.hora+ ' '+'am' }}
											</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Comentarios</td>
											<td class="overline"> {{ detalle.obs }}</td>
                    </tr>
						      </tbody>
						    </template>
						  </v-simple-table>

					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
			<!--//! TELEFONO 1 -->
			<v-col cols="6" align="center" v-if="!tSolicitudes "> 
				<v-btn  color="info" outlined block :disabled="detalle.tel1 === '' "><v-icon left>phone</v-icon>
						<a :href="`tel:${detalle.tel1}`" style="text-decoration:none;">{{ detalle.tel1? detalle.tel1: '00-00-00' }}</a>
						<!--<a :href="`tel:${detalle.tel1}`">{{phone}}</a>  -->
					</v-btn>
			</v-col>
			 <!--//! TELEFONO 2 -->
			<v-col cols="6" align="center" v-if="!tSolicitudes">
				<v-btn  color="info" outlined block :disabled="detalle.tel2 === '' "><v-icon left>phone</v-icon>
					<a :href="`tel:${detalle.tel2}`" style="text-decoration:none;">{{ detalle.tel2? detalle.tel2: '00-00-00' }}</a>
				</v-btn>
			</v-col>
			<!--//! CONFIRMAR CITA  -->
			<v-col cols="12" align="center" v-if="!tSolicitudes"> 
				<v-btn color="red darken-4" :disabled="dialog" persistent :loading="dialog" block 
																		 dark  @click="confirmarModal = true" v-if="citaConfirmada === 0" >
					CONFIRMAR CITA
				</v-btn>
				<v-btn color="green" block dark  v-else > CITA CONFIRMADA </v-btn>
			</v-col>
			<!--//! REAGENDAR -->
			<v-col cols="12" v-if="!activaReagendar && !tSolicitudes" align="center"> 
				<v-btn color="morado" block dense dark @click="activaReagendar= true"> Reagendar </v-btn>
			</v-col>
			<!--//! FECHA DE COMPROMISO -->
			<v-col cols="6" v-if="activaReagendar"> 
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
			<!--//! HORA DEL COMPROMISO -->
			<v-col cols="6" v-if="activaReagendar"> 
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
			<!--//! COMENARIO -->
			<v-col cols="12" v-if="activaReagendar"> 
				<v-textarea
					v-model ="obs" outlined label="Comentario" placeholder="Agregar un comentario ..."
					rows="2" hide-details dense color="celeste"
				></v-textarea>
			</v-col>
			<!-- //! CANCELAR REAGENDADO * AGENDADO  -->
			<v-col cols="12" v-if="activaReagendar"> 
				<v-card-actions>
					<v-btn small dark color="gris" @click="activaReagendar=false"> Cancelar </v-btn>
					<v-spacer></v-spacer>
					<v-btn small dark color="rosa" :disabled="dialog" persistent :loading="dialog" @click="validaFechas" > Reagendar </v-btn>
				</v-card-actions>
			</v-col>
		</v-row>
		<!-- //! OPCIONES DE ACCION **FINALIZAR**SOLICITAR-PEDIDO*** -->
		<v-row justify="center" v-if="citaConfirmada===1 && !Terminar && !tSolicitudes"  >
			<v-col cols="6" class="text-center" >
				<v-btn color="rosa" large dark outlined style="height:100px; width:150px" @click="Terminar=true" href="#fase"> 
					Finalizar <br> compromiso
				</v-btn>
			</v-col>
			<v-col cols="6" class="text-center" >
				<v-btn color="celeste" large dark outlined style="height:100px; width:150px" @click="tSolicitudes = true" href="#fase"> 
					Solicitar <br> pedido 
				</v-btn>
			</v-col>
		</v-row>
			
		<!-- //!CAPTURA DE RESULTADOS -->
		<v-row v-if="Terminar && !tSolicitudes"> 
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

		<!-- //!TABLA DE SOLICITUDES -->
		<v-row v-if="!Terminar && tSolicitudes">
			<v-col cols="12">
				<v-card-text class="font-weight-black my-0 py-0 subtitle-1" align="center">SOLICITUD DE PEDIDO</v-card-text>
				<v-btn color="orange" block dark @click="verDetalle(1)"> AGREGAR PRODUCTO </v-btn>
			</v-col>
			<v-col cols="12">
				<v-card outlined>
					<v-card-text v-if="!getSolicitudes.length"> No se a registrado ningun producto </v-card-text>
					<v-simple-table v-else>
						<template v-slot:default>
							<thead>
								<tr>
									<th class="text-left"> Referencia </th>
									<th class="text-left"></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item,i) in getSolicitudes" :key="i" >
									<td class="font-weight-black">{{ item.referencia }}</td>
									<td align="right">
										<v-btn small color="success" class="mx-1"  @click="verDetalle(2,item)"> <v-icon>mdi-pencil</v-icon> </v-btn>
										<v-btn small color="error" class="mx-1" @click="EliminarProducto(item.id)"> <v-icon>mdi-delete</v-icon> </v-btn>
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			<v-col cols="12" class="py-1 my-0 pb-0">
				<v-textarea
					v-model="nota" outlined rows="2" label="Comentario" placeholder="Agrega un comentario..."
        ></v-textarea>
			</v-col>

			<!-- //!BOTON PARA GUARDAR INFORMACION -->
			<v-col cols="12" align="right" class="py-0 my-0" > 
				<v-card-actions>
					<v-btn color="gris" dark  small @click="tSolicitudes = false">Cancelar</v-btn> <v-spacer></v-spacer> 
					<v-btn color="celeste" dark  small @click="validaInfo">Guardar Información</v-btn> 
				</v-card-actions>
			</v-col>
		</v-row>

		<!-- // !ESTE ES EL BUENO ECHALE GANAS PARA ENTENDERLE TE QUIERO MUCHO -->
		<v-dialog v-model="solicitarModal" persistent max-width="400">
			<v-card class="pa-4 ">
        <v-card-text class="font-weight-black my-1 " align="center">SOLICITUD DE PEDIDO</v-card-text>
				<!-- //! SELECCION DEL DEPARTAMENTO  -->
				<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" v-if="modoVista === 1"
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos"
				></v-select> 
					<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" v-else 
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos" disabled 
				></v-select> 
				
				<!-- //! FORMULARIOS  -->
				<flexografia 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===1"
				/>
				<!-- <bordados    
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===2"
				/> -->
				<digital     
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===3"
				/>
				<!-- //TODO -- OFSET FALTANTE -->
				<!-- <offset 		 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===4"
				/> -->
				<!-- <serigrafia  
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===5"
				/> -->
				<!-- //TODO -- EMPAQUE FALTANTE -->
				<!-- <empaque 		 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===6"
				/>
				<sublimacion 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===7"
				/> -->
				<!-- <tampografia 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===8"
				/> -->
				<!-- <uv 				 
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					@modal="solicitarModal = $event" 
					v-if="activaFormulario===9"
				/> -->
			</v-card>
		</v-dialog>

		<!-- //!MODAL PARA GUARDAR LA INFORMACION -->
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
		<!-- //!PROCESO PARA CONFIRMACION  -->
		<v-dialog v-model="confirmarModal" persistent max-width="400" > 
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
		</v-dialog> 

		<!--//!PROCESO PARA TERMINAR COMPROMISO  -->
		<v-dialog v-model="terminarCompromiso" persistent max-width="400" > 
			<v-card>
				<v-col cols="12" class="pa-4">
					<strong >EL COMPROMISO SE FINALIZARA </strong> <br> ¿Esta seguro de continuar?
				</v-col>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn color="gris" text small @click="terminarCompromiso = false">Cancelar</v-btn>
					<v-btn color="rosa" dark small @click="EnviarSolicitud()" v-if="tSolicitudes">Continuar</v-btn>
					<v-btn color="rosa" dark small @click="EnviarResultado()" v-else>Continuar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog> 

		<!-- //!ALERTA DE CONTENIDO -->
		<v-dialog v-model="alertaDeContenido" persistent max-width="500">
			<v-card class="pa-1" >
				<v-card-title class="subtitle-1 font-weight-black"> HAY UNA SOLICITUD PENDIENTE  </v-card-title>
				<v-card-subtitle class="subtitle-2 font-weight-black">¿ESTA SEGURO DE QUERER REGRESAR?</v-card-subtitle>
				<v-divider class="my-0 py-3 " ></v-divider>
				<v-card-subtitle align="center" class="red--text font-weight-bold "> SE PERDERA TODA LA INFORMACIÓN </v-card-subtitle>
				<v-divider class="my-0 py-2 "></v-divider>
				<v-card-actions>
					<v-spacer/>
					<v-btn dark outlined color="gris" @click="alertaDeContenido = false">Cancelar</v-btn>
					<v-btn dark color="rosa" @click="volverAtras()" >Confirmar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<div id="fase"></div>
	</v-content>
</template>

<script>
	var moment = require('moment'); 
	import {mapGetters, mapActions} from 'vuex'
	import metodos     from '@/mixins/metodos.js'
	import flexografia from '@/views/Formularios/flexografia.vue'
	// import bordados    from '@/views/Formularios/bordados.vue'
	import digital     from '@/views/Formularios/digital.vue'
	// import offset      from '@/views/Formularios/offset.vue'
	// import serigrafia  from '@/views/Formularios/serigrafia.vue'
	// import empaque	 	 from '@/views/Formularios/empaque.vue'
	// import sublimacion from '@/views/Formularios/sublimacion.vue'
	// import tampografia from '@/views/Formularios/tampografia.vue'
	// import uv 				 from '@/views/Formularios/uv.vue'
	
	export default {
		mixins:[metodos],
		components: {
			flexografia,
			// bordados,
			digital,
			// offset,
			// serigrafia,
			// empaque,
			// sublimacion,
			// tampografia,
			// uv
		},
		data(){
			return{
				fase_venta  			: '',
				obs_usuario 			: '',
				obs         			: '',
				Terminar    			: false,
				citaConfirmada    : null,
				terminarCompromiso: false,
				nota 						  : '',
				// MANEJO DE FECHAS
				fecha						  : new Date().toISOString().substr(0, 10),
				fechamodal 			  : false,
				fecha_compromiso  : false,
				hora 					    : null,
        horamodal			    : false,
				hora_compromiso   : false,
				activaReagendar   : false, // MODAL DE REAGENDACION
				confirmarModal    : false, // MODAL DE CONFIRMACION
				alertaDeContenido : false, // MODAL DE ALERTA DE CONTENIDO 
				// FORMLARIOS
				tSolicitudes      : false,
				solicitarModal    : false, // !ABRE EL MODAL PARA FORMULARIOS
				activaFormulario  : 1,
				parametros        : '',
				modoVista         : 1,
				depto             : { id:1, nombre:'FLEXOGRAFÍA'},
				deptos            : [],
				// ALERTAS
				snackbar          : false,
				text		          : '',
				color		          : 'error',
				dialog            : false,
				textDialog        : "Guardando Información",
				Correcto          : false,
				textCorrecto      : '',
			}
		},

		created(){
			if(!this.$route.params.detalle){  window.history.go(-1); } // SI NO OBTENGO LA INFORMACION RETORNO VISTA.
			this.vaciaSolicitudes()
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
			...mapGetters('Solicitudes'  ,['getSolicitudes']),  
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']),// IMPORTANDO USO DE VUEX
		  ...mapActions('Solicitudes'  ,['eliminaProducto','vaciaSolicitudes']),
			verDetalle(modo,item){
				// console.log('verdetalle otro', this.deptos[item.dx].nombre)
				if(modo === 2){  this.depto = { id: item.dx } };
				this.solicitarModal = true;
				this.parametros 		= item;
				this.modoVista      = modo; 
			},

			EliminarProducto(id){
        this.eliminaProducto(id).then( res =>{
          if(res){ this.snackbar = true; this.text ="El producto se elimino correctamente." }
        })
      },

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
				if(!this.fecha)			{ this.snackbar=true;this.text="No puedes omitir la FECHA INICIAL"	; return}
				if(!this.hora)			{ this.snackbar=true;this.text="No puedes omitir la HORA INICIAL"	; return}
				if(ff > fi){ this.snackbar=true;; this.text="La FECHA Y HORA del compromiso no puede ser antes de la actual."; return};
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
				if(this.tSolicitudes){
					if(!this.getSolicitudes.length){
						this.snackbar=true;	this.text="Debes agregar al menos 1 producto"; return;
					}
				}else{
					if(!this.obs_usuario){ 
						this.snackbar=true;	this.text="Debes agregar alguna OBSERVACIÓN DEL COMPROMISO."; return;
					}
				}
			
				this.terminarCompromiso = true;
			},

			EnviarResultado(){
				const payload = {
													fecha: this.traerFechaActual(),
													hora : this.traerHoraActual(),
													obs_usuario  : this.obs_usuario,
													cumplimiento: 1,
											 }
				console.log('payload por resultado', payload)
				this.terminarCompromiso = false; this.dialog = true;
					// this.$http.post('add.solicitudes', payload).then(response =>{
				// 	var me = this;this.dialog = false;this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
				// 	this.consultaCompromisos() 
				// }).catch(error =>{
				// 	this.mostrarError(error.bodyText)
				// 	console.log('error', error)
				// }).finally(()=> this.dialog = false)
			},

			EnviarSolicitud(){
				let detalle = this.validaProductos();
				const payload = { 
													fecha     : this.traerFechaActual(),
													hora      : this.traerHoraActual(),
													urgencia  : 1,
													id_usuario: this.detalle.id_vendedor,
													id_cliente: this.detalle.id_cliente,
													nota	    : this.nota,
													detalle   : detalle,
												}
				console.log('payload por solicitud', payload)
												
				this.terminarCompromiso = false; this.dialog = true ; // ACTIVAR DIALOGOS DE GUARDAR

				// this.$http.post('add.solicitudes', payload).then(response =>{
					var me = this;
					// this.dialog = false;this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(function(){ me.$router.push({name: 'compromisos' })}, 1000);
				// 	this.consultaCompromisos() 
				// }).catch(error =>{
				// 	this.mostrarError(error.bodyText)
				// 	console.log('error', error)
				// }).finally(()=> this.dialog = false)
			},

			validaProductos(){
				let Temporal = [];
				for(let i=0;i<this.getSolicitudes.length;i++){
					if(this.getSolicitudes[i].tproducto === 2){
						console.log('xmodificar', this.getSolicitudes[i].xmodificar )
						Temporal.push(this.getSolicitudes[i].xmodificar) 
					} else{
						Temporal.push(this.getSolicitudes[i])
					}
				}
				console.log('Temportal',Temporal)
				return Temporal;
			},

			mostrarError(mensaje){
				this.snackbar=true; this.text=mensaje;
			},

			volverAtras(){
				this.alertaDeContenido = false;
				this.vaciaSolicitudes()
				window.history.go(-2);
			}
		
			
		}
	}
</script>