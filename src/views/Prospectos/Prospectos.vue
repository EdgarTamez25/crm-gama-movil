<template>
 <v-content class="pa-0">
		<v-row no-gutters>

  		<v-col cols="12">
				<v-card-actions class="font-weight-bold"> PROSPECTOS </v-card-actions>

				<v-card  flat >
					<v-card-actions>
			      <v-text-field
			        v-model="search"
			        append-icon="search"
			        label="Buscar prospectos"
			        single-line
			        hide-details
			      ></v-text-field>
			      <v-spacer></v-spacer>
			      <v-btn  class="celeste" icon dark @click="abrirModal(1)">
							<v-icon>person</v-icon>
						</v-btn>
			      <v-btn  class="gris" icon dark @click="consultaProspectos(getUsuarios.id)" ><v-icon>refresh</v-icon> </v-btn>
			    </v-card-actions>

			    <v-data-table
			      :headers="headers"
			      :items="getProspectos"
			      :search="search"
			      fixed-header
						:height="pantalla"
						hide-default-footer
						hide-default-header
						:loading ="Loading"
						loading-text="Cargando... Por favor espere."
						:page.sync="page"
      			:items-per-page="itemsPerPage"
						@page-count="pageCount = $event"
						
			    >
			    	<template v-slot:item.action="{ item }" > 
			    		<v-btn  class="celeste" icon dark @click="abrirModal(2, item)"><v-icon> create </v-icon></v-btn> 
				    </template>

			    </v-data-table>
			  </v-card>

				<!-- PAGINACION -->
				<div class="text-center pt-2">
					<v-pagination v-model="page" :length="pageCount"></v-pagination>
				</div>

				 <v-dialog persistent v-model="dialog" width="700px" >	
		    	<v-card class="pa-5">
		    		<ControlProspectos :param="param" :edit="edit" @modal="dialog = $event" />
		    	</v-card>
		    </v-dialog>
  		</v-col>

  	</v-row>
  </v-content>
</template>

<script>
	import ControlProspectos  from '@/views/Prospectos/ControlProspectos.vue';
	import {mapGetters, mapActions} from 'vuex';
	export default {
		components: {
			ControlProspectos
		},
		data () {
				return {
					page: 1,
					pageCount: 0,
					itemsPerPage: 20,

					search: '',
					movie:'data',
					dialog: false,
					param: 0,
					edit:'',
					headers:[
						{ text: '#'  		   , align: 'left'  , value: 'id'		  },
						{ text: 'Nombre'	 , align: 'left'  , value: 'nombre' },
						{ text: 'Telefono' , align: 'left'  , value: 'tel1' },
						{ text: 'Contacto' , align: 'left'  , value: 'contacto' },
						{ text: ''  , align: 'right' , value: 'action', sortable: false },
					],
				}
			},

			created(){
				if(!this.getUsuarios.id){ this.Salir() }
				this.consultaProspectos(this.getUsuarios.id) // CONSULTAR CLIENTES A VUEX
			},

			computed:{
				...mapGetters('Prospectos'  ,['Loading','getProspectos']), // IMPORTANDO USO DE VUEX - Prospectos (GETTERS)
				...mapGetters('Usuarios'    ,['getUsuarios']), // IMPORTANDO USO DE VUEX - Prospectos (GETTERS)


				pantalla () {
					return screen.height - 260;
				},
			},

			methods:{
				...mapActions('Prospectos'  ,['consultaProspectos']), // IMPORTANDO USO DE VUEX - CLIENTES(ACCIONES)
     		...mapGetters('Usuarios',['Salir']),

				abrirModal(action, items){
					this.param = action;
					this.edit = items;
					this.dialog = true;
				},
				
			}
	}
</script>
