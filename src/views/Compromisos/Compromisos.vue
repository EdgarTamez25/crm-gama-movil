<template>
	<v-content class="pa-0">
		<v-row no-gutters>
			<v-col cols="12" class="text-center my-1">
				<span class="font-weight-bold ">COMPROMISOS</span>
			</v-col>

			<v-col cols="7" > <!-- CONTROLADOR DE FECHA  -->
				<v-dialog ref="fechaCompromiso" v-model="fechaModal" :return-value.sync="fecha" persistent 	width="290px"	>
					<template v-slot:activator="{ on }">
						<v-text-field
							v-model="fecha" label="Fecha de compromiso" append-icon="event" readonly 	v-on="on"
							outlined 	dense color="rosa" 
						></v-text-field>
					</template>

					<v-date-picker v-model="fecha" locale="es-es" color="celeste" scrollable>
						<v-spacer></v-spacer>
						<v-btn text color="gris" @click="fechaModal = false">Cancel</v-btn>
						<v-btn dark color="celeste" @click="$refs.fechaCompromiso.save(fecha)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="2" class="mx-1" align="right"> <!-- BOTON DE RECARGA -->
				<v-btn  class="rosa ma-1" icon dark @click="nuevoCompromiso(1,'')" ><v-icon>chrome_reader_mode</v-icon> </v-btn>
			</v-col>
			<v-col cols="2" class="mx-1" align="left">
				<v-btn  class="gris ma-1" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn>
			</v-col>

			<v-container fluid v-if="Loading">
				<v-row align="center" justify="center" style="height: 300px;">
					<v-col cols="12" class="text-center"  >  <!-- PROGRES -->
						<v-progress-circular :size="100" :width="7" color="celeste" indeterminate ></v-progress-circular>
					</v-col>	
				</v-row>
			</v-container>	

			<v-col cols="12" v-if="getCompromisos.length"> <!-- TABLA DE COMPROMISOS -->
				<v-card outlined >
					<v-data-table 
					:headers="headers"
					:items="getCompromisos"
					:height="tamanioPantalla"
					hide-default-footer
					hide-default-header
					disable-pagination
					:loading ="Loading"
					loading-text="Cargando... Por favor espere."
				>
					<template v-slot:item.tipo_compromiso="{ item }" > 
						{{ item.tipo_compromiso === 1? 'Interno': 'Externo'}}
					</template>

					<template v-slot:item.cumplimiento="{ item }" >  
							{{ item.cumplimiento === 0? 'Sin realizar': 'Cumplido'}} 
					</template>

					<template v-slot:item.action="{ item }" > 
						<!-- <v-btn class="celeste" dark @click="abrirModal(1, item)"><v-icon> airport_shuttle </v-icon></v-btn> FORMAR RUTA  -->
						<v-btn class="success" dark @click="editar(item)"><v-icon> remove_red_eye </v-icon></v-btn> <!-- DETALLE -->
					</template>

				</v-data-table>
				</v-card>
			</v-col>

			<v-col cols="12"  v-if="!getCompromisos.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No hay compromisos pendientes. <br> Actualice la vista o revise sus proximos compromisos.</v-col>
						<v-col class="shrink">
							<v-btn fab small color="celeste" dark :to="{name:'pendientes'}"> <v-icon> calendar_today </v-icon> </v-btn>
						</v-col>
					</v-row>
				</v-alert>
			</v-col>

			<!-- NUEVO COMPROMISO -->
			<v-dialog persistent v-model="compromisoModal" width="700px" >	
				<v-card>
					<ControlCompromiso :param="param" :edit="edit" @modal="compromisoModal = $event" />
				</v-card>
			</v-dialog>
		
		</v-row>
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	import metodos from '@/mixins/metodos.js'
	import ControlCompromiso from '@/views/Compromisos/ControlCompromiso.vue'

	export default {
		mixins:[metodos],
		components: { ControlCompromiso }, //formaRuta
		data(){
			return{
				search: '',
				dialog: false,
				param: 0,
				edit:'',

				compromisoModal: false,

				headers: [
								{ text: '#'								, align: 'left'	 , value: 'id' },
								{ text: 'Categoria'					, align: 'left'	 , value: 'nomcatego' },
								{ text: 'Cliente'					  , align: 'left'	 , value: 'nomcli' },
								{ text: 'Hora'							, align: 'left'	 , value: 'hora' },
								{ text: '', value: 'action' , sortable: false },
							],
				Compromisos: [],

				// MANEJO DE FECHAS
				fecha: '',
				fechaModal: false,
				prueba:{ id: 1, nombre:'Edgar',apellido:'Tamez'},

					// HORA
				hora 					 : null,
        horamodal			 : false,
				hora_compromiso: false,

				fechacomp				: new Date().toISOString().substr(0, 10),
				fechamodal2 			: false,
				fecha_compromiso: false,
			}
		},

		created(){
			// ASIGNAR FECHA
			if(!this.getUsuarios.id){ this.Salir() }
			
			this.fecha = this.traerFechaActual();
			this.fechacomp = this.traerFechaActual();
			// LLENAR COMPROMISOS
			this.consultar();

			const objectData = Object.values(this.prueba);
		},

		watch:{
			fecha: function(){
				const payload = { id_vendedor: this.getUsuarios.id , fecha: this.fecha}
				this.consultaCompromisos(payload)
			}
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getCompromisos']), // IMPORTANDO USO DE VUEX 
			...mapGetters('Usuarios',['getUsuarios']),
			tamanioPantalla () {
				switch (this.$vuetify.breakpoint.name) {
					case 'xs':
						return this.$vuetify.breakpoint.height -200
					break;
					case 'sm': 
						return this.$vuetify.breakpoint.height -200
					break;
					case 'md':
						return this.$vuetify.breakpoint.height -200
					break;
					case 'lg':
						return this.$vuetify.breakpoint.height -200
					break;
					case 'xl':
						return this.$vuetify.breakpoint.height -200
					break;
				}
			},
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']), // IMPORTANDO USO DE VUEX
      ...mapGetters('Usuarios',['Salir']),

			consultar(){ // CONSULTAR COMPROMISOS
				var me = this;
				const payload = { id_vendedor: this.getUsuarios.id , fecha: this.fecha } // FORMO ARRAY CON id DEL VENDEDOR LOGEADO Y FECHA ACTUAL
				this.consultaCompromisos(payload)
			},

			nuevoCompromiso(action, items){
				this.param = action;
				this.edit = items;
				this.compromisoModal = true;
			},

			editar(item){  // VER DETALLE DEL COMPROMISO}
				console.log('DETALLE',  item)
				this.$router.push({name: 'det_compromiso', params:{ detalle:item }})
			},
		}


	}
</script>
