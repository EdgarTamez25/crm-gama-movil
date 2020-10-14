<template>
  <!-- <v-main class="pa-0"> -->
    <v-row justify="center">
      <v-col cols="12" align="center"> 
        <v-form ref="form" v-model="valid" > 
          <v-row >
            <v-card-text class="font-weight-black pa-1 body-1">{{ titulo }}</v-card-text>
            <v-col cols="12" >
              <v-select
                v-model="material" :items="materiales" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Materiales" return-object placeholder="Materiales" :rules="materialRules"
              ></v-select> 
            </v-col>
              <v-card-text class="font-weight-black pa-1">PRESENTACIÓN</v-card-text>
             <!-- // !INPUT PARA PANTONE  -->
            <v-col cols="9"  align="center" class="my-0 py-0">
              <v-text-field 
                v-model="pantone" label="Pantone " placeholder="Pantone" 
                outlined dense hide-details :rules="pantoneRules"
              ></v-text-field>
            </v-col>
             <!-- // !BOTON DE AGREGAR PANTONE  -->
            <v-col cols="3" class="text-right my-0 py-0">
              <v-btn color="celeste" dark @click="agregarPantone()"> <v-icon>add</v-icon> </v-btn>
            </v-col>
             <!-- // !CHIPS DE PANTONES   -->
            <v-col cols="12" class="my-0 py-0 text-left">
              <v-chip v-for="(item, i) in pantones" :key="i"
                class="ma-2" close :color="item" outlined  @click:close="eliminaPanton(i)">
                {{ item }}
              </v-chip>
            </v-col>
            <v-col cols="12" >
              <v-select
                v-model="acabado" :items="acabados" item-text="nombre" item-value="id" outlined color="celeste"
                dense hide-details label="Acabados" return-object multiple :rules="acabadoRules" :menu-props="{ maxHeight: '400' }"
              ></v-select>
            </v-col>
          </v-row>
        </v-form>
      </v-col>

      <v-col cols="4" sm="3" lg="1" v-for="(item,i) in orientacion" :key="i" class=" my-0 py-1">
        <v-card  shaped outlined>
          <v-img height="100px" contain :src="item.img" ></v-img>
          <v-checkbox 
            v-model="item.activo"
            :label="item.nombre" 
            color="red" 
            hide-details 
            @click="evaluaCheck(item.id)"
          ></v-checkbox>
        </v-card>
      </v-col>

      <v-col cols="12" class="my-3"/>

      <!-- CONTENEDOR DE CIERRE Y PROCESOS -->
      <v-footer absolute >
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
			// 'modoVista',
      // 'parametros',
      'depto_id'
	  ],
    data: () => ({
      titulo       : 'CARACTERÍSTICAS',
      valid        : true,
      material     : { id:null, nombre:''},
      materiales   : [],
      acabado      : [],
      acabados     : [],
      acabadoRules : [v => !!v || 'Es requerido'],
      materialRules: [v => !!v || 'Es requerido'],
      otroRules    : [v => !!v || 'Es requerido'],
      pantoneRules : [v => !!v || 'Es requerido'],
      correoRules  : [v => !!v || 'Es requerido'],
      motivoRules  : [v => !!v || 'Es requerido'],
      otro         : '',
      pantone      : '',
      pantones     : [],
      correo       : '',
      motivo       : '',
      checkActivo  : 0,
      orientacion:[ { id:1, nombre:'1',activo: false, img:'ico-flexo/1.svg' },
                    { id:2, nombre:'2',activo: false, img:'ico-flexo/2.svg' },
                    { id:3, nombre:'3',activo: false, img:'ico-flexo/3.svg' },
                    { id:4, nombre:'4',activo: false, img:'ico-flexo/4.svg' },
                    { id:5, nombre:'5',activo: false, img:'ico-flexo/5.svg' },
                    { id:6, nombre:'6',activo: false, img:'ico-flexo/6.svg' },
                    { id:7, nombre:'7',activo: false, img:'ico-flexo/7.svg' },
                    { id:8, nombre:'8',activo: false, img:'ico-flexo/8.svg' },
                  ],

      dialog       : false,
      textDialog   : "Guardando Información",
      Correcto     : false,
      textCorrecto : '',
    }),

    created(){ 
      this.validarModoVista() ;
    },
    // computed:{ ...mapGetters('Login'    ,['getdatosUsuario']),  },
    watch:{ 
      depto_id: function(){ 
        this.validarModoVista(); 
      } 
    }, 

    methods:{
        // ...mapActions('LLamadas'  ,['consultarLlamadas']), // IMPORTANDO USO DE VUEX (ACCIONES)
      evaluaCheck(id){
        for(let i=0; i< this.orientacion.length;i++){
          id != this.orientacion[i].id? this.orientacion[i].activo=false: this.orientacion[i].activo=true;
        }
        this.checkActivo = id;
      },

      agregarPantone(){
        var esHexadecimal = false;

        if( esHexadecimal = this.esHexadecimal(this.pantone) ){
        console.log('esHexadecimal', esHexadecimal)
          this.pantones.push(this.pantone);
          this.pantone = '';
        }
        
        
      },

      esHexadecimal(pantone){
        return /^#[0-9A-F]+$/i.test(pantone);
      },

      eliminaPanton(i){
        this.pantones.splice(i,1);
      },

      validarModoVista(){ 
        this.consultaMateriales(this.depto_id);
        this.consultaAcabados(this.depto_id);

				if(this.modoVista === 2 ){
          // ASIGNAR VALORES AL FORMULARIO
          // this.nombre   = this.parametros.nombre;
          // this.dedonde  = this.parametros.dedonde
          // this.telefono = this.parametros.telefono;
          // this.correo   = this.parametros.correo;
          // this.motivo   = this.parametros.motivo;
				}else{
				  this.limpiarCampos()
				}
      },

      PrepararPeticion(){
        const payload = { nombre  : this.nombre,
                          dedonde : this.dedonde,
                          telefono: this.telefono? this.telefono=this.telefono: this.telefono = '',
                          correo  : this.correo? this.correo=this.correo: this.correo = '',
                          motivo  : this.motivo,
                          fecha   : this.traerFechaActual(),
                          hora    : this.traerHoraActual(),
                          id_sucursal: this.getdatosUsuario.id_sucursal
                        }
        // VALIDO QUE ACCION VOY A EJECUTAR SEGUN EL MODO DE LA VISTA
				this.modoVista === 1 ? this.Crear(payload): this.Actualizar(payload);
        
      },
      Crear(payload){
        this.dialog = true ; setTimeout(() => (this.dialog = false), 2000) 
        this.$http.post('add.llamada', payload).then(response =>{
          if(!response.body.length){
            this.snackbar=true; this.color ="error";
            this.text = "Ocurrio un problema, intentelo mas tarde."
            return
          }
          this.TerminarProceso(response.body);
        })
      },

      Actualizar(payload){
				this.dialog = true ; this.textDialog ="Actualizando Información";
				setTimeout(() => (this.dialog = false), 2000);
        this.$http.put('put.llamada/'+ this.parametros.id, payload).then((response)=>{
          if(!response.body.length){
            this.snackbar=true; this.color ="error";
            this.text = "Ocurrio un problema, intentelo mas tarde."
            return
          }
					this.TerminarProceso(response.body);					
				})
			},

			TerminarProceso(mensaje){
        var me = this ;
        this.dialog = false; this.Correcto = true ; this.textCorrecto = mensaje;
        setTimeout(function(){ me.$emit('modal',false)}, 2000);
        setTimeout(function(){ me.$emit('recarga',!this.modoVista)},500);
        this.limpiarCampos();  //LIMPIAR FORMULARIO
        this.consultarLlamadas(this.getdatosUsuario.id_sucursal);
      },

      limpiarCampos(){
        this.id_material   = '';
        this.dedonde  = '';
        this.telefono = '';
        this.correo   = '';
        this.motivo   = '';
      }
    }
  }
</script>
