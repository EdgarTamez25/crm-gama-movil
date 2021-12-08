<template>
  <v-row justify="center">
    <v-snackbar v-model="alerta.activo" multi-line vertical top right :color="alerta.color" > 
      <strong> {{alerta.texto}} </strong>
      <template v-slot:action="{ attrs }">
        <v-btn color="white" text @click="alerta.activo = false" v-bind="attrs"> Cerrar </v-btn>
      </template>
    </v-snackbar>

    <v-col cols="8" class="font-weight-black">
      SOLICITAR FICHA TECNICA
    </v-col>

    <v-col cols="4" class="py-0 my-3" align="right">
      <v-btn  outlined text  small color="error"  @click="$emit('modal',false)"> <v-icon>mdi-close</v-icon>  </v-btn>
    </v-col>

    <v-col cols="12" class="pa-0 ">
      <v-list two-line subheader >
        <v-list-item>
          <v-list-item-content >
            <v-list-item-title    class="celeste--text font-weight-bold subtitle-2"># {{ itemAEditar.id_compromiso }}</v-list-item-title>
            <v-list-item-title    class="font-weight-bold subtitle-2"> {{ itemAEditar.codigo }}</v-list-item-title>
            <v-list-item-subtitle class="font-weight-bold subtitle-2"> {{ itemAEditar.nomcli }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-col>

    <v-col cols="12" class="py-0" >
      <v-radio-group v-model="descripcion" mandatory >
          <v-radio
            label="Envíar información por correo"
            value="Envíar información por correo"
          ></v-radio>
          <v-radio
            label="Envíar información por what´s app"
            value="Envíar información por what´s app"
          ></v-radio>
      </v-radio-group>
    </v-col>

    <v-col cols="12">
      <v-btn color="success" dark block @click="solicitar_FT()"> Guardar Información </v-btn>
    </v-col>

    <overlay v-if="overlay"/>
  </v-row>
</template>

<script>
  import { mapActions, mapGetters } from 'vuex'
	import metodos     from '@/mixins/metodos.js'
	import overlay     from '@/components/overlay.vue'

  export default {
		mixins:[metodos],
    components:{
      overlay
    },
    props:[
			'itemAEditar',
      'autorizar'
	  ],
    data:() => ({
      descripcion: null,
      alerta: { 
        activo: false, 
        texto:'', 
        color:''
      },
      overlay : false,
    }),

    created(){
    },
    computed:{
			...mapGetters('Usuarios' ,['getUsuarios']),
    },
    methods:{
      ...mapActions('Notificaciones' ,['consultaPendientesxValidar']),
      solicitar_FT(){
        this.overlay = true; 
				const payload = new Object({
          'id'           : this.itemAEditar.id,
					'id_cliente'   : this.itemAEditar.id_cliente, 
					'id_creador'   : this.getUsuarios.id,
					'id_compromiso': this.itemAEditar.id_compromiso,
					'id_producto'  : this.itemAEditar.id_producto,
          'dx'           : this.itemAEditar.dx,
          'tipo_prod'    : this.itemAEditar.tipo_prod,
          'descripcion'  : this.descripcion,
 					'estatus'      : 1,
          'fecha'				 : this.traerFechaActual(),
					'hora'				 : this.traerHoraActual(),
          'autorizar'    : this.autorizar
				});

				this.$http.post('genera.solicitud.ft', payload).then(response =>{
          console.log('resp', response.body);
					var me = this
					this.alerta =  { activo: true, texto: response.bodyText , color:'green'};
          setTimeout(() =>{ 
            me.$emit('modal',false); 
            me.$emit('CerrarAll',false)
          }, 1000);

          this.consultaPendientesxValidar(this.getUsuarios.id); // RECARGO LA VISTA DE NOTIFICACIONES PENDIENTES
          if(this.$router.currentRoute.name != 'compromisos'){  // COMPARO LA RUTA EN LA QUE ME ENCUENTRO 
			  	  setTimeout(()=>{ me.$router.push({name: 'compromisos' })}, 1000); // SI ES DIFERENTE ENRUTO A PAGINA ARRANQUE
          }
				}).catch(error =>{
					this.alerta = { activo: true, texto: error.bodyText , color:'error'};
				}).finally(()=> this.overlay = false);
      }
    }
  }
</script>
  