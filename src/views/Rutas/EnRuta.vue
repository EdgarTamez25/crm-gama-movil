<template>
	<v-content class="pa-0" >
		<v-row>
			<v-btn color="rosa" style="display: none" class="ir-arriba white--text" small fab fixed bottom left> <!-- IR ARRIBA -->
				<v-icon top>keyboard_arrow_up</v-icon>
			</v-btn>

			<v-col cols="12" class="text-center"> <!-- TITULO DE LA VISTA -->
				<strong>EN RUTA</strong>
			</v-col>

			<v-col cols="10" > <!-- FECHA DE COMPROMISO -->
				<v-dialog ref="fechaCompromiso" v-model="fechaModal" :return-value.sync="fecha" persistent 	width="290px"	>
					<template v-slot:activator="{ on }">
						<v-text-field
							v-model="fecha" label="Fecha de compromiso" append-icon="event" readonly 	v-on="on"
							outlined 	dense color="rosa" hide-details
						></v-text-field>
					</template>

					<v-date-picker v-model="fecha" locale="es-es" color="celeste" scrollable>
						<v-spacer></v-spacer>
						<v-btn text color="gris" @click="fechaModal = false">Cancel</v-btn>
						<v-btn dark color="celeste" @click="$refs.fechaCompromiso.save(fecha)">OK</v-btn>
					</v-date-picker>
				</v-dialog>
			</v-col>

			<v-col cols="2"> <!-- ACTUALIZAR VISTA -->
				<v-btn  class="gris" icon dark @click="consultar" ><v-icon>refresh</v-icon> </v-btn>
			</v-col>		

			<v-col cols="12"  v-if="ruta1.length" > <!-- RUTA 1 -->
				<v-card> 
					<v-card-actions> 
						<v-card-text>	RUTA 1 </v-card-text>
						<v-spacer></v-spacer>
						<v-btn color="rosa" small dark>Inicar ruta</v-btn>
					</v-card-actions>
					<v-card v-for="(rta1, a) in ruta1" :key="a" >
						<v-simple-table fixed-header height="auto" class="my-1">
							<template v-slot:default>
								<thead>
									<tr>
										<th class="text-left">#</th>
										<th class="text-left">{{ rta1.id}}</th>
									</tr>
									<tr>
										<th class="text-left">Categoria</th>
										<th class="text-left">{{ rta1.nomcatego}}</th>
									</tr>
									<tr>
										<th class="text-left">Cliente</th>
										<th class="text-left">{{ rta1.nomcli}}</th>
									</tr>
										<th class="text-left">Fecha</th>
										<th class="text-left">{{ rta1.fecha}}</th>
									<tr>
										<th class="text-left">Hora</th>
										<th class="text-left">{{ rta1.hora }}</th>
									</tr>
									<tr>
										<th class="text-left"></th>
										<th class="text-right">
											<v-btn class="celeste" dark @click="abrirModal(2, rta1)"><v-icon> airport_shuttle </v-icon></v-btn>
										</th>
									</tr>
								</thead>
							</template>
						</v-simple-table>
					</v-card>
				</v-card>
			</v-col>

			<v-col cols="12"  v-if="ruta2.length" > <!-- RUTA 2 -->
				<v-card> 
					<v-card-actions> 
						<v-card-text>	RUTA 2 </v-card-text>
						<v-spacer></v-spacer>
						<v-btn color="rosa" small dark>Inicar ruta</v-btn>
					</v-card-actions>
						<v-card  v-for="(rta2, a) in ruta2" :key="a" >
							<v-simple-table fixed-header height="auto" class="my-1 ">
								<template v-slot:default>
									<thead>
										<tr>
											<th class="text-left">#</th>
											<th class="text-left">{{ rta2.id}}</th>
										</tr>
										<tr>
											<th class="text-left">Categoria</th>
											<th class="text-left">{{ rta2.nomcatego}}</th>
										</tr>
										<tr>
											<th class="text-left">Cliente</th>
											<th class="text-left">{{ rta2.nomcli}}</th>
										</tr>
											<th class="text-left">Fecha</th>
											<th class="text-left">{{ rta2.fecha}}</th>
										<tr>
											<th class="text-left">Hora</th>
											<th class="text-left">{{ rta2.hora }}</th>
										</tr>
										<tr>
											<th class="text-left"></th>
											<th class="text-right">
												<v-btn class="celeste" dark @click="abrirModal(2, rta2)"><v-icon> airport_shuttle </v-icon></v-btn>
											</th>
										</tr>
									</thead>
								</template>
							</v-simple-table>
						</v-card>
				</v-card>
			</v-col>

			<v-col cols="12" v-if="ruta3.length"  > <!-- RUTA 3 -->
				<v-card> 
					<v-card-actions> 
						<v-card-text>	RUTA 3 </v-card-text>
						<v-spacer></v-spacer>
						<v-btn color="rosa" small dark>Inicar ruta</v-btn>
					</v-card-actions>
						<v-card  v-for="(rta3, s) in ruta3" :key="s" >
							<v-simple-table fixed-header height="auto" class="my-1 ">
								<template v-slot:default>
									<thead>
										<tr>
											<th class="text-left">#</th>
											<th class="text-left">{{ rta3.id}}</th>
										</tr>
										<tr>
											<th class="text-left">Categoria</th>
											<th class="text-left">{{ rta3.nomcatego}}</th>
										</tr>
										<tr>
											<th class="text-left">Cliente</th>
											<th class="text-left">{{ rta3.nomcli}}</th>
										</tr>
											<th class="text-left">Fecha</th>
											<th class="text-left">{{ rta3.fecha}}</th>
										<tr>
											<th class="text-left">Hora</th>
											<th class="text-left">{{ rta3.hora }}</th>
										</tr>
										<tr>
											<th class="text-left"></th>
											<th class="text-right">
												<v-btn class="celeste" dark @click="abrirModal(2, rta3)"><v-icon> airport_shuttle </v-icon></v-btn>
											</th>
										</tr>
									</thead>
								</template>
							</v-simple-table>
						</v-card>
				</v-card>
			</v-col>

			<v-col cols="12" v-if="ruta4.length" > <!-- RUTA 4 -->
				<v-card> 
					<v-card-actions> 
						<v-card-text>	RUTA 4</v-card-text>
						<v-spacer></v-spacer>
						<v-btn color="rosa" small dark>Inicar ruta</v-btn>
					</v-card-actions>
						<v-card  v-for="(rta4, m) in ruta4" :key="m" >
							<v-simple-table fixed-header height="auto" class="my-1 ">
								<template v-slot:default>
									<thead>
										<tr>
											<th class="text-left">#</th>
											<th class="text-left">{{ rta4.id}}</th>
										</tr>
										<tr>
											<th class="text-left">Categoria</th>
											<th class="text-left">{{ rta4.nomcatego}}</th>
										</tr>
										<tr>
											<th class="text-left">Cliente</th>
											<th class="text-left">{{ rta4.nomcli}}</th>
										</tr>
											<th class="text-left">Fecha</th>
											<th class="text-left">{{ rta4.fecha}}</th>
										<tr>
											<th class="text-left">Hora</th>
											<th class="text-left">{{ rta4.hora }}</th>
										</tr>
										<tr>
											<th class="text-left"></th>
											<th class="text-right">
												<v-btn class="celeste" dark @click="abrirModal(2, rta4)"><v-icon> airport_shuttle </v-icon></v-btn>
											</th>
										</tr>
									</thead>
								</template>
							</v-simple-table>
						</v-card>
				</v-card>
			</v-col>

			<v-dialog persistent v-model="dialog" width="600px" >	
				<v-card>
					<formaRuta :param="param" :edit="edit" @modal="dialog = $event" />
				</v-card>
			</v-dialog>
		</v-row>
		
	</v-content>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex';
	import formaRuta  from '@/views/Rutas/formaRuta.vue';
	import $ from 'jquery';
	export default {
		components: {
			formaRuta
		},
		data(){
			return{
				Rutas:[],
				search: '',
				dialog: false,
				param: 0,
				edit:'',
				
				Compromisos: [],

				// MANEJO DE FECHAS
				fecha: '',
				fechaModal: false,

				ruta_1 : [],
				ruta_2 : [],
				ruta_3 : [],
				ruta_4 : []
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

		created(){

			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha = y + "-" + m +"-" + d;

			// LLENAR COMPROMISOS
			const payload = { id_vendedor: 7 , fecha: this.fecha}
			this.consultaEnRuta(payload)
		},

		watch:{
			fecha: function(){
				const payload = { id_vendedor: 7 , fecha: this.fecha}
				this.consultaEnRuta(payload)
			}
		},

		computed:{
			...mapGetters('Rutas'  ,['Loading','getEnRuta']), // IMPORTANDO USO DE VUEX 
			...mapGetters('Rutas'  ,['ruta1','ruta2','ruta3','ruta4']), // IMPORTANDO USO DE VUEX 

		},

		methods:{
			...mapActions('Rutas'  ,['consultaEnRuta']), // IMPORTANDO USO DE VUEX
		
			consultar(){ // CONSULTAR COMPROMISOS
				const payload = { id_vendedor: 7 , fecha: this.fecha } // FORMO ARRAY CON id DEL VENDEDOR LOGEADO Y FECHA ACTUAL
				this.consultaEnRuta(payload)
			},

			editar(item){  // VER DETALLE DEL COMPROMISO
				this.$router.push({name: 'det_compromiso', params:{ detalle:item }})
			},

			abrirModal(action, items){
				console.log('items', items)
				this.param = action;
				this.edit = items;
				this.dialog = true;
			},

		}


	}
</script>