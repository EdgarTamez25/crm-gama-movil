<template>
  <v-card class="pa-0 py-0" flat transparent >
		
		<v-btn color="celeste" style="display: none" class="ir-arriba white--text" small fab fixed bottom >
			<v-icon top>keyboard_arrow_up</v-icon>
		</v-btn>

		<v-row justify="center" >
			<v-col cols="12" class="text-center py-1">
				<span class="font-weight-bold">SEGUIMIENTO DE SOLICITUDES </span>
			</v-col>
			<v-col cols="6"  > <!-- FECHA DE COMPROMISO -->
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

			<v-col cols="6"  > <!-- FECHA DE COMPROMISO -->
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
			<v-col cols="12" class="py-0" >
				<v-btn color="gris" dark block @click="init()">RECARGAR VISTA</v-btn>
			</v-col>
		</v-row>
		
		<v-container style="height: 400px;" v-if="Loading">
      <loading/>
    </v-container>
	
		<v-col cols="12" v-if="!Solicitudes.length && !Loading">
			<v-alert color="error" outlined dark icon="sentiment_very_dissatisfied" >
				No hay Solicitudes  para tí. 
			</v-alert>
		</v-col> 

    <v-card  flat v-for="(item, i) in Solicitudes" :key ="i"  >
      <v-btn  dark absolute bottom color="pink" right fab small  @click="consultaDetalle(item)" v-if="!item.show"> 
				<v-icon> expand_more </v-icon>
			</v-btn>
			<v-btn  dark absolute bottom color="pink" right fab small  @click="item.show = false" v-if="item.show" > 
				<v-icon> expand_less </v-icon>
			</v-btn>
			<v-card  class="mt-6 pa-1 elevation-10"  :color="item.estatus === 4? colores[item.estatus-1]:'white'">
				<v-card flat>
					<v-card-text class="py-1"><span class="font-weight-bold"> SOLICITUD:</span> {{ item.id }}  </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> CLIENTE:  </span> {{ item.nomcli }}  </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> FECHA:    </span> {{  moment(item.fecha).format('LL') }}   </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> HORA:     </span> {{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }} </v-card-text>
					<v-divider></v-divider>
					<v-card-actions class="pa-4">
						 <v-progress-circular
							:rotate="270"
							:size="100"
							:width="15"
							:value="porcentaje[item.estatus-1]"
							:color="!item.procesado ? colores[item.estatus-1] : 'green'"
						>
							 {{ porcentaje[item.estatus-1]}} %
						</v-progress-circular>
						<v-spacer></v-spacer>
						<span class="font-weight-bold"> ESTATUS<br> 
							<span class="gris--text"    v-if="item.estatus===1 && !item.procesado"> {{ estatus1[item.estatus-1] }} </span>
							<span class="celeste--text" v-if="item.estatus===2 && !item.procesado"> {{ estatus1[item.estatus-1] }} </span>
							<span class="rosa--text"    v-if="item.estatus===3 && !item.procesado"> {{ estatus1[item.estatus-1] }} </span>
							<span class="error--text"   v-if="item.estatus===4 && !item.procesado"> {{ estatus1[item.estatus-1] }} </span>
							<span class="green--text"   v-if="item.estatus===3 && item.procesado"> PROGRAMADO </span>
						</span>
					</v-card-actions>
				</v-card>

			  <v-expand-transition >
					<div v-show="item.show" >
						<v-card class="ma-1 pa-1 mt-1" v-if="!item.detalle.length">
							<v-alert text dense	color="warning" >	NO HAY PARTIDAS REGISTRADAS </v-alert>
						</v-card>
						<v-card outlined class="ma-1 pa-1 mt-1" v-for="(data, x) in item.detalle" :key ="x"  >
							<div class="d-flex flex-no-wrap justify-space-between">
								<v-btn  text  height="40px" class="font-weight-black caption"> {{ data.codigo }}</v-btn >
								<div v-if="!item.procesado">
									<v-btn :color="colores2[data.estatus]" text dark height="40px" class="caption"> 
										 {{ estatus2[parseInt(data.estatus)] }} 
									</v-btn >
								</div>
								<div v-else>
									<v-btn color="green" text dark height="40px" class="caption"> 
										 PROGRAMADO
									</v-btn >
								</div>
								
							</div>
						</v-card>
					</div>

				</v-expand-transition> 
			</v-card>
    </v-card>

		<v-card height="50" class="mt-5" flat transparent> </v-card>
    
  </v-card>
	
</template>

<script>

	import {mapGetters, mapActions} from 'vuex';
	import $ from 'jquery'
	var moment = require('moment'); moment.locale('es') /// inciar Moment 
	import metodos from '@/mixins/metodos.js'
  import loading from '@/components/loading.vue'

	export default {
		mixins:[metodos],
		components: {
      loading
			// overlay
		},
		data(){
			return{
				estatus1:['EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR','CANCELADO'],
				estatus2:['PENDIENTE','EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR'],

				porcentaje:['33','66','100','0'],
				colores:['gris','celeste','rosa','red darken-4'],
				colores2:['error','gris','celeste','rosa'],

				search: '',

				Loading: false,
				Solicitudes:[],
				detalle: [],
				arrayTemp:[],
				// MANEJO DE FECHAS
				fecha1: '',
				fechamodal1:false,
				fecha2: '',
				fechamodal2:false,
			}
		},

		created(){
			this.fecha1 = this.traerMesActual().fechaInicial;
			this.fecha2 = this.traerMesActual().fechaFinal;
			this.init();
			// this.consultaSeguimiento(this.getUsuarios.id);
		},

		watch:{
			fecha1(){
				this.init();
			},
			fecha2(){
				this.init();
			}
		},

		computed:{
			...mapGetters('Usuarios',['getUsuarios']),
		},

		methods:{
			...mapActions('Solicitudes'  ,['consultaSolicitudes']), // IMPORTANDO USO DE VUEX
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar']),

			init(){
        this.consultaPendientesxValidar(this.getUsuarios.id); // traer los pendientes por validar
				
				this.Solicitudes = [];   this.Loading = true;
				const parametros = new Object();
							parametros.id_usuario = this.getUsuarios.id;
							parametros.fecha1     = this.fecha1;
							parametros.fecha2     = this.fecha2;
				
				this.consultaSolicitudes(parametros).then( res =>{
					this.Solicitudes = [];
					for(let i=0;i< res.length;i++){

						this.Solicitudes.push({
							"id"  				: res[i].id,
							"id_cliente"  : res[i].id_cliente,
							"nomcli"      : res[i].nomcli,
							"fecha"       : res[i].fecha,
							"hora"        : res[i].hora,
							"id_usuario"  : res[i].id_usuario,
							"nomusuario"  : res[i].nomusuario,
							"estatus"     : res[i].estatus,
							"nota"        : res[i].nota,
							"procesado"   : res[i].procesado,
							"detalle"     : [],
							"show"        : false
						})
					}
				}).finally(()=>{ this.Loading= false });
			},

			consultaDetalle(data){
				this.$http.get('detalle.solicitud/'+ data.id ).then(response =>{ 
					this.Solicitudes = this.Solicitudes.map( item =>{
						if(item.id === data.id){
							item.detalle = response.body
							item.show    = true
						}
						return item;
					})
				})
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