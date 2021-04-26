<template>
  <v-card class="pa-0 py-0" flat transparent >
		
		<v-btn color="celeste" style="display: none" class="ir-arriba white--text" small fab fixed bottom >
			<v-icon top>keyboard_arrow_up</v-icon>
		</v-btn>

		<v-row justify="center" >
			<v-col cols="12" class="text-center py-1">
				<span class="font-weight-bold"> SOLICITUDES REALIZADAS </span>
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

			<v-col cols="12" v-if="!Solicitudes.length && !Loading">
				<v-alert color="error" outlined dark icon="sentiment_very_dissatisfied" >
					No hay Solicitudes  para tí. 
				</v-alert>
			</v-col>
		</v-row>
		
		<v-container style="height: 400px;" v-if="Loading">
      <loading/>
    </v-container>

    <v-card  flat v-for="(item, i) in Solicitudes" :key ="i" >
      <v-btn  dark absolute bottom :color="colores[item.estatus]" right fab small @click="verDetalleSol(item)" > 
				<v-icon> mdi-eye </v-icon>
			</v-btn>
		
			<v-card  class="mt-6 pa-1 elevation-10" :color="colores[item.estatus]">
				<v-card flat >
					<v-card-text class="py-1"><span class="font-weight-bold"> SOLICITUD:</span> {{ item.id }}  </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> CLIENTE:  </span> {{ item.nomcli }}  </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> FECHA:    </span> {{  moment(item.fecha).format('LL') }}   </v-card-text>
					<v-card-text class="py-1"><span class="font-weight-bold"> HORA:     </span> {{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }} </v-card-text>
					<v-card-text class="py-1 font-weight-black">
            <span :class="[`${colores[item.estatus]}--text`]"> {{ estatus1[item.estatus-1] }} </span> </v-card-text>
          <v-card-actions class="pa-1">
					</v-card-actions>
				</v-card>
			</v-card>
    </v-card>

		<v-card height="50" class="mt-5" flat transparent> </v-card>
    
  </v-card>
	
</template>

<script>
	var moment = require('moment'); moment.locale('es') /// inciar Moment 
	import $ from 'jquery'
	import {mapGetters, mapActions} from 'vuex';
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
				estatus1:['EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR'],
				estatus2:['PENDIENTE','EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR'],

				porcentaje:['33','66','100'],
				colores:['error','gris','celeste','rosa','red darken-4'],

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
      filterCompromisos(){
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
			...mapActions('Solicitudes'  ,['consultaSolicitudes']), // IMPORTANDO USO DE VUEX

			init(){
				this.Loading = true;
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
						})
					}
				}).finally(()=>{ this.Loading= false });
			},

			verDetalleSol(item){
				this.$router.push({name: 'det_solicitud', params:{ detalle:item }})
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