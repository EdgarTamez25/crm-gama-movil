<template>
  <v-row>
    
    <v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
      <strong> {{alerta.text}} </strong>
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
      </template>
    </v-snackbar>
    
    <v-col cols="12" class="py-0 my-3" align="right">
      <v-btn  outlined text  small color="error"  @click="$emit('modal',false)"> <v-icon>mdi-close</v-icon>  </v-btn>
    </v-col>

    <!-- VISTA PRINCIPAL-->
    <v-col cols="12" class=" py-0 " >
      <v-tabs color="rosa" centered  show-arrows >
        <v-tab> PENDIENTES </v-tab>
        <v-tab> AUTORIZADOS </v-tab>

        <!-- TAB PENDIENTE  -->
        <v-tab-item justify="center" class="mt-2">
          <v-row justify="center"> 
            <v-col cols="12" v-if="!Loading" class="pa-4">
              <v-alert text dense	color="warning"	icon="mdi-clock-fast" v-if="!Notificaciones.length">	NO HAY PARTIDAS POR AUTORIZAR </v-alert>

              <v-card v-for="(item , i) in Notificaciones" :key="i"  v-else class="mt-1">
                <v-list two-line subheader >
                  <v-list-item>
                    <v-list-item-content >
                      <v-list-item-title    class="celeste--text font-weight-black caption"># {{ item.id_compromiso }}</v-list-item-title>
                      <v-list-item-title    class="rosa--text font-weight-black caption">{{ item.id_depto === 1 ? 'COTIZACIÓN':'FICHA TÉCNICA' }}</v-list-item-title>
                      <v-list-item-title    class="font-weight-black caption">{{ item.codigo }}</v-list-item-title>
                      <v-list-item-subtitle class="font-weight-black caption"> {{ item.nomcli }}</v-list-item-subtitle>
                    </v-list-item-content>
                    <v-list-item-action>
                      <v-btn 
                        fab small dark  
                        color="celeste"
                        @click="alertaRespuesta = true; 
                        itemAEditar= item"
                      > 
                        <v-icon > mdi-account-check </v-icon> 
                      </v-btn>
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
            </v-col>
          </v-row>
        </v-tab-item>
        <!-- TAB AUTORIZADOS -->
        <v-tab-item justify="center" class="mt-2">
          <v-row justify="center"> 
            <v-col cols="12">
              <v-menu
                ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="fecha" 
                transition="scale-transition" offset-y min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field 
                    v-model="fecha" label="Fecha" append-icon="mdi-calendar" 
                    v-bind="attrs" v-on="on" outlined dense hide-details color="rosa"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="fecha" no-title scrollable color="rosa">
                  <v-spacer></v-spacer>
                  <v-btn text color="error" @click="menu = false" > Cancelar </v-btn>
                  <v-btn  color="rosa"  dark @click="$refs.menu.save(fecha)"> OK </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
        

            <v-col cols="12" v-if="!Loading" class="pa-4 ">
              <v-alert text dense	color="warning"	icon="mdi-clock-fast" v-if="!Autorizadas.length">	NO HAY PARTIDAS AUTORIZADAS </v-alert>
              <v-card v-for="(item , i) in Autorizadas" :key="i"  v-else class="mt-1">
                <v-list two-line subheader >
                  <v-list-item>
                    <v-list-item-content >

                      <v-list-item-title    class="celeste--text font-weight-black caption"># {{ item.id_compromiso }}</v-list-item-title>
                      <v-list-item-title    class="rosa--text font-weight-black caption">{{ item.id_depto === 1 ? 'COTIZACIÓN':'FICHA TÉCNICA' }}</v-list-item-title>
                      <v-list-item-title    class="font-weight-black caption">{{ item.codigo }}</v-list-item-title>
                      <v-list-item-subtitle class="font-weight-black caption"> {{ item.nomcli   }}</v-list-item-subtitle>
                    </v-list-item-content>

                    <v-list-item-action v-if="item.estatus != 5 && item.id_depto === 1">
                      <v-btn fab color="celeste" small dark  @click="alertaRespuesta = true; itemAEditar= item "> 
                        <v-icon > mdi-text-box-check-outline </v-icon> 
                      </v-btn>
                      <!-- <v-btn fab small color="error" dark class="ma-1" @click="alertaRespuesta = true; itemAEditar=item; estatusAEdit= 1"> <v-icon> mdi-close </v-icon> </v-btn> -->
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
            </v-col>
          </v-row>
        </v-tab-item>

        <!-- ARRAY DE PERSONAL FUERA DE LA EMPRESA -->
        <v-tab-item justify="center">
        </v-tab-item>
      </v-tabs>
    </v-col>

    <loading v-if="Loading" class="mt-5"/>

     <v-col cols="12" class="mt-6">
      <v-btn  block x-small color="gris" dark @click="init()"> RECARGAR  </v-btn>
    </v-col>

    <v-dialog v-model="alertaRespuesta" persistent>
      <v-card class="pa-1 " v-if="!solicitarFTModal">
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn  
            outlined text small
            color="error"  
            @click="alertaRespuesta = false"
          > <v-icon>mdi-close</v-icon>  
          </v-btn>
        </v-card-actions>

        <v-card-text class="mt-3" v-if="itemAEditar.estatus != 3">
          <v-btn  
            block large dark
            color="success"  
            @click="respuestaCliente(3)"
          > AUTORIZADO  
          </v-btn>
        </v-card-text>

        <!-- SE MUESTRA SIEMPRE Y CUANDO SEA COTIZAION Y NO FT -->
        <v-card-text class="my-3" v-if="itemAEditar.tipo === 2 && itemAEditar.estatus != 3"> 
           <v-btn  
            block large color="celeste" height="80px" dark 
            @click="solicitarFTModal = true; 
            autorizar = true; "
          >
              SOLICITAR FICHA TECNICA  <br> Y <br> AUTORIZAR COTIZACION 
          </v-btn>
        </v-card-text>

        <v-card-text class="my-3" v-if="itemAEditar.tipo === 2 && itemAEditar.estatus === 3"> 
           <v-btn  block large color="celeste" dark @click="solicitarFTModal = true; autorizar = false;">
              SOLICITAR FICHA TECNICA 
          </v-btn>
        </v-card-text>
        
        <v-card-text class="my-3" v-if="itemAEditar.estatus != 3">
          <v-btn 
            block large outlined dark 
            color="error"
            @click="respuestaCliente(1)"
          >
            RECHAZADO   
          </v-btn>
        </v-card-text>
      </v-card>

      <!-- FALTA RECARGAR VISTA AL MOMENTO DE PROCESAR -->
      <v-card class="pa-5" v-else> 
        <solicitarFT 
          :itemAEditar="itemAEditar" 
          :autorizar="autorizar" 
          @modal="solicitarFTModal = $event"
          @CerrarAll="alertaRespuesta = $event"
        /> 
      </v-card>
      
    </v-dialog>

    <overlay v-if="overlay"/>
  </v-row>  
</template>

<script>
  import loading from '@/components/loading.vue'
  import {mapGetters, mapActions} from 'vuex'
  import overlay from '@/components/overlay.vue'
	import metodos from '@/mixins/metodos.js';
  import solicitarFT from '@/views/Solicitudes/solicitarFT.vue'

  export default {
		mixins:[metodos],
    components:{
      loading,
      overlay,
      solicitarFT
    }, 
    data:() => ({
      itemAEditar: {},
      alertaRespuesta: false,
      overlay: false,
      alerta: { 
          activo: false,
          text: '',
          color: 'error',
      },
		  fecha: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      menu: false,

      solicitarFTModal: false,
      tipoDoc: null,
      autorizar: false,
      
    }),
    
    created(){
    },

    watch:{
			fecha(){
        this.init();
			}
		},

    computed:{
      ...mapGetters('Notificaciones',['Notificaciones','Autorizadas','Loading']),
      ...mapGetters('Usuarios',['getUsuarios']),

    },

    methods:{
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar','consultaAutorizados']),
      
      async init(){
        this.consultaPendientesxValidar(this.getUsuarios.id);
        const payload = {
          fecha: this.fecha ? this.fecha: this.traerFechaActual(),
          id_usuario: this.getUsuarios.id,
        }
        // console.log('payload init', payload);
        await this.consultaAutorizados(payload);
      },

      respuestaCliente(estatus){
        this.alertaRespuesta = false; 
        this.overlay = true;

        const payload = { 
          id           : this.itemAEditar.id,  // id para actualizar
          estatus      : estatus,              // estatus a actualizar 1 O 3 ,
          fecha        : this.traerFechaActual(),
          id_compromiso: this.itemAEditar.id_compromiso
        }
       // ESTA FUNCION CAEE EN EL CONTROLADOR DE REGISTROS DE ACTIVIDAD
        this.$http.post('actualiza.estatus.resultado', payload ).then( response =>{
          // console.log('response', response.body)
          this.alerta = { activo: true, text: response.bodyText , color:'green'}
          this.init();
        }).catch( error =>{
          this.alerta = { activo: true, text: error.bodyText    , color:'error '}
          // this.activarAlerta(error.bodyText, 500);
        }).finally(()=>{
          this.overlay = false;
        })


      }
    }
  }
</script>