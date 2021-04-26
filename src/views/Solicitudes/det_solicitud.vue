<template>
	<v-content class="pa-0">
		<v-row>
			<!-- //! SNACK BAR  -->
			<v-snackbar v-model="snackbar" multi-line :timeout="3000" top :color="color"> {{text}}
				<template v-slot:action="{ attrs }">
					<v-btn color="white" text @click="snackbar = false" v-bind="attrs"> Cerrar </v-btn>
				</template>
			</v-snackbar>
			
			<!--//! TITILO DE LA VISTA -->
			<v-card-actions class="mx-3 pa-0"> 
				<v-btn color="gris" dark x-small fab onClick="history.go(-1); return false;">
					<v-icon  >mdi-arrow-left-thick</v-icon>
				</v-btn>
				<v-card-title class="font-weight-medium">Detalle de Solicitud </v-card-title>	
			</v-card-actions>
      
			<!--//! DETALLE DEL PROSPECTO -->
		  <v-card-text  class="my-0 py-0"> 
				<v-row justify="center" >
					<v-col cols="12" class="pa-1">
					  <v-card outlined>
						  <v-simple-table dense >
						    <template v-slot:default>
						      <tbody>
										<tr>
                      <td class="font-weight-black">Cliente</td>
											<td class="overline "> {{ detalle.nomcli }}</td>
                    </tr>
                    <tr>
                      <td class="font-weight-black">Fecha</td>
											<td class="overline">
												 {{  moment(detalle.fecha).format('LL') }}
											</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Hora</td>
											<td class="overline">
												{{ detalle.hora >='12:00'? detalle.hora +' '+'pm': detalle.hora+ ' '+'am' }}
											</td>
                    </tr>
										<tr>
                      <td class="font-weight-black">Nota</td>
											<td class="overline"> {{ detalle.nota }}</td>
                    </tr>
						      </tbody>
						    </template>
						  </v-simple-table>

					  </v-card>
					</v-col>
				</v-row>
      </v-card-text>
		</v-row>

    <v-container style="height: 400px;" v-if="Loading">
      <loading/>
    </v-container>

		<!-- //!TABLA DE SOLICITUDES -->
		<v-row v-if="!Loading">
			<v-col cols="12">
				<v-card-text class="font-weight-black my-0 py-0 subtitle-1" align="center">DETALLE DEL PEDIDO</v-card-text>
				<v-btn color="orange" block dark @click="verDetalle(3, detalle)"> AGREGAR PRODUCTO </v-btn>
			</v-col>
			
			<v-col cols="12">
				<v-card outlined>
					<v-card-text v-if="!getPartidas.length"> No se a registrado ningun producto </v-card-text>
					<v-simple-table v-else>
						<template v-slot:default>
							<thead>
								<tr>
									<th class="text-left"> Referencia </th>
									<th class="text-left"></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item,i) in getPartidas" :key="i" >
									<td class="font-weight-black">{{ item.referencia || item.ft }}</td>
									<td align="right">
										<v-btn text small color="success" class="mx-1"  @click="verDetalle(4,item)"> <v-icon>mdi-pencil</v-icon> </v-btn>
										<v-btn text small color="error" class="mx-1" @click="alertaEliminar = true; productoAEliminar = item"> <v-icon>mdi-delete</v-icon> </v-btn>
									</td>
								</tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			
			
			
			<!-- //!BOTON PARA GUARDAR INFORMACION -->
			<v-col cols="12" align="right" class="py-0" v-if="detalle.estatus === 1"> 
				<v-btn color="error" block outlined dark small @click="alertaCancelacion=true">CANCELAR SOLICITUD</v-btn> 
			</v-col>
		</v-row>

		<!-- // !ESTE ES EL BUENO ECHALE GANAS PARA ENTENDERLE TE QUIERO MUCHO -->
		<v-dialog v-model="solicitarModal" persistent max-width="400">
			<v-card class="pa-4 ">
        <v-card-text class="font-weight-black my-1 " align="center">SOLICITUD DE PEDIDO</v-card-text>
				<!-- //! SELECCION DEL DEPARTAMENTO  -->
				<v-select
						v-model="depto" :items="deptos" item-text="nombre" item-value="id" outlined color="celeste" 
						dense hide-details  label="Departamentos" return-object placeholder ="Departamentos"
						v-if="modoVista === 1 || modoVista === 3"
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
					:actualiza="actualiza"
					@modal="solicitarModal = $event" 
					@put="actualiza = $event" 
					v-if="activaFormulario===1"
				/>
				
				<digital     
					:depto_id="depto.id" 
					:modoVista="modoVista"
					:parametros="parametros"
					:actualiza="actualiza"
					@modal="solicitarModal = $event" 
					@put="actualiza = $event" 
					v-if="activaFormulario===3"
				/>
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
		
		<overlay v-if="overlay"/>

	

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
		<v-dialog v-model="alertaEliminar" persistent max-width="500">
			<v-card class="" >
				<v-card-title class="subtitle-1 font-weight-black"> EL PRODUCTO SE ELIMINARA  </v-card-title>
				<v-card-subtitle class="subtitle-2 font-weight-black">¿ ESTA SEGURO DE QUERER CONTINUAR ?</v-card-subtitle>
				<v-divider class="my-0 py-3" ></v-divider>
				<v-card-subtitle align="center" class="red--text font-weight-bold "> SE ELIMINARA DE FORMA DEFINITIVA </v-card-subtitle>
				<v-divider class="my-0 py-2 " ></v-divider>
				<v-card-actions>
					<v-spacer/>
					<v-btn dark outlined color="gris" @click="alertaEliminar = false">Cancelar</v-btn>
					<v-btn dark color="error" @click="EliminarProducto()" >Eliminar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<!-- //!ALERTA DE CANCELACION -->
		<v-dialog v-model="alertaCancelacion" persistent max-width="500">
			<v-card class="" >
				<v-card-title class="subtitle-1 font-weight-black"> LA SOLICITUD SE CANCELARA  </v-card-title>
				<v-card-subtitle class="subtitle-2 font-weight-black">¿ ESTA SEGURO DE QUERER CONTINUAR ?</v-card-subtitle>
				<v-divider class="my-0 py-3" ></v-divider>
				<v-card-subtitle align="center" class="red--text font-weight-bold "> SE CANCELARA DE FORMA DEFINITIVA </v-card-subtitle>
				<v-divider class="my-0 py-2 " ></v-divider>
				<v-card-actions>
					<v-spacer/>
					<v-btn dark outlined color="gris" @click="alertaCancelacion = false">Regresar</v-btn>
					<v-btn dark color="error" @click="cancelarSolicitud()" >Continuar</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<div id="fase"></div>
	</v-content>
</template>

<script>
	var moment = require('moment'); moment.locale('es') /// inciar Moment 
	import {mapGetters, mapActions} from 'vuex'
	import metodos     from '@/mixins/metodos.js'
	import overlay     from '@/components/overlay.vue'
	import flexografia from '@/views/Formularios/flexografia.vue'
	import digital     from '@/views/Formularios/digital.vue'
  import loading     from '@/components/loading.vue'

	export default {
		mixins:[metodos],
		components: {
      loading,
			overlay,
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
        Loading: true,
				fase_venta  			: '',
				obs_usuario 			: '',
				obs         			: '',
				Terminar    			: false,
				terminarCompromiso: false,
				nota 						  : '',
				// MANEJO DE FECHAS
				
				confirmarModal    : false, // MODAL DE CONFIRMACION
				alertaEliminar    : false, // MODAL DE ALERTA DE ELIMINAR 
				productoAEliminar : [],
				alertaCancelacion   : false,

				// FORMLARIOS
				tSolicitudes      : false,
        solicitarModal    : false, // !ABRE EL MODAL PARA FORMULARIOS
        
				activaFormulario  : 1,
				actualiza         : false,
				parametros        : '',
				modoVista         : 1,
				depto             : { id:1, nombre:'FLEXOGRAFÍA'},
        deptos            : [],
        
				partidas: [],
				// ALERTAS
				snackbar          : false,
				text		          : '',
				color		          : 'error',
				dialog            : false,
				textDialog        : "Guardando Información",
				Correcto          : false,
				textCorrecto      : '',

				overlay           : false
			}
		},

		created(){
			if(!this.$route.params.detalle){  window.history.go(-1); } // SI NO OBTENGO LA INFORMACION RETORNO VISTA.
      this.vaciaSolicitudes()
			this.detalle 				= this.$route.params.detalle           // PARAMETROS QUE RECIBO DE LA VISTA 
      this.init();
			this.consultaDeptos()
		},

		watch:{ 
			depto(){
				this.activaFormulario = this.depto.id
			},

			actualiza(){
				this.init();
			}
		},
		computed:{ 
			...mapGetters('Solicitudes'  ,['getPartidas']),  
		},

		methods:{
			// ...mapActions('Compromisos'  ,['consultaCompromisos']),// IMPORTANDO USO DE VUEX
      ...mapActions('Solicitudes'  ,['eliminaProducto','vaciaSolicitudes','PartidasEncontradas']),

      init(){
        this.Loading = true;
        this.$http.get('detalle.solicitud/'+ this.detalle.id ).then(response =>{ 
            this.PartidasEncontradas(response.body);
				}).finally(()=>{ this.Loading = false;})
      },
      
			verDetalle(modo,item){
				if(modo === 4){  
					this.depto = { id: item.dx } 
				};
				this.solicitarModal = true;
				this.parametros 		= item;
				this.modoVista      = modo; 
			},

			EliminarProducto(id){
				if(this.productoAEliminar.estatus > 1 ){
					this.snackbar= true; this.text="Esté producto no se puede eliminar ya que esta siendo atendido!";
					return;
        }

				this.alertaEliminar = false; this.overlay = true;
				const payload = new Object();
							payload.id_px  			 = this.productoAEliminar.id
							payload.id_solicitud = this.productoAEliminar.id_solicitud;
							payload.id_dx        = this.productoAEliminar.tipo_prod ===3? this.productoAEliminar.id_dx:'';      // id de la c
							payload.tipo_prod    = this.productoAEliminar.tipo_prod;
							payload.dx					 = this.productoAEliminar.dx
				// console.log('payload', payload)

				this.$http.post('eliminar.producto',payload).then( response => {
					console.log('RESPUESTA', response.body)
					var me = this;this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(()=>{ me.Correcto = false }, 1000);
					this.init()
				}).catch( error =>{
					console.log('error', error)
				}).finally(()=>{ 
					this.overlay = false;
				})
      },
	
			validaInfo(){
				if(this.tSolicitudes){
					if(!this.getPartidas.length){
						this.snackbar=true;	this.text="DEBES AGREGAR AL MENOS 1 PRODUCTO"; return;
					}
				}else{
					if(!this.obs_usuario){ 
						this.snackbar=true;	this.text="DEBES AGREGAR EL RESULTADO DEL COMPROMISO."; return;
					}
				}
			
				this.terminarCompromiso = true;
			},

			EnviarSolicitud(){
				let detalle = this.validaProductos();
				this.overlay = true; this.terminarCompromiso = false;

				const payload = new Object();
							payload.id_compromiso = this.detalle.id,
							payload.fecha      = this.traerFechaActual(),
							payload.hora       = this.traerHoraActual(),
							payload.id_usuario = this.detalle.id_vendedor,
							payload.id_cliente = this.detalle.id_cliente,
							payload.nota	     = this.nota,
							payload.detalle    = detalle,

				this.$http.post('add.Solicitud', payload).then(response =>{
					var me = this; 
					this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(()=>{ me.$router.push({name: 'compromisos' })}, 1000);
					this.consultaCompromisos() 
				}).catch(error =>{
					this.mostrarError(error.bodyText)
					console.log('error', error)
				}).finally(()=> this.overlay = false)
			},

			cancelarSolicitud(){
				this.alertaCancelacion = false; this.overlay = true;
				const payload = new Object();
							payload.id_solicitud = this.detalle.id
							payload.estatus      = 4;

				this.$http.post("cancelar.solicitud", payload).then(response =>{
					var me = this; 
					this.Correcto = true ; this.textCorrecto = response.bodyText;
					setTimeout(()=>{ me.$router.push({name: 'solicitudes.hechos' })}, 1000);

				}).catch(error =>{
					console.log('error', error)

					this.mostrarError(error.bodyText);
				}).finally(()=>{ 
					this.overlay = false
				})
				
			},

			validaProductos(){
				let Temporal = [];
				for(let i=0;i<this.getPartidas.length;i++){
					if(this.getPartidas[i].tproducto === 2){
						// console.log('xmodificar', this.getPartidas[i].xmodificar )
						Temporal.push(this.getPartidas[i].xmodificar) 
					} else{
						Temporal.push(this.getPartidas[i])
					}
				}
				// console.log('Temportal',Temporal)
				return Temporal;
			},

			mostrarError(mensaje){
				this.snackbar=true; this.text=mensaje;
			},

		}
	}
</script>