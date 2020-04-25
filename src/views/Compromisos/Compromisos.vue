<template>
    <v-container>
			<v-row>
				<v-col cols="12" class="text-center">
						<strong>COMPROMISOS</strong>
				</v-col>
			

				<v-col cols="10" >
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

				<v-col cols="1">
					<v-btn  class="gris" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn>
				</v-col>		

				<v-col cols="12">
          <v-card flat>
						<v-data-table 
			      :headers="headers"
			      :items="getCompromisos"
				 		height="450px"
				  	hide-default-footer
						hide-default-header
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
			    		<v-btn  class="celeste"  dark ><v-icon> airport_shuttle </v-icon></v-btn> <!-- Editar -->
			    		<v-btn   class="success"  dark ><v-icon> remove_red_eye </v-icon></v-btn> <!-- Editar -->
				    </template>

			    </v-data-table>
					</v-card>
				</v-col>
			</v-row>
    </v-container>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	export default {
		data(){
			return{
				search: '',
				dialog: false,
				headers: [
								{ text: 'Categoria'					, align: 'left'	 , value: 'nomcatego' },
								{ text: 'Cliente'					  , align: 'left'	 , value: 'nomcli' },
								{ text: 'Fecha'							, align: 'left'	 , value: 'fecha' },
								{ text: 'Hora'							, align: 'left'	 , value: 'hora' },
								{ text: 'Cumplimiento'			, align: 'left'	 , value: 'cumplimiento' },
								{ text: '', value: 'action' , sortable: false },

							],
				Compromisos: [],

				// MANEJO DE FECHAS
				fecha: '',
				fechaModal: false,
			}
		},

		created(){

			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha = y + "-" + m +"-" + d;

			// LLENAR COMPROMISOS
			const payload = { id_vendedor: 7 , fecha: this.fecha}
			this.consultaCompromisos(payload)
		},

		watch:{
			fecha: function(){
				const payload = { id_vendedor: 7 , fecha: this.fecha}
				this.consultaCompromisos(payload)
			}
		},

		computed:{
			...mapGetters('Compromisos'  ,['Loading','getCompromisos']), // IMPORTANDO USO DE VUEX 
		},

		methods:{
			...mapActions('Compromisos'  ,['consultaCompromisos']), // IMPORTANDO USO DE VUEX

			consultar(){
				const payload = { id_vendedor: 7 , fecha: this.fecha}
				this.consultaCompromisos(payload)
			}
		}


	}
</script>