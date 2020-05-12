<template>
	<v-container>
		<v-row justify="center">
			<v-col cols="12">

				<v-snackbar top v-model="snackbar" :timeout="1000"  :color="color"> {{text}}
					<v-btn color="white" text @click="snackbar = false" > Cerrar </v-btn>
				</v-snackbar>

				<v-card-actions class="pa-0" >
					<h3> <strong> Formación de Ruta </strong></h3> 
					<v-spacer></v-spacer>
					<v-btn color="error" small @click="$emit('modal',false)" text><v-icon>clear</v-icon></v-btn>
				</v-card-actions>

				<v-divider class="ma-2"></v-divider>

				<v-card-text class="pa-2" align="justify">  <!-- TITULO DE LA MODAL -->
					<strong> <p>{{ TITULO_MODAL }} </p> </strong>
				</v-card-text>

				<v-col cols="12" > <!-- RUTA A SELECCIONAR -->
					<v-select 
						v-model="ruta" :items="rutasDisponible"  item-text="nombre" item-value="id" append-icon="emoji_transportation" outlined  
						label="Escuela" return-object hide-details dense  color="celeste"
					></v-select>
				</v-col>
		
				<v-col cols="12"> <!-- BOTON DE GUARDAR -->
					<v-btn color="rosa" block dark small @click="validaInfo"> {{ param===1? 'Guardar':'Actualizar'}} </v-btn>
				</v-col>
			
				<!-- //DIALOG PARA GUARDAR LA INFORMACION -->
				<!-- <v-card-actions> -->
					<!-- <v-spacer></v-spacer> -->
					 <!-- <v-btn small :disabled="dialog" persistent :loading="dialog" 
					 			  block dark center class="white--text" color="success" @click="validaInfo" v-if="param === 1">
             GUARDAR   
          </v-btn> -->
					<!-- <v-btn small :disabled="dialog" persistent :loading="dialog" 
								 block dark center class="white--text" color="success" @click="validaInfo" v-if="param === 2">
             INICIAR RECORRIDO  
          </v-btn> -->

          <v-dialog v-model="dialog" hide-overlay persistent width="300">
            <v-card color="blue darken-4" dark >
              <v-card-text> <th style="font-size:17px;" align="center">{{ textDialog }}</th>
                <br>
                <v-progress-linear indeterminate color="white" class="mb-0" persistent></v-progress-linear>
              </v-card-text>
            </v-card>
          </v-dialog>

					<v-dialog v-model="Correcto" hide-overlay persistent width="350">
            <v-card color="success"  dark class="pa-3">
							<h3><strong>{{ textCorrecto }} </strong></h3>
            </v-card>
						
          </v-dialog>
				<!-- </v-card-actions> -->

			</v-col>
		</v-row>
	</v-container>
	
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	export default {
	  components: {
		},
		props:[
			'param',
			'edit',
	  ],
	  data () {
			return {
				fecha: '',
				dialog : false,
				textDialog : "Guardando Información",
				Correcto   : false,
				textCorrecto: '',
				// VARIABLES PRINCIPALES
				ruta: { id: null , nombre: ''},
			 // ALERTAS
				snackbar: false,
				text		: '',
				color		: 'red darken-4',

			}
		},

		created(){
			this.validarModoVista() // VALIDO EL MODO DE LA VISTA

			// ASIGNAR FECHA
			const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
			this.fecha = y + "-" + m +"-" + d;
		},
			
		computed:{
			// IMPORTANDO USO DE VUEX - ZONAS (GETTERS)
			...mapGetters('Rutas' ,['rutasDisponible']), 
			
			TITULO_MODAL: function(){
				var contenido = '';
				this.ruta.id != null ? contenido = 'Estas agregando  el compromiso número ' + this.edit.id + ' a la ' + this.ruta.nombre
														 : contenido = 'Seleccione una ruta'
				return contenido;
			}
		},

		watch:{
			edit: function(){
				this.validarModoVista();
			}
		},

		methods:{
			...mapActions('Rutas'  ,['consultaEnRuta']), // IMPORTANDO USO DE VUEX 	

			validarModoVista(){
				console.log('recibiendo edit', this.edit)
				if(this.param === 2){
					this.ruta = { id:this.edit.id, nombre:this.edit.nombre }
				}else{
				// this.limpiarCampos()
				}
			},

			validaInfo(){
				if(!this.ruta.nombre) { this.snackbar = true; this.text="No puedes omitir la RUTA" ; return }
				this.PrepararPeticion()
			},

			PrepararPeticion(){
				// FORMAR ARRAY A MANDAR
				const payload = { id_vendedor: this.edit.id_vendedor,
													numruta: this.ruta.id,
													nombre: this.ruta.nombre,
													id_compromiso: this.param === 1 ? this.edit.id: this.edit.id_compromiso, 
													estatus: 0,
													fecha: this.param === 1? this.fecha: this.edit.fecha
												}
				// VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.param === 1 ? this.CrearRuta(payload): this.ActualizarRuta(payload);
			},

			CrearRuta(payload){
				
				this.dialog = true ; setTimeout(() => (this.dialog = false),1000) // ACTIVO DIALOGO -> GUARDANDO INFO
				this.$http.post('agregar.ruta', payload).then((response)=>{ // MANDO A INSERTAR ZONA
					const enruta = { id: this.edit.id, enruta: 1}; 				// FORMO ARRAY PARA ACTUALIZAR EL COMPROMISO
					this.$http.post('en.ruta', enruta).then(response =>{  // CAMBIO ESTATUS DEL COMPROMISO
						this.TerminarProceso(response.body); 								// MANDO A TERMINAR PROCESO
					}).catch(error=>{
						 console.log('error enruta',error)
					})
				}).catch(error=>{
					console.log('error en add', error)
				})
			},

			ActualizarRuta(payload){
				this.dialog = true ; this.textDialog ="Actualizando Información" // ACTIVO DIALOGO -> GUARDANDO INFO
				setTimeout(() => (this.dialog = false), 2000);

				console.log('id a mandar', this.edit.id )
				console.log('contenido', payload )

				this.$http.put('put.en.ruta/'+ this.edit.id, payload).then((response)=>{
					this.TerminarProceso(response.body);					
				})
			},

			TerminarProceso(mensaje){
				var me = this ;
				this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
				setTimeout(function(){ me.$emit('modal',false)}, 1000);
				// this.limpiarCampos();  //LIMPIAR FORMULARIO
				const payload = { id_vendedor: 7 , fecha: this.fecha} // llENO ARRAY CON FECHA ACTUAL Y ID_USER
				this.consultaEnRuta(payload)
			},

			// limpiarCampos(){
			// 	this.ruta.id = null;
			// 	this.ruta.nombre = '';
			// },
			
		}
	}
</script>