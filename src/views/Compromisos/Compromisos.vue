<template>
	<v-main class="pa-0">
		<v-btn color="rosa" style="display: none" class="ir-arriba white--text" small fab fixed bottom >
			<v-icon top>keyboard_arrow_up</v-icon>
		</v-btn>
		<v-row no-gutters>
			<v-col cols="12" class="text-center my-1">
				<span class="font-weight-bold ">COMPROMISOS PENDIENTES</span>
			</v-col>

			<!-- CONTROLADOR DE FECHA  -->
			<!-- <v-col cols="7" > 
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
			</v-col> -->

			<v-col cols="8" class="pa-1" align="right"> <!-- BOTON DE RECARGA -->
				<v-btn block class="rosa"  dark @click="nuevoCompromiso(1,'')" ><v-icon large>chrome_reader_mode</v-icon> </v-btn>
			</v-col>
			<v-col cols="4" class="pa-1" align="left">
				<v-btn block class="gris"  dark @click="consultar()" ><v-icon>refresh</v-icon> </v-btn>
			</v-col>

			<v-col cols="12" class="mt-1 pa-1">
				<v-text-field
					v-model="search" label="Buscar por cliente" dense outlined hide-details  append-icon="search"	color="celeste">
				</v-text-field>
			</v-col>

			<v-container style="height: 400px;" v-if="Loading">
				<loading/>
			</v-container>

				<v-col cols="12">
				<v-card v-for="(item, i) in filterCompromisos" :key ="i" class="py-0" color="celeste">
						<v-btn  dark absolute bottom :color="colores[item.estatus]" right fab small @click="editar(item)" > 
							<v-icon> mdi-eye </v-icon>
						</v-btn>
						
						<v-card  class="mt-6 pa-1 elevation-10" :color="colores[item.estatus]" >
							<v-card flat>
								<v-card-text class="py-1"><span class="font-weight-bold"> COMPROMISO:  </span> {{ item.id }}  </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> CLIENTE:  </span> {{ item.nomcli }}  </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> FECHA:    </span> {{  moment(item.fecha).format('LL') }}   </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> HORA:     </span> {{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }} </v-card-text>
								<v-card-text class="py-1"><span class="font-weight-bold"> CATEGORÍA:</span> {{ item.nomcatego }}  </v-card-text>

								<v-card-actions class="pa-1">

								</v-card-actions>
							</v-card>
						</v-card>
				</v-card>
			</v-col>

			<v-col cols="12" class="mt-3"  v-if="!getCompromisos.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No hay compromisos pendientes. <br> Actualice la vista.</v-col>
						<v-col class="shrink">
							<!-- <v-btn fab small color="celeste" dark :to="{name:'pendientes'}"> <v-icon> calendar_today </v-icon> </v-btn> -->
							<v-btn fab small color="celeste" dark @click="consultar()"> <v-icon> refresh </v-icon> </v-btn>
						</v-col>
					</v-row>
				</v-alert>
			</v-col>

			<!-- NUEVO COMPROMISO -->
			<v-dialog persistent v-model="compromisoModal" width="700px" >	
				<v-card class="pa-4">
					<ControlCompromiso :param="param" :edit="edit" @modal="compromisoModal = $event" />
				</v-card>
			</v-dialog>
		
		</v-row>
	</v-main>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	import metodos from '@/mixins/metodos.js'
	import ControlCompromiso from '@/views/Compromisos/ControlCompromiso.vue'
  import loading from '@/components/loading.vue'
	import $ from 'jquery'

	export default {
		mixins:[metodos],
		components: { 
			ControlCompromiso,
			loading
		}, 
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
				// 0 = PENDIENTE , 1 = SOL.COTIZACION , 2= SOL.FT, 3 = LISTO , 4 = CANCELADO, 
				colores:['celeste','orange darken-1','purple' ,'success','error'],

				// MANEJO DE FECHAS
				fecha: '',
				fechaModal: false,
			  // HORA
				hora 					    : null,
        horamodal			    : false,
				hora_compromiso   : false,

				fechacomp				  : false,
				fechamodal2 			: false,
				fecha_compromiso  : false,
			}
		},

		created(){
			// ASIGNAR FECHA
			if(!this.getUsuarios.id){ this.Salir() }
			this.fecha = this.traerFechaActual();
			// this.fechacomp = this.traerFechaActual();
			// LLENAR COMPROMISOS
			this.consultar();
		},

		watch:{
			// fecha: function(){
			// 	const payload = { id_vendedor: this.getUsuarios.id , fecha: this.fecha}
			// 	this.consultaCompromisos(payload)
			// }
		},

		filters:{
    	toUppercase(value){
      	return value.toUpperCase();
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

			filterCompromisos: function(){
				return this.getCompromisos.filter((comp)=>{
					var data = comp.nomcli.toLowerCase(); // BUSCAR POR PROVEDOR
					return data.match(this.search.toLowerCase())
				})
			},
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']), // IMPORTANDO USO DE VUEX
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),


			consultar(){ // CONSULTAR COMPROMISOS
        this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
        const autorizadas = new Object({
					fecha     : this.traerFechaActual(),
					id_usuario: this.getUsuarios.id,
				});
        this.consultaAutorizados(autorizadas);

				const payload = new Object({ id_vendedor: this.getUsuarios.id, fecha: this.traerFechaActual() })
				this.consultaCompromisos(payload)
			},

			nuevoCompromiso(action, items){
				this.param = action;
				this.edit = items;
				this.compromisoModal = true;
			},

			editar(item){  // VER DETALLE DEL COMPROMISO}
				// console.log('DETALLE',  item)
				this.$router.push({name: 'det_compromiso', params:{ detalle:item }})
			},
			
		},

		mounted(){
			// Jquey para activar la animacion del boton hacia arriba
			$(document).ready(function(){

				$('.ir-arriba').click(function(){
					$('body, html').animate({
						scrollTop: '0px'
					}, 300);
				});
				
				$(window).scroll(function(){
					if( $(this).scrollTop() > 0 ){
						$('.ir-arriba').slideDown(300);
					} else {
						$('.ir-arriba').slideUp(300);
					}
				});
			});
  	},


	}
</script>
