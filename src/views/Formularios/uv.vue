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
            <v-col cols="12">
              <v-text-field 
                v-model="referencia" hide-details dense label="PRODUCTO" 
                filled color="celeste" class="mt-1 font-weight-black" :rules="referRules"
              />
            </v-col>
             <!-- // !SELETOR DE MATERIALES  -->
            <v-col cols="12" >
              <v-select
                v-model="material" :items="materiales" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Materiales" return-object placeholder="Materiales" :rules="materialRules"
              ></v-select> 
            </v-col>
             <!-- // !INPUT PARA PANTONE  -->
            <v-col cols="9"  align="center" class="my-0 py-0">
              <v-text-field 
                v-model="pantone" label="Pantone " placeholder="Pantone" 
                outlined dense hide-details  
              ></v-text-field>
            </v-col>
             <!-- // !BOTON DE AGREGAR PANTONE  -->
            <v-col cols="3" class="text-right my-0 py-0">
              <v-btn color="celeste" dark @click="agregarPantone()" > <v-icon>add</v-icon> </v-btn>
            </v-col>
             <!-- // !CHIPS DE PANTONES   -->
            <v-col cols="12" class="my-0 py-0 text-left">
              <v-chip v-for="(item, i) in pantones" :key="i"
                class="ma-2" close :color="item" dark  @click:close="eliminaPanton(i)">
                {{ item }}
              </v-chip>
            </v-col>
            
             <!-- // ! ANCHO DE ETIQUETA   -->
            <v-col cols="6" class="my-0 py-2">
              <v-text-field 
                v-model="ancho" hide-details dense label="Ancho" 
                outlined color="celeste" :rules="anchoRules"
              />
            </v-col>
             <!-- // ! LARGO DE ETIQUETA   -->
            <v-col cols="6" class="my-0 py-2">
              <v-text-field 
                v-model="largo" hide-details dense label="Largo" 
                outlined color="celeste" :rules="largoRules"
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
      titulo         : 'CARACTERÍSTICAS',
      valid          : true,
      referRules     : [v => !!v || 'Es requerido'],
      materialRules  : [v => !!v || 'Es requerido'],
      pantonRules    : [v => !!v || 'Es requerido'],
      anchoRules     : [v => !!v || 'Es requerido'],
      largoRules     : [v => !!v || 'Es requerido'],
      tproducto    : { id:1, nombre: ''},
      tproductos   : [{ id:1, nombre:'Producto Existente'}, 
                      { id:2, nombre:'Modificación de producto'},
                      { id:3, nombre:'Nuevo Producto'}],

      material     : { id:null, nombre:''},
      materiales   : [],
			referencia   : '',
      pantone      : '',
      pantones     : [],
      ancho         : '',
      largo         : '',
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
   

      agregarPantone(){
        var esHexadecimal = false;
        if( esHexadecimal = this.esHexadecimal(this.pantone) ){
          this.pantones.push(this.pantone);
          this.pantone = '';
        }
      },

      esHexadecimal(pantone){ return /^#[0-9A-F]+$/i.test(pantone); },

      eliminaPanton(i){ this.pantones.splice(i,1); },

      validarModoVista(){ 
        this.consultaMateriales(this.depto_id);
        this.consultaAcabados(this.depto_id);

				if(this.modoVista === 2 ){
          // ASIGNAR VALORES AL FORMULARIO
          this.referencia   = this.parametros.referencia;
          this.material     = { id: this.parametros.id_material, nombre:''};
          this.pantones     = this.parametros.pantones;
          this.ancho         =  this.parametros.ancho;
          this.largo         =  this.parametros.largo;
          this.tproducto    = { id: this.parametros.tproducto, nombre:''};

				}else{
				  this.limpiarCampos()
				}
      },

      PrepararPeticion(){
      
        if(!this.pantones.length){ this.snackbar=true; this.text ="DEBES AGREGAR AL MENOS UN PANTONE"; return };

        const payload = { id             : this.modoVista === 1? this.consecutivo: this.parametros.id,
                          dx             : 9,
                          referencia     : this.referencia,
                          id_material    : this.material.id,
                          pantones       : this.pantones,
                          ancho          : this.ancho,
                          largo          : this.largo,
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
        this.tproducto    = { id:null, nombre:''};
        this.pantone      = '';
        this.pantones     = []
        this.ancho        = '';
        this.largo        = '';

      }
    }
  }
</script>
