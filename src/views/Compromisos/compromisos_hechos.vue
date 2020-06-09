<template>
	<v-content class="pa-0">
		<v-row no-gutters>
			<v-col cols="12" class="text-center my-2">
				<strong>COMPROMISOS REALIZADO </strong>
			</v-col>

			<v-col cols="10" > <!-- CONTROLADOR DE FECHA  -->
				<v-dialog ref="fechaCompromiso" v-model="fechaModal" :return-value.sync="fecha_compromiso" persistent 	width="290px"	>
					<template v-slot:activator="{ on }">
						<v-text-field
							v-model="fecha_compromiso" label="Fecha de compromiso" append-icon="event" readonly 	v-on="on"
							outlined 	dense color="rosa" 
						></v-text-field>
					</template>

					<v-date-picker v-model="fecha_compromiso" locale="es-es" color="celeste" scrollable>
						<v-spacer></v-spacer>
						<v-btn text color="gris" @click="fechaModal = false">Cancel</v-btn>
						<v-btn dark color="celeste" @click="$refs.fechaCompromiso.save(fecha_compromiso)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="1" class="mx-3"> <!-- BOTON DE RECARGA -->
				<!-- <v-btn  class="gris" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn> -->
				<v-btn  class="gris" icon dark @click="busxFecha" ><v-icon>search</v-icon> </v-btn>
			</v-col>	

			<v-col cols="12" class="text-center" v-if="Loading" >  <!-- PROGRES -->
				<v-progress-circular :size="100" :width="7" color="celeste" indeterminate ></v-progress-circular>
			</v-col>	

			<v-col cols="12">
				<v-card v-for="(item, i) in getCompromisosHechos" :key ="i" class="mt-2" color="celeste">
					<v-simple-table dense flat>
						<template v-slot:default>
							<tbody>
								<tr> <td class="font-weight-bold">Cliente  </td> <td> {{ item.nomcli }}   </td></tr>
								<tr> <td class="font-weight-bold">Catégoria</td> <td> {{ item.nomcatego }}</td></tr>
								<tr> <td class="font-weight-bold">Fecha		 </td> <td> {{ item.fecha }} 		</td></tr> 
								<tr> <td class="font-weight-bold">Hora		 </td> <td> {{ item.hora }} 		</td></tr> 
								<tr> <td class="font-weight-bold">Cumplimiento</td> <td class="green--text"> {{ item.cumplimiento ==1? 'Compromiso Realizado':'Pendiente' }} </td></tr> 
								<tr> <td class="font-weight-bold"></td>
								<td align="right" class="pa-1">
									<v-btn color="celeste" dark fab x-small @click="VerDetalle(item)"><v-icon>visibility</v-icon></v-btn> 
								</td></tr>
							</tbody>
						</template>
					</v-simple-table>
				</v-card>
			</v-col>

			<v-dialog v-model="VerDetalleModal" width="500">
				<v-card >
					<v-card-actions class="headline white--text celeste">
						<v-card-title >{{ nomcatego }}</v-card-title>
						<v-spacer></v-spacer>
						<v-btn color="white" small  text @click="VerDetalleModal=false"><v-icon  >clear</v-icon></v-btn>
					</v-card-actions>
					<v-btn color="green" text small block v-if="cumplimiento === 1">Compromiso Cumplido</v-btn>
					<v-simple-table fixed-header height="auto" >
						<template v-slot:default>
							<thead>
								<tr><th class="text-left">Cliente		 	  </th><th class="text-left">{{ nomcli }}</th></tr>
								<tr><th class="text-left">Fecha 		 	  </th> <th class="text-left">{{ fecha }}</th></tr>
								<tr><th class="text-left">Hora		  	  </th><th class="text-left">{{ hora }}</th></tr>
								<tr><th class="text-left">Fecha de cierre </th><th class="text-left">{{ fecha_cierre }}</th></tr>
								<tr><th class="text-left">Hora de cierre	</th><th class="text-left">{{ hora_cierre  }}</th></tr>
								<tr><th class="text-left">Observaciónes </th><th class="text-left">{{ obs_usuario}}</th></tr>
							</thead>
						</template>
					</v-simple-table>
				</v-card>
			</v-dialog>

			<v-col cols="12"  v-if="!getCompromisosHechos.length && !Loading">  <!-- ALERTA => NO HAY COMPROMISOS -->
				<v-alert prominent text color="rosa">
					<v-row align="center">
						<v-col class="grow">No se ha encontrado un historial de compromisos.</v-col>
						<v-col class="shrink">
							<v-btn fab small color="celeste" dark @click="consultar"> <v-icon> refresh </v-icon> </v-btn>
						</v-col>
					</v-row>
				</v-alert>
			</v-col>

		</v-row>
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	var moment = require('moment'); 
	export default {
		data(){
			return{
				search: '',
				dialog: false,
				param: 0,
				edit:'',
				VerDetalleModal: false,
				
				nomcatego 		: '',
				nomcli    		: '',
				fecha_compromiso     		: '',
				hora      		: '',
				comentarios 	: '',
				fecha_cierre 	: '',
				hora_cierre  	: '',
				obs_usuario   : '',
				cumplimiento  : '',

				Compromisos: [],

				// MANEJO DE FECHAS
				fecha: '',
				fechaModal: false,
			}
		},

		created(){
			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha_compromiso = y + "-" + m +"-" + d;
			this.consultar(); // LLENAR COMPROMISOS
			moment.locale('es') /// inciar Moment 
		},

		watch:{
			fecha: function(){
				
				const payload = { id_vendedor: this.getUsuarios.id, fecha: this.fecha}
				this.busxFecha(payload)
			}
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getCompromisosHechos']), // IMPORTANDO USO DE VUEX 
      ...mapGetters('Usuarios',['getUsuarios']),

		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisoshechos']), // IMPORTANDO USO DE VUEX

			consultar(){ // CONSULTAR COMPROMISOS
				var me = this; var id = this.getUsuarios.id
				this.consultaCompromisoshechos(id)
			},

			busxFecha(){

			},

			VerDetalle(item){
				this.nomcatego 		= item.nomcatego;
				this.nomcli    		= item.nomcli;
				this.fecha        = moment(item.fecha).format('LL');
				this.hora         = moment(item.start).calendar(); 
				this.comentarios 	= item.comentarios;
				this.fecha_cierre = moment(item.fecha_cierre).format('LL');
				this.hora_cierre  = moment(item.hora_cierre).calendar();
				this.obs_usuario  = item.obs_usuario;
				this.cumplimiento = item.cumplimiento;
				this.VerDetalleModal = true
				
			},

			editar(item){  // VER DETALLE DEL COMPROMISO
				this.$router.push({name: 'det_compromiso', params:{ detalle:item }})
			},

			abrirModal(action, items){
				this.param = action;
				this.edit = items;
				this.dialog = true;
			},

		}
	}
</script>

<style scoped>

</style>