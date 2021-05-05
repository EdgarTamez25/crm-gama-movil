<template>
  <v-row>
    <v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
      <strong> {{alerta.text}} </strong>
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
      </template>
    </v-snackbar>

    <v-col cols="9" class="py-0">
      <v-card-text class="font-weight-black text-left" style="word-break:normal;">
        PENDIENTES POR AUTORIZAR
      </v-card-text>
    </v-col>
    
    <v-col cols="3" class="py-1 text-right">
      <v-btn  outlined text  small color="error"  @click="$emit('modal',false)"> <v-icon>mdi-close</v-icon>  </v-btn>
    </v-col>
    
    <loading v-if="Loading"/>

    <v-col cols="12" v-if="!Loading">
		  <v-alert text dense	color="warning"	icon="mdi-clock-fast" v-if="!Notificaciones.length">	NO HAY PARTIDAS POR AUTORIZAR </v-alert>

      <v-card v-for="(item , i) in Notificaciones" :key="i"  v-else>
        <v-list two-line subheader >
          <v-list-item>
            <v-list-item-content >
              <v-list-item-title    class="rosa--text font-weight-black caption">{{ item.id_depto === 1 ? 'COTIZACIÓN':'FICHA TÉCNICA' }}</v-list-item-title>
              <v-list-item-title    class="font-weight-black caption">{{ item.codigo }}</v-list-item-title>
              <v-list-item-subtitle class="font-weight-black caption"> {{ item.nomcli }}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn fab color="celeste" small dark  @click="alertaRespuesta = true; itemAEditar= item "> <v-icon > mdi-account-check </v-icon> </v-btn>
              <!-- <v-btn fab small color="error" dark class="ma-1" @click="alertaRespuesta = true; itemAEditar=item; estatusAEdit= 1"> <v-icon> mdi-close </v-icon> </v-btn> -->
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </v-card>
    </v-col>

     <v-col cols="12">
      <v-btn  block small color="gris" dark @click="init()"> RECARGAR  </v-btn>
    </v-col>

    <v-dialog v-model="alertaRespuesta" persistent>
      <v-card class="pa-1 " >
        <v-card-actions>
          <v-spacer></v-spacer>
           <v-btn  outlined text  small color="error"  @click="alertaRespuesta = false"> <v-icon>mdi-close</v-icon>  </v-btn>
        </v-card-actions>
        <v-card-text class="mt-3">
           <v-btn  block large color="green" dark @click="respuestaCliente(3)"> AUTORIZADO  </v-btn>
        </v-card-text>
         <v-card-text class="my-3">
           <v-btn  block large color="error" dark @click="respuestaCliente(1)"> RECHAZADO  </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>

    <overlay v-if="overlay"/>
  </v-row>  
</template>

<script>
  import loading from '@/components/loading.vue'
  import {mapGetters, mapActions} from 'vuex'
  import overlay from '@/components/overlay.vue'

  export default {
    components:{
      loading,
      overlay
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
    }),
    
    created(){
    },

    computed:{
      ...mapGetters('Notificaciones',['Notificaciones','Loading']),
      ...mapGetters('Usuarios',['getUsuarios']),

    },

    methods:{
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar']),
      
      init(){
        this.consultaPendientesxValidar(this.getUsuarios.id)
      },

      respuestaCliente(estatus){
        this.alertaRespuesta = false; this.overlay = true;
        const payload = new Object({
          estatus : estatus,  // estatus a actualizar 
          id      : this.itemAEditar.id,  // id para actualizar
          id_det_sol : this.itemAEditar.id_det_sol,
          id_solicitud : this.itemAEditar.id_solicitud
        })
       // ESTA FUNCION CAEE EN EL CONTROLADOR DE REGISTROS DE ACTIVIDAD
        this.$http.post('actualiza.estatus.resultado', payload ).then( response =>{
          // console.log('response', response.bodyText)
          this.alerta = { activo: true, text: response.bodyText , color:'green'}
          this.init();
          // let me = this;
          // setTimeout(() => {  me.$emit('modal',false) }, 1000);

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