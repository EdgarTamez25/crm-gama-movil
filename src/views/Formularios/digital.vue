<template>

    
    <v-row justify="center">
      <v-col cols="12" align="center"> 
        <v-snackbar v-model="snackbar" multi-line :timeout="2000" top color="error"> {{text}}
          <template v-slot:action="{ attrs }">
            <v-btn color="white" text @click="snackbar = false" v-bind="attrs"> Cerrar </v-btn>
          </template>
        </v-snackbar>

        <v-form ref="form"  > 
          <v-row >
            <v-col cols="12" class="my-0 py-0" >
              <v-select
                v-model="tproducto" :items="tproductos" item-text="nombre" item-value="id" outlined color="celeste" 
                dense hide-details label="Tipo de producto" return-object  :disabled="modoVista===2 || modoVista=== 4?true:false"
              ></v-select>
            </v-col>
           

            <v-card-text class="font-weight-black pa-1 body-1">{{ titulo }}</v-card-text>
             
             <!-- //! REFERENCIA DEL PRODUCTO  -->
            <v-col cols="12">
              <v-text-field 
                v-model="referencia" hide-details dense label="REFERENCIA" 
                filled color="celeste" class=" font-weight-black" 
              />
            </v-col>
            <!-- //! CANTIDAD  -->
            <v-col cols="12" class="" >
              <v-text-field 
                v-model="cantidad" hide-details dense label="Cantidad" 
                outlined color="celeste" placeholder="Cantidad de material"
              />
            </v-col>
             <!-- // ! SELETOR  MATERIALES  -->
            <v-col cols="12" class="my-0 py-0" v-if="ACTIVACAMPO">
              <v-select
                v-model="material" :items="materiales" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Materiales" return-object placeholder="Materiales"
              ></v-select> 
            </v-col>
             <!-- // ! SELETOR SOBRE QUE MATERIALES  -->
             <v-col cols="12"  v-if="ACTIVACAMPO">
              <v-select
                v-model="material2" :items="materiales2" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Material Secundario" return-object placeholder="Material Secundario" 
              ></v-select> 
            </v-col>
             <!-- // !INPUT PARA PANTONE  -->
            <v-col cols="8"  align="center" class="my-0 py-0" v-if="ACTIVACAMPO">
              <v-text-field 
                v-model="pantone" label="Pantone " placeholder="Pantone" 
                outlined dense hide-details  
              ></v-text-field>
            </v-col>
             <!-- // !BOTON DE AGREGAR PANTONE  -->
            <v-col cols="3" class="text-right my-0 py-0" v-if="ACTIVACAMPO">
              <v-btn color="celeste" dark @click="agregarPantone()" > <v-icon>add</v-icon> </v-btn>
            </v-col>
             <!-- // !CHIPS DE PANTONES   -->
            <v-col cols="12" class="my-0 py-0 text-left" v-if="ACTIVACAMPO">
              <v-chip v-for="(item, i) in pantones" :key="i"
                class="ma-2" dark close :color="item" @click:close="eliminaPanton(i)">
                {{ item }}
              </v-chip>
            </v-col>
             <!-- // !SELECTOR DE ACABADOS    -->
            <v-col cols="12" v-if="ACTIVACAMPO">
              <v-select
                v-model="acabado" :items="acabados" item-text="nombre" item-value="id" outlined color="celeste" 
                dense hide-details label="Acabados" return-object multiple :menu-props="{ maxHeight: '400' }"
              ></v-select>
            </v-col>

             <!-- // ! ANCHO DE ETIQUETA   -->
            <v-col cols="6" class="my-0 py-0" v-if="ACTIVACAMPO">
              <v-text-field 
                v-model="ancho" hide-details dense label="Ancho" 
                outlined color="celeste" 
              />
            </v-col>
             <!-- // ! LARGO DE ETIQUETA   -->
            <v-col cols="6" class="my-0 py-0" v-if="ACTIVACAMPO">
              <v-text-field 
                v-model="largo" hide-details dense label="Largo" 
                outlined color="celeste" 
              />
            </v-col>
             <!-- // ! ESTRUCTURA   -->
            <v-col cols="12" v-if="ACTIVACAMPO" >
              <v-select
                v-model="estructura" :items="estructuras" item-text="nombre" item-value="id" outlined clearable
                dense hide-details label="Estructuras" return-object  color="celeste" 
              ></v-select>
            </v-col>
            <!-- // ! GROSOR   -->
            <v-col cols="12" class="my-0 py-1" v-if="ACTIVACAMPO">
              <v-text-field 
                v-model="grosor" hide-details dense label="Grosor" 
                outlined color="celeste" 
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
        <v-btn color="success" small  @click="validaInformacion()">Guardar </v-btn>
      </v-footer>
   
		  <overlay v-if="overlay"/>
      

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
  import ControlCompromisoVue from '../Compromisos/ControlCompromiso.vue';
	import overlay     from '@/components/overlay.vue'
  
  export default {
    mixins:[metodos],
    props:[
			'modoVista',
      'parametros',
      'depto_id',
      'actualiza'
    ],
    components: {
			overlay,
		},
    data: () => ({
      titulo         : 'IMPRESIÓN DIGITAL',

      tproducto    : { id:1, nombre: 'Producto Existente'},
      tproductos   : [{ id:1, nombre:'Producto Existente'}, 
                      { id:2, nombre:'Modificación de producto'},
                      { id:3, nombre:'Nuevo Producto'}],
      id_partida   : null , // identificador de partida que recibo
      id_caracter  : null,  // identificador de caracteristicas que consulto
      cantidad     : '',
      material     : { id:null, nombre:''},
      materiales   : [],
      material2    : { id:null, nombre:''},
      materiales2  : [],
			referencia   : '',
      acabado      : [],
      acabados     : [],
      pantone      : '',
      pantones     : [],
      estructuras  : [],
      estructura   : { id:null, nombre:''},
      grosor       : '',
      motivo       : '',
      ancho         : '',
      largo         : '',
      acabadosAEliminar: [],
      pantonesAEliminar:[],
      conceptosAEliminar:[],
      // AVISOS
      snackbar      : false,
      text          : '',
      overlay       : false ,
      Correcto      : false,
      textCorrecto  : '',
    }),

    created(){ 
      this.validarModoVista() ;
    },
    computed:{ 
      ...mapGetters('Solicitudes',['consecutivo']),  
      ACTIVACAMPO(){
        return this.tproducto.id === 1 ?  false: true ;
      }
    },
    watch:{ 
      depto_id(){  this.validarModoVista(); } ,
      parametros(){  this.validarModoVista(); } ,
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
          console.log('parametros 1', this.parametros)

        this.limpiarCampos();
        this.consultaMateriales(this.depto_id);
        this.consultaAcabados(this.depto_id);
        this.consultaEstructuras();

        // if(this.modoVista === 1 || this.modoVista === 3){ this.limpiarCampos() };

				if(this.modoVista === 2 ){
          // ASIGNAR VALORES AL FORMULARIO
          this.tproducto    = { id: this.parametros.tproducto },
          this.cantidad     = this.parametros.cantidad
          this.referencia   = this.parametros.referencia;
          this.material     = { id: this.parametros.id_material, nombre:''};
          this.material2    = { id: this.parametros.id_material2, nombre:''};
          this.pantones     = this.parametros.pantones;
          this.acabado      = this.parametros.acabados;
          this.ancho        = this.parametros.ancho;
          this.largo        = this.parametros.largo;
          this.estructura   = { id: this.parametros.estructura};
          this.grosor       = this.parametros.grosor;
          this.tproducto    = { id: this.parametros.tproducto};

        }
        if(this.modoVista === 4){
          this.id_partida = this.parametros.id,
          this.tproducto  = { id: this.parametros.tipo_prod };
          this.cantidad   = this.parametros.cantidad;
          this.referencia = this.parametros.ft;

          if(this.parametros.tipo_prod === 3){ this.consultaCaracteristicas(); };
          if(this.parametros.tipo_prod === 2){ this.consultaModificaciones();  };
        }
      },

      consultaCaracteristicas(){
        this.$http.post('caracteristicas', this.parametros).then(response =>{
          this.id_caracter  = response.body.id
          this.material     = { id: response.body.id_material};
          this.material2    = { id: response.body.det_sobre  };
          this.pantones     = response.body.pantones.map( item =>{ return item.pantone});
          this.acabado      = response.body.acabados;
          this.ancho        = response.body.ancho;
          this.largo        = response.body.largo;
          this.estructura   = { id: response.body.estructura};
          this.grosor       = response.body.grosor;
          this.acabadosAEliminar = response.body.acabados;
          this.pantonesAEliminar = response.body.pantones;
        }).catch( error =>{
          console.log('err', error)
        } )
      },

      consultaModificaciones(){
        this.$http.get('modificaciones/'+ this.parametros.id).then(res =>{
          this.conceptosAEliminar  = []; let acabados = [], pantones =[]; 
          for(let i=0; i< res.body.length; i++){
            this.conceptosAEliminar.push(res.body[i].id);
            res.body[i].concepto === 'Material'            ? this.material   = { id: parseInt(res.body[i].valor)}: '';
            res.body[i].concepto === 'Material Secundario' ? this.material2  = { id: parseInt(res.body[i].valor)}: '';
            res.body[i].concepto === 'Grosor'              ? this.grosor     = res.body[i].valor: '';
            res.body[i].concepto === 'Estructura'          ? this.estructura = { id: parseInt(res.body[i].valor)}: '';   
            res.body[i].concepto === 'Ancho'               ? this.ancho      = res.body[i].valor: '';
            res.body[i].concepto === 'Largo'               ? this.largo      = res.body[i].valor: '';
            if(res.body[i].concepto === 'Pantone' ){ pantones.push( res.body[i].valor) }
            if(res.body[i].concepto === 'Acabado' ){ acabados.push({id: parseInt(res.body[i].valor)})}
          }
            this.pantones = pantones; this.acabado  = acabados;
        }).catch(error =>{
          console.log('error', error)
        })
      },

      validaInformacion(){
        if(this.tproducto.id === 3) {
          if(!this.referencia)     { this.snackbar=true; this.text ="OLVIDASTE LA FICHA TECNICA"               ; return };
          if(!this.cantidad)       { this.snackbar=true; this.text ="OLVIDASTE LA CANTIDAD DEL MATERIAL"       ; return };
          if(!this.material.id)    { this.snackbar=true; this.text ="DEBES SELECCIONAR UN MATERIAL"            ; return };
          if(!this.material2.id)   { this.snackbar=true; this.text ="DEBES SELECCIONAR UN MATERIAL SECUNDARIO" ; return };
          if(!this.pantones.length){ this.snackbar=true; this.text ="DEBES AGREGAR AL MENOS UN PANTONE"        ; return };
          if(!this.acabado.length) { this.snackbar=true; this.text ="DEBES AGREGAR AL MENOS UN ACABADO"        ; return };
          if(!this.ancho)          { this.snackbar=true; this.text ="DEBES AGREGAR EL ANCHO"                   ; return };
          if(!this.largo)          { this.snackbar=true; this.text ="DEBES AGREGAR EL LARGO"                   ; return };
        }else if(this.tproducto.id === 1 || this.tproducto.id === 2){
          if(!this.referencia)     { this.snackbar=true; this.text ="OLVIDASTE LA FICHA TECNICA"               ; return };
          if(!this.cantidad)       { this.snackbar=true; this.text ="OLVIDASTE LA CANTIDAD DEL MATERIAL"       ; return };
        }
        this.PrepararPeticion();
      },

      PrepararPeticion(){
        let payload = {}, id = null, id_sol =  null;
        if(this.modoVista === 4){
          if(this.parametros.estatus > 1 ){
            this.snackbar= true; this.text="Esté producto no se puede actualizar ya que lo estan atendiendo!";
            ; return;
          }
        }

        if(this.modoVista === 1 )  { id = this.consecutivo  ; id_sol = null };
        if(this.modoVista === 2 )  { id = this.consecutivo  ; id_sol = this.parametros.id_solicitud };
        if(this.modoVista === 3 )  { id = this.consecutivo  ; id_sol = this.parametros.id  };
        if(this.modoVista === 4 )  { id = this.parametros.id; id_sol = this.parametros.id_solicitud };

        if(this.tproducto.id === 1){ //! FORMO ARRAY SI ES PRODUCTO EXISTENTE
          payload = { id        : id,
                      id_solicitud    : id_sol,
                      dx        : 3,
                      referencia: this.referencia,
                      tproducto : this.tproducto.id,
                      cantidad  : this.cantidad,
                      id_partida  : this.id_partida,
                      id_caracter : this.id_caracter
                    }
        }

        if(this.tproducto.id === 2 || this.tproducto.id === 3){ //! FORMO ARRAY SI ES UNA MODIFICACION DE PRODUCTO
          payload ={  id                :id,
                      id_solicitud    : id_sol,
                      dx                : 3,
                      referencia        : this.referencia,
                      id_material       : this.material.id,
                      id_material2      : this.material2.id,
                      pantones          : this.pantones,
                      acabados          : this.acabado.length? this.formarObject(this.acabado) : '',
                      grosor            : this.grosor ? this.grosor: '',
                      ancho             : this.ancho,
                      largo             : this.largo,
                      estructura        : this.estructura.id ? this.estructura.id: '',
                      tproducto         : this.tproducto.id,
                      cantidad          : this.cantidad,
                      xmodificar        : this.tproducto.id === 2? this.objetoxModificar(): '',
                      id_partida        : this.id_partida,
                      id_caracter       : this.id_caracter,
                      conceptosAEliminar: this.conceptosAEliminar,
                      pantonesAEliminar : this.pantonesAEliminar,
                      acabadosAEliminar : this.acabadosAEliminar
                    }
        }
        
        // VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
       if(this.modoVista === 1){ this.Crear(payload)      }; // CREAR PRODUCTO EN VUEX
       if(this.modoVista === 2){ this.Actualizar(payload) }; // ACTUALIZAR PRODUCTO EN VUEX
       if(this.modoVista === 3){ this.Añadir_Producto(payload)     }; // 
       if(this.modoVista === 4){ this.Actualizar_Producto(payload) };
      },

      Crear(payload){
        this.dialog = true ; 
        this.agregaProducto(payload).then( res =>{
          if(res){ this.TerminarProceso("El producto se agrego a la lista"); }
        }).finally(()=>{ 
          this.dialog = false
        })
      },

      Añadir_Producto(payload){
        this.overlay = true;
        this.$http.post('anadir.producto.sol', payload).then(response =>{
          this.TerminarProceso(response.bodyText);
        }).catch(error =>{
          console.log('error', error)
        }).finally(()=>{
          this.overlay = false;
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

      Actualizar_Producto(payload){
        this.overlay = true;
        this.$http.post('actualiza.producto',payload).then( res =>{
          if(res){ this.TerminarProceso(res.bodyText); }
        }).finally(()=>{ 
          this.overlay = false
        })
      },

			TerminarProceso(mensaje){
        var me = this ;
        this.Correcto = true ; this.textCorrecto = mensaje;
        setTimeout(()=>{ me.$emit('modal',false); me.$emit('put',!this.actualiza)}, 2000);
        // this.limpiarCampos();  //LIMPIAR FORMULARIO
      },

      limpiarCampos(){
        this.referencia   = '';
        this.material     = { id:null, nombre:''};
        this.material2    = { id:null, nombre:''};
        this.estructura   = { id:null, nombre:''};
        this.tproducto    = { id:1};
        this.cantidad     = '';
        this.pantone      = '';
        this.pantones     = []
        this.acabado      = [];
        this.ancho        = '';
        this.largo        = '';
        this.grosor       = ''
      },

      objetoxModificar(){
        let id = null, id_sol = null;
        if(this.modoVista === 1 )  { id = this.consecutivo  ; id_sol = null };
        if(this.modoVista === 2 )  { id = this.consecutivo  ; id_sol = this.parametros.id_solicitud };
        if(this.modoVista === 3 )  { id = this.consecutivo  ; id_sol = this.parametros.id  };
        if(this.modoVista === 4 )  { id = this.parametros.id; id_sol = this.parametros.id_solicitud };

        let payload = { id             : id,
                        id_solicitud         : id_sol,
                        dx             : 3,
                        referencia     : this.referencia,
                        tproducto      : this.tproducto.id,
                        cantidad       : this.cantidad,
                        xmodificar     : this.agregaConceptos(),
                      }
        return payload;
      },

      agregaConceptos(){
        let arrayTemp = [];

        this.material.id     ? arrayTemp.push( { tipo:1 ,concepto:'Material'            , valor: this.material.id    }): '';  
        this.material2.id    ? arrayTemp.push( { tipo:1 ,concepto:'Material Secundario' , valor: this.material2.id   }): ''; 
        this.pantones.length ? arrayTemp.push( { tipo:2 ,concepto:'Pantone'             , valor: this.pantones       }): ''; 
        this.acabado.length  ? arrayTemp.push( { tipo:2 ,concepto:'Acabado'             , valor: this.formarObject(this.acabado) }): '';
        this.grosor          ? arrayTemp.push( { tipo:1 ,concepto:'Grosor'              , valor: this.grosor         }): ''; 
        this.ancho           ? arrayTemp.push( { tipo:1 ,concepto:'Ancho'               , valor: this.ancho          }): ''; 
        this.largo           ? arrayTemp.push( { tipo:1 ,concepto:'Largo'               , valor: this.largo          }): ''; 
        this.estructura.id   ? arrayTemp.push( { tipo:1 ,concepto:'Estructura'          , valor: this.estructura.id  }): 0 ;

        return arrayTemp;
      },

      formarObject(items){
        let arrayTemp = [];
        for(let i=0; i< items.length;i++){
          arrayTemp.push(items[i].id)
        }

        return arrayTemp;
      }

    }
  }
</script>

<style scoped>
  
</style>
