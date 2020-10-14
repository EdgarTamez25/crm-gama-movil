<template>
	<v-content class="pa-0">
		<v-row no-gutters>
			<v-col cols="12" class="text-center my-2">
				<span class="font-weight-bold mt-3">COMPROMISOS REALIZADOS </span>
			</v-col>

			<v-col cols="5" sm="5" xl="2" class=" mx-1" > <!-- FECHA DE COMPROMISO -->
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

			<v-col cols="5" sm="5" xl="2" class=" mx-1" > <!-- FECHA DE COMPROMISO -->
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

			<v-col cols="1"  class="mx-1">
				<v-btn color="rosa" fab small dark @click="consultar()"><v-icon>search</v-icon> </v-btn>
			</v-col>	

			<v-col cols="12" class="mx-1 my-2">
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

			<v-container fluid v-if="Loading">
				<v-row align="center" justify="center" style="height: 300px;">
					<v-col cols="12" class="text-center"  >  <!-- PROGRES -->
						<v-progress-circular :size="100" :width="7" color="celeste" indeterminate ></v-progress-circular>
					</v-col>	
				</v-row>
			</v-container>

			<v-col cols="12">
				<v-card v-for="(item, i) in filterCompromisos" :key ="i" class="mt-2" color="celeste">
					<v-simple-table dense flat>
						<template v-slot:default>
							<tbody>
								<tr> <td class="font-weight-bold">Cliente  </td> <td> {{ item.nomcli }}   </td></tr>
								<tr> <td class="font-weight-bold">Fecha		 </td> <td> {{ item.fecha }} 		</td></tr> 
								<tr> <td class="font-weight-bold">Hora		 </td> <td> {{ item.hora }} {{ item.hora <12? 'a.m.':'p.m.'}}		</td></tr> 
								<tr> <td class="font-weight-bold"></td>
								<td align="right" class="pa-1">
									<v-btn color="celeste" dark fab x-small @click="verResumen(item.id)"><v-icon>visibility</v-icon></v-btn> 
								</td></tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			<!-- HISTORIAL DEL COMPROMISO -->
				<v-dialog persistent v-model="Historial" width="650px" >	
		    	<v-card class="pa-0">
						<v-card-actions class="rosa white--text">
							<v-card-title  >Fases del compromiso</v-card-title>
							<v-spacer></v-spacer>
							<v-btn color="white" small @click="Historial=false" text><v-icon>clear</v-icon></v-btn>
						</v-card-actions>

						<v-data-table
							:headers="hfases"
							:items="resumen"
							:single-expand="singleExpand"
							:expanded.sync="expanded"
							item-key="id"
							show-expand
							hide-default-header
							hide-default-footer
							disable-pagination
							calculate-widths
						>
							<template v-slot:item.fase_venta="{ item }">
								<span class="font-weight-bold text-left"> {{ fase_ventas[item.fase_venta-1] }} 
										<span v-if="item.fase_venta === 8" class="overline	"> 
											({{ item.aceptado===1?'Entregado':'Sin entregar'}}) 
										</span>
								</span>
							</template>

							<template v-slot:item.hora="{ item }">
								{{ item.hora >='12:00'? item.hora +' '+'pm': item.hora+ ' '+'am' }}
							</template>

							<template v-slot:expanded-item="{ headers, item }" >
								<td class="celeste white--text " :colspan="headers.length" v-if="item.obscierre">{{ item.obscierre }} </td>
							</template>

							<template v-slot:item.actions="{ item }" >
								<v-btn icon small color="red"><v-icon color="red" v-if="item.obscierre">priority_high</v-icon></v-btn>
							</template>

						</v-data-table>
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
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	var moment = require('moment'); 
	import metodos from '@/mixins/metodos.js'

	export default {
		mixins:[metodos],
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
			}
		},

		created(){
			if(!this.getUsuarios.id){ this.Salir() }
			// ASIGNAR FECHA
			this.fecha1 = this.traerMesActual().fechaInicial;
			this.fecha2 = this.traerMesActual().fechaFinal;

			this.consultar(); // LLENAR COMPROMISOS
			moment.locale('es') /// inciar Moment 
		},

		watch:{
			fecha: function(){
				const payload = { id_vendedor: this.getUsuarios.id, fecha1: this.fecha1, fecha2:this.fecha2}
				this.busxFecha(payload)
			}
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getCompromisosHechos']), // IMPORTANDO USO DE VUEX 
      ...mapGetters('Usuarios',['getUsuarios']),

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
      ...mapGetters('Usuarios',['Salir']),

			consultar(){ // CONSULTAR COMPROMISOS
				var me = this; var id = this.getUsuarios.id
				const payload = { id: this.getUsuarios.id, fecha1: this.fecha1, fecha2:this.fecha2}
				this.consultaCompromisoshechos(payload)
			},

			verResumen(id){
				this.$http.get('resumen.compromiso/'+id).then(response =>{
					this.resumen = [];
					for(let i =0;i< response.body.length;i++){
						this.resumen.push({ fase_venta: response.body[i].fase_venta,
																fecha			: moment(response.body[i].fecha).format('LL'),
																hora 			: response.body[i].hora,
																aceptado	: response.body[i].aceptado,
																id: response.body[i].id,
																obscierre : response.body[i].obscierre
															})
					}
					this.Historial = true
				}).catch(err =>{
					console.log('err', err)
				})
			},

		}
	}
</script>

<style scoped>

</style>