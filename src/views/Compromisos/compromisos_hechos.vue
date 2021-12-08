<template>
	<v-main class="pa-0">
		<v-row no-gutters>
			<v-col cols="12" class="text-center my-2">
				<span class="font-weight-bold mt-3">COMPROMISOS REALIZADOS </span>
			</v-col>

			<v-col cols="6" sm="6" xl="2" class="pa-1" > <!-- FECHA DE COMPROMISO -->
				<v-dialog ref="fecha1" v-model="fechamodal1" :return-value.sync="fecha1" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha1" label="Fecha Inicio" append-icon="event" readonly v-on="on"
								outlined dense hide-details color="celeste"
						></v-text-field>
					</template>
					<v-date-picker v-model="fecha1" locale="es-es" color="rosa"  scrollable>
						<v-spacer></v-spacer>
						<v-btn text small color="gris" @click="fechamodal1 = false">Cancelar</v-btn>
						<v-btn dark small color="rosa" @click="$refs.fecha1.save(fecha1)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="6" sm="6" xl="2" class="pa-1" > <!-- FECHA DE COMPROMISO -->
				<v-dialog ref="fecha2" v-model="fechamodal2" :return-value.sync="fecha2" persistent width="290px">
					<template v-slot:activator="{ on }">
						<v-text-field
							 v-model="fecha2" label="Fecha fin" append-icon="event" readonly v-on="on"
								outlined dense hide-details color="celeste"
						></v-text-field>
					</template>
					<v-date-picker v-model="fecha2" locale="es-es" color="rosa"  scrollable>
						<v-spacer></v-spacer>
						<v-btn text small color="gris" @click="fechamodal2 = false">Cancelar</v-btn>
						<v-btn dark small color="rosa" @click="$refs.fecha2.save(fecha2)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<!--
			<v-col cols="1"  class="mx-1">
				<v-btn color="rosa" fab small dark @click="init()"><v-icon>search</v-icon> </v-btn>
			</v-col>	-->

			<v-col cols="12" class="pa-1">
				<v-text-field
						label="Buscar por cliente"
						dense
						outlined
						hide-details
						v-model="search"
						append-icon="search"
						color="celeste"
				></v-text-field>
			</v-col>
			
			<v-container style="height: 400px;" v-if="Loading">
				<loading/>
			</v-container>

			<v-col cols="12">
				<v-card v-for="(item, i) in filterCompromisos" :key ="i" class="py-0" color="celeste">
						<v-btn  dark absolute bottom color="celeste" right fab small  @click="verResumen(item)" > 
							<v-icon> mdi-eye </v-icon>
						</v-btn>
					
						<v-card  class="mt-6 pa-1 elevation-10" >
							<v-card flat>
								<v-card-text class="py-1"><span class="font-weight-bold"> CLIENTE:  </span> {{ item.nomcli }}  </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> FECHA:    </span> {{  moment(item.fecha).format('LL') }}   </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> HORA:     </span> {{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }} </v-card-text>
								<v-card-actions class="pa-1">
								</v-card-actions>
							</v-card>
						</v-card>

					<!-- <v-simple-table dense flat>
						<template v-slot:default>
							<tbody>
								<tr> <td class="font-weight-bold">Cliente  </td> <td> {{ item.nomcli }}   </td></tr>
								<tr> <td class="font-weight-bold">Fecha		 </td> <td> {{ item.fecha }} 		</td></tr> 
								<tr> <td class="font-weight-bold">Hora		 </td> <td> {{ item.hora }} {{ item.hora <12? 'a.m.':'p.m.'}}		</td></tr> 
								<tr> <td class="font-weight-bold"></td>
								<td align="right" class="pa-1">
									<v-btn color="celeste" dark small @click="verResumen(item)"><v-icon>visibility</v-icon></v-btn> 
								</td></tr>
							</tbody>
						</template>
					</v-simple-table> -->
				</v-card>
			</v-col>

			<v-dialog v-model="Historial" width="500px">
					<v-card class="pa-0 ">
						<v-card-title class="subtitle-2 "> RESULTADO DEL COMPROMISO {{ resultados.id }} </v-card-title>
						<!-- <v-divider class="black"></v-divider> -->
						<v-card-subtitle class="caption mt-3 font-weight-black ">FECHA CIERRE: <br> 
							{{  moment(resultados.fecha_cierre).format('LL') }}  
						</v-card-subtitle> 
						<v-card-subtitle class="caption font-weight-black ">     HORA CIERRE:  <br> 
							{{ resultados.hora_cierre >='12:00'? resultados.hora_cierre +' '+'pm': resultados.hora_cierre+ ' '+'am' }}
						</v-card-subtitle>
						<v-divider class="gris"></v-divider>
						<v-alert text dense	color="green"	icon="mdi-clock-fast" v-if="!resultados.obs_usuario">	SE GENERÓ UNA SOLICITUD </v-alert>
						<v-card-text class="caption font-weight-black  mt-2" v-else> {{ resultados.obs_usuario }} </v-card-text>
						<v-card-actions>
							<v-btn small dark block color="celeste" @click="Historial = false">Cerrar</v-btn> 
						</v-card-actions>
					</v-card>
				</v-dialog>

			<v-col cols="12"  v-if="!filterCompromisos.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No se ha encontrado un historial de compromisos.</v-col>
						<!-- <v-col class="shrink">
							<v-btn fab small color="celeste" dark @click="consultar"> <v-icon> refresh </v-icon> </v-btn>
						</v-col> -->
					</v-row>
				</v-alert>
			</v-col>
		</v-row>
	</v-main>
</template>

<script>
		var moment = require('moment'); moment.locale('es') /// inciar Moment 
	import {mapGetters, mapActions} from 'vuex';
	import metodos from '@/mixins/metodos.js'
  import loading from '@/components/loading.vue'

	export default {
		mixins:[metodos],
		components: { 
			loading
		}, 
		data(){
			return{
				search: '',
				dialog: false,
				param: 0,
				edit:'',

				expanded: [],
        singleExpand: false,

				Historial: false,
				resumen: [],
				fase_ventas:['Prospecto','Por Cotizar','Cotizado','Recotizar','Producción','Por Entregar','Entrega Rechazada','Proyecto Cerrado'],

				Compromisos: [],
				hfases: [	{ text: 'fase venta'  , align: 'left'	 , value: 'fase_venta' },
									{ text: 'fecha'   		, align: 'left'	 , value: 'fecha' },
									{ text: 'hora'   		  , align: 'left'	 , value: 'hora' },
									{ text: ''						, value: 'data-table-expand' },
									{ text: ''						, value: 'actions' }
								],

				// MANEJO DE FECHAS
				fecha1: '',
				fechamodal1:false,
				fecha2: '',
				fechamodal2:false,

				resultados:{}
			}
		},

		created(){
			if(!this.getUsuarios.id){ this.Salir() }
			// ASIGNAR FECHA
			this.fecha1 = this.traerMesActual().fechaInicial;
			this.fecha2 = this.traerMesActual().fechaFinal;
			this.init(); // LLENAR COMPROMISOS
 
		},

		watch:{
			fechamodal1(){
				this.init(); 
			},
			fechamodal2(){
				this.init(); 
			}
			// fecha: function(){
			// 	const payload = { id_vendedor: this.getUsuarios.id, fecha1: this.fecha1, fecha2:this.fecha2}
			// 	this.busxFecha(payload)
			// }
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getCompromisosHechos']), // IMPORTANDO USO DE VUEX 
      ...mapGetters('Usuarios',['getUsuarios']),
      ...mapGetters('Usuarios',['Salir']),

			filterCompromisos: function(){
				return this.getCompromisosHechos.filter((comp)=>{
					var data = comp.nomcli.toLowerCase(); // BUSCAR POR PROVEDOR
					return data.match(this.search.toLowerCase())
				})
			},
		},

		filters:{
    	toUppercase(value){
      	return value.toUpperCase();
    	}
  	},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisoshechos']), // IMPORTANDO USO DE VUEX
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),


			init(){ // CONSULTAR COMPROMISOS
				const parametros = new Object();
							parametros.id = this.getUsuarios.id;
							parametros.fecha1 = this.fecha1;
							parametros.fecha2 = this.fecha2;

				this.consultaCompromisoshechos(parametros);
				this.notificaciones();
			},

			notificaciones(){
				this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
        this.consultaAutorizados(this.getUsuarios.id); 
				const autorizadas = {
					id_usuario: this.getUsuarios.id,	
					fecha     : this.traerFechaActual(),
				}
        this.consultaAutorizados(autorizadas); // traer los pendientes por validar
			},

			verResumen(item){
				this.resultados = item;
				this.Historial = true;
				
			},

		}
	}
</script>

<style scoped>

</style>