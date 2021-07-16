<template>
  <v-card class="pa-0 py-0" flat transparent >
		
		<v-btn color="celeste" style="display: none" class="ir-arriba white--text" small fab fixed bottom >
			<v-icon top>keyboard_arrow_up</v-icon>
		</v-btn>

		<v-row justify="center" >
			<v-col cols="10" sm="10" md="7" lg="6" xl="4">
				<v-text-field
						label="Buscar por cliente"
						dense
						outlined
						hide-details
						v-model="search"
						append-icon="search"
						color="rosa"
				></v-text-field>
			</v-col>
			<v-col cols="1" class="mr-6">
					<v-btn class="gris" icon dark @click="consultaSeguimiento(getUsuarios.id)" ><v-icon>refresh</v-icon> </v-btn>
			</v-col>
		</v-row>
		
		<div  class="text-center" v-if="Loading" >  
			<v-progress-circular :size="100" :width="7" color="celeste" indeterminate  v-if="Loading"></v-progress-circular>
		</div>

		<v-col cols="12" sm="9" v-if="!filterArticulos.length && !Loading">
			<v-alert color="error" dark icon="sentiment_very_dissatisfied" border="right" prominent >
				No se encontro el cliente.
			</v-alert>
		</v-col>

    <v-card  flat v-for="(item, i) in filterArticulos" :key ="i" >
      <v-btn  dark absolute bottom color="pink" right fab x-small  @click="item.show = !item.show" > 
				<v-icon> {{ !item.show?'expand_more': 'expand_less' }} </v-icon>
			</v-btn>
			<v-card  class="mt-5 elevation-10" >
				<v-card flat>
					<v-card-text class="font-weight-bold">CLIENTE: {{ item.nomcli }}  </v-card-text>
					<v-divider></v-divider>
					<v-card-actions class="pa-4">
						 <v-progress-circular
							:rotate="270"
							:size="100"
							:width="15"
							:value="porcentaje[item.fase_venta-1]"
							:color="colores[item.fase_venta-1]"
						>
							 {{ porcentaje[item.fase_venta-1]}} %
						</v-progress-circular>
						<v-spacer></v-spacer>
						<span class="font-weight-bold"> FASE DE VENTA <br> 
							<span class="green--text"  v-if="item.fase_venta===1"> {{ fases[item.fase_venta-1] }} </span>
							<span class="orange--text" v-if="item.fase_venta===2"> {{ fases[item.fase_venta-1] }} </span>
							<span class="orange--text" v-if="item.fase_venta===3"> {{ fases[item.fase_venta-1] }} </span>
							<span class="red--text" 	 v-if="item.fase_venta===4"> {{ fases[item.fase_venta-1] }} </span>
							<span class="blue--text" v-if="item.fase_venta===5"> {{ fases[item.fase_venta-1] }} </span>
							<span class="morado--text" v-if="item.fase_venta===6"> {{ fases[item.fase_venta-1] }} </span>
							<span class="red--text" 	 v-if="item.fase_venta===7"> {{ fases[item.fase_venta-1] }} </span>
							<span class="rosa--text" 	 v-if="item.fase_venta===8"> {{ fases[item.fase_venta-1] }} </span>
						</span>
					</v-card-actions>
				</v-card>
				<v-expand-transition >
					<div v-show="item.show" >
						<v-divider></v-divider>
						<v-card-text class="py-0" >
							<v-timeline align-top dense >
								<v-timeline-item :color="colores[item.fase_venta-1]" small v-for="(data, x) in filterArticulos[i].historial" :key ="x">
									<v-row no-gutters>
										<v-col cols="12">
												<strong>{{ fases[data.fase_venta-1] }}</strong>
										</v-col>
										<v-col cols="12">
											<v-card-actions >
												<strong class="celeste--text"> {{ data.hora }} {{data.hora >= 12? 'a.m.':'p.m.' }}</strong>
												<v-spacer></v-spacer>
												<strong class="celeste--text"> {{ data.fecha}}</strong>
											</v-card-actions>
										</v-col>
										<v-col>
											<div class="caption mx-2" align="left" >
												{{ data.obscierre }}
											</div>
										</v-col>
									</v-row>
								</v-timeline-item>
								
							</v-timeline>
						</v-card-text>
					</div>
				</v-expand-transition>
			</v-card>
    </v-card>

		<v-card height="50" class="mt-5" flat transparent>
    
  </v-card>
    
  </v-card>
	
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	import $ from 'jquery'
	var moment = require('moment'); 
	export default {
		data(){
			return{
				fases:['PROSPECTO','POR COTIZAR','PROYECTO COTIZADO','POR RECOTIZAR','EN PRODUCCIÓN','POR ENTREGAR','ENTREGA RECHAZADA','PROYECTO CERRADO'],
				porcentaje:['15','25','40','25','60','80','60','100'],
				colores:['green','green','orange','red','blue','orange','red','rosa'],
				search: '',
			}
		},

		created(){
			moment.locale('es') /// inciar Moment 
			this.init();
		},

		watch:{
			
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getSeguimiento']), // IMPORTANDO USO DE VUEX 
			...mapGetters('Usuarios',['getUsuarios']),
			
			filterArticulos: function(){
				return this.getSeguimiento.filter((fase)=>{
					var data = fase.nomcli.toLowerCase(); // BUSCAR POR PROVEDOR
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
			...mapActions('Compromisos'    ,['consultaSeguimiento']), // IMPORTANDO USO DE VUEX
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),

			init(){
				this.consultaSeguimiento(this.getUsuarios.id);
				this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
				const payload = new Object({
					fecha: this.traerFechaActual(),
					id_usuario: this.getUsuarios.id,
				});
        this.consultaAutorizados(payload);
			}
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