<template>
  <!-- <v-main class="pa-0"> -->
    <v-row justify="center">
      <v-col cols="12" align="center"> 
        <v-snackbar v-model="snackbar" multi-line :timeout="2000" top color="error"> {{text}}
          <template v-slot:action="{ attrs }">
            <v-btn color="white" text @click="snackbar = false" v-bind="attrs"> Cerrar </v-btn>
          </template>
        </v-snackbar>

        <v-form ref="form" v-model="valid" > 
          <v-row >
            <v-col cols="12" class="my-0 py-0">
              <v-select
                v-model="tproducto" :items="tproductos" item-text="nombre" item-value="id" outlined color="celeste" 
                dense hide-details label="Tipo de producto" return-object  
              ></v-select>
            </v-col>
            
            <v-card-text class="font-weight-black pa-1 body-1">{{ titulo }}</v-card-text>
             
             <!-- //! REFERENCIA DEL PRODUCTO  -->
            <v-col cols="12" >
              <v-text-field 
                v-model="referencia" hide-details dense label="PRODUCTO" 
                filled color="celeste" class="mt-1 font-weight-black" :rules="referRules"
              />
            </v-col>
            
            <!-- // !SELETOR DE MATERIALES  -->
            <v-col cols="12" class="my-0 py-1">
              <v-select
                v-model="material" :items="materiales" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Materiales" return-object placeholder="Materiales" :rules="materialRules"
              ></v-select> 
            </v-col>
             <!-- // !SELETOR DE RESISTENCIA  -->
            <v-col cols="12" class="my-0 py-1">
              <v-select
                v-model="resistencia" :items="resistencias" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Resistencia" return-object placeholder="Resistencias" :rules="resisRules"
              ></v-select> 
            </v-col>
             <!-- // !SELETOR DE CALIBRE  -->
            <v-col cols="12" class="my-0 py-1">
              <v-select
                v-model="calibre" :items="calibres" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Calibre" return-object placeholder="Calibres" :rules="calibreRules"
              ></v-select> 
            </v-col>
             <!-- // !SELECTOR DE ACABADOS    -->
            <v-col cols="12" class="my-0 py-1">
              <v-select
                v-model="acabado" :items="acabados" item-text="nombre" item-value="id" outlined color="celeste" 
                dense hide-details label="Acabados" return-object multiple :rules="acabadoRules" :menu-props="{ maxHeight: '400' }"
              ></v-select>
            </v-col>

             <!-- // ! GROSOR   -->
            <v-col cols="12" class="my-0 py-1">
              <v-text-field 
                v-model="grosor" hide-details dense label="Grosor" 
                outlined color="celeste" :rules="grosorRules"
              />
            </v-col>
            
          </v-row>
        </v-form>
      </v-col>

      <v-col cols="12" class="my-3"/>

      <!-- //!CONTENEDOR DE CIERRE Y PROCESOS -->
      <v-footer  absolute>
        <v-spacer></v-spacer>
        <v-btn color="error" outlined small  @click="$emit('modal',false)"  class="ma-1">Cancelar </v-btn>
        <v-btn color="success" small :disabled="!valid" @click="PrepararPeticion()">Guardar </v-btn>
      </v-footer>

      <v-dialog v-model="dialog" hide-overlay persistent width="300">
        <v-card color="blue darken-4" dark >
          <v-card-text> <th style="font-size:17px;" align="center">{{ textDialog }}</th>
            <br><v-progress-linear indeterminate color="white" class="mb-0" persistent></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="Correcto" hide-overlay persistent width="350">
        <v-card color="success"  dark class="pa-3">
          <h3><strong>{{ textCorrecto }} </strong></h3>
        </v-card>
      </v-dialog>
    </v-row>
  <!-- </v-main> -->
</template>

<script>
	import  metodos from '@/mixins/metodos.js';
	import {mapGetters, mapActions} from 'vuex';
  
  export default {
    mixins:[metodos],
    props:[
			'modoVista',
      'parametros',
      'depto_id',
	  ],
    data: () => ({
      titulo       : 'CARACTERÍSTICAS',
      valid        : true,
      referRules   : [v => !!v || 'Es requerido'],
      materialRules: [v => !!v || 'Es requerido'],
      acabadoRules : [v => !!v || 'Es requerido'],
      resisRules   : [v => !!v || 'Es requerido'],
      calibreRules : [v => !!v || 'Es requerido'],
      grosorRules  : [v => !!v || 'Es requerido'],
      
      tproductos   : [{ id:1, nombre:'Producto Existente'}, 
                      { id:2, nombre:'Modificación de producto'},
                      { id:3, nombre:'Nuevo Producto'}],
      acabado      : [],
      acabados     : [], 
      material     : { id:null, nombre:''},
      materiales   : [],
      resistencia  : { id:null, nombre:''},
      resistencias : [],
      calibre      : { id:null, nombre:''},
      calibres     : [],
      grosor       : '',
      referencia   : '',
      
      // AVISOS
      snackbar      : false,
      text          : '',
      dialog        : false,
      textDialog    : "Guardando Información",
      Correcto      : false,
      textCorrecto  : '',
    }),

    created(){ 
      this.validarModoVista() ;
    },
    computed:{ ...mapGetters('Solicitudes',['consecutivo']),  },
    watch:{ 
      depto_id(){  this.validarModoVista(); } ,
      parametros(){ this.validarModoVista(); } ,
    }, 

    methods:{
      ...mapActions('Solicitudes'  ,['agregaProducto','actualizaProducto']), // IMPORTANDO USO DE VUEX (ACCIONES)

      validarModoVista(){ 
        this.consultaMateriales(this.depto_id);
        this.consultaAcabados(this.depto_id);

				if(this.modoVista === 2 ){
          // ASIGNAR VALORES AL FORMULARIO
          this.referencia  = this.parametros.referencia;
          this.material    = { id: this.parametros.id_material , nombre:''};
          this.resistencia = { id: this.parametros.resistencia , nombre:''};
          this.calibre     = { id: this.parametros.calibre     , nombre:''};
          this.acabado     = this.parametros.acabados;
          this.grosor      = this.parametros.grosor;
          this.tproducto    = { id: this.parametros.tproducto, nombre:''};

				}else{
				  this.limpiarCampos()
				}
      },

      PrepararPeticion(){
      
        if(!this.pantones.length){ this.snackbar=true; this.text ="DEBES AGREGAR AL MENOS UN PANTONE"; return };

        const payload = { id             : this.modoVista === 1? this.consecutivo: this.parametros.id,
                          dx             : 6,
                          referencia     : this.referencia,
                          id_material    : this.material.id,
                          resistencia    : this.resistencia.id,
                          calibre        : this.calibre.id,
                          acabados       : this.acabado,
                          grosor         : this.grosor,
                          tproducto      : this.tproducto.id
                        }
        // VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.modoVista === 1 ? this.Crear(payload): this.Actualizar(payload);
      },

      Crear(payload){
        this.dialog = true ; 
        this.agregaProducto(payload).then( res =>{
          if(res){ this.TerminarProceso("El producto se agrego a la lista"); }
        }).finally(()=>{ 
          this.dialog = false
        })
      },

      Actualizar(payload){
        this.dialog = true ;
        this.actualizaProducto(payload).then( res =>{
          if(res){ this.TerminarProceso("El producto se modifico correctamente"); }
        }).finally(()=>{ 
          this.dialog = false
        })
      },

			TerminarProceso(mensaje){
        var me = this ;
        this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
        setTimeout(function(){ me.$emit('modal',false)}, 2000);
        this.limpiarCampos();  //LIMPIAR FORMULARIO
      },

      limpiarCampos(){
        this.referencia   = '';
        this.material     = { id:null, nombre:''};
        this.resistencia  = { id:null, nombre:''};
        this.calibre      = { id:null, nombre:''};
        this.tproducto    = { id:null, nombre:''};
        this.acabado     = [];
        this.grosor       = '';
      }
    }
  }
</script>





<!-- <v-col cols="12" class="text-left my-0 py-0">
              <v-card-actions class="my-0 py-0">
                <v-card-text class="body-1 font-weight-black">SEMICORTES</v-card-text>
                <v-spacer></v-spacer>
                 <v-switch
                  v-model="semicorte"
                  flat
                  color="rosa"
                ></v-switch>
              </v-card-actions>
            </v-col> -->