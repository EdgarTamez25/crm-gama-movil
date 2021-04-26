<template>
  <v-card class="pa-0 py-0" flat transparent >
		
		<v-btn color="celeste" style="display: none" class="ir-arriba white--text" small fab fixed bottom >
			<v-icon top>keyboard_arrow_up</v-icon>
		</v-btn>

		<v-row justify="center" >
			<v-col cols="12" class="text-center py-1">
				<span class=" font-weight-bold">ORDENES DE TRABAJO </span>
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
		</v-row>

    <v-container style="height: 400px;" v-if="Loading">
      <loading/>
    </v-container>


		<v-col cols="12" v-if="!OT.length && !Loading">
			<v-alert prominent text color="rosa">
				<v-row align="center">
					<v-col class="grow">No hay ordenes de trabajo para ti.</v-col>
					<v-col class="shrink">
						<v-btn fab small color="celeste" dark @click="init()"> <v-icon> refresh </v-icon> </v-btn>
					</v-col>
				</v-row>
			</v-alert>
		</v-col> 

    <v-card  flat v-for="(item, i) in OT" :key ="i"  >
      <v-btn  dark absolute bottom color="pink" class="boton" right fab small  @click="consultaDetalle(item)" v-if="!item.show"> 
				<v-icon> expand_more </v-icon>
			</v-btn>
			<v-btn  dark absolute bottom color="pink" right fab small  @click="item.show = false" v-if="item.show" > 
				<v-icon> expand_less </v-icon>
			</v-btn>

			<v-card  class="mt-6 pa-1 elevation-10" >
				<v-card flat>
					<v-card-title class=" py-1 font-weight-bold"> {{ item.depto }}  </v-card-title>
					<v-divider></v-divider>
					<v-card-text class="py-1"> <span class="font-weight-bold">  ORDEN DE TRABAJO: </span>  {{ item.id }}  																										 </v-card-text>
					<v-card-text class="py-1"> <span class="font-weight-bold">  CLIENTE:          </span>  {{ item.nomcli }}  																							   </v-card-text>
					<v-card-text class="py-1"> <span class="font-weight-bold">  OC:               </span>  {{ item.oc }}  																							   </v-card-text>
					<v-card-text class="py-1"> <span class="font-weight-bold">  REFERENCIA:       </span>  {{ item.referencia }}  																							   </v-card-text>
					<v-card-text class="py-1"> <span class="font-weight-bold">  FECHA:            </span>  {{  moment(item.fecha).format('LL') }}   													 </v-card-text>
					<v-card-text class="py-1"> <span class="font-weight-bold">  HORA: 						</span>  {{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }} </v-card-text>
					<v-card-actions class="pa-1"/>
				</v-card>

			  <v-expand-transition >
					<div v-show="item.show" >
						<v-card outlined class="ma-1 pa-1 mt-1" v-for="(data, x) in item.detalle" :key ="x" style="border-color: #0096cb;" >
							<div class="d-flex flex-no-wrap justify-space-between">
								<v-btn  text  height="40px" class="font-weight-black caption"> {{ data.codigo }} </v-btn>
								<div>
									<v-btn :color="colores2[data.estatus]" text dark height="40px" class="caption"> 
										 {{ estatus2[parseInt(data.estatus)] }} 
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
  // import overlay     from '@/components/overlay.vue'

	export default {
    mixins:[metodos],
    components: {
      loading
			// overlay
		},
		data(){
			return{
				estatus1:['EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR'],
				estatus2:['','PENDIENTE','EN CALL-CENTER','EN DESARROLLO','A PROGRAMAR'],

				porcentaje:['33','66','100'],
				colores:['gris','celeste','rosa'],
				colores2:['error','gris','celeste','rosa'],

				search: '',

				Loading: false,
				OT:[],
				detalle: [],
				arrayTemp:[],

				deptos:[],
				// MANEJO DE FECHAS
				fecha1: '',
				fechamodal1:false,
				fecha2: '',
				fechamodal2:false,
			}
		},

		created(){
			this.consultaDeptos();
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
			...mapActions('OT'  ,['consultaOT']), // IMPORTANDO USO DE VUEX

			init(){
				this.Loading = true;
				const payload = new Object();
							payload.id_vendedor = this.getUsuarios.id;
							payload.fecha1     = this.fecha1;
              payload.fecha2     = this.fecha2;
              payload.estatus    = 1;
        
        this.$http.post('ordenes.trabajo.vend',payload).then(res =>{
					this.OT = [];
					for(let i=0;i< res.body.length;i++){

						this.OT.push({
							"id"  				: res.body[i].id,
							"id_depto"    : res.body[i].id_depto,
							"depto"				: this.buscaDeptos(res.body[i].id_depto),
							"id_vendedor" : res.body[i].id_vendedor,
							"id_cliente"  : res.body[i].id_cliente,
							"nomcli"      : res.body[i].nomcli,
							"oc"  				: res.body[i].oc,
							"referencia"  : res.body[i].referencia,
							"fecha"     	: res.body[i].fecha,
							"hora"        : res.body[i].hora,
							"estatus"     : res.body[i].estatus,
							"detalle"     : [],
							"show"        : false
						})
					}

        }).catch(error =>{
          console.log('err', error)
        })
        .finally(()=>{ this.Loading= false });
			},

			consultaDetalle(data){
				this.$http.get('detalle.ot/'+ data.id ).then(response =>{ 
					this.OT = this.OT.map( item =>{
						if(item.id === data.id){
							item.detalle = response.body
							item.show    = true
						}
						return item;
					})
				})
			},

			buscaDeptos(id){
				let nombre = '';
				this.deptos.filter(item =>{ if(item.id === id){ nombre = item.nombre 	}});
				return nombre;
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


	
