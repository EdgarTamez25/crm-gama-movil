<template>
  <v-app id="inspire">
    
    <!-- MENU -->
    <v-navigation-drawer left app v-model="drawer" v-ripple dark v-if="Logeado">

      <v-list dense nav class="py-0 white--text" >
        <v-list-item two-line>
          <v-list-item-avatar >
            <img src="@/assets/person.png" >
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title > {{ getUsuarios.nombre }}</v-list-item-title>
            <v-list-item-subtitle>{{ getUsuarios.nivel ===3? 'Vendedor':'Chofer'}}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
      </v-list>
      
      <v-list dense>
        <template v-for="item in items">
          <v-list-item
            v-for="(child, i) in item.children"
            :key="i"
            :to="child.path"
            v-if="getUsuarios.nivel === 3?getUsuarios.nivel === child.nivel.vend : child.nivel.chofer"
          >
            <v-list-item-action v-if="child.icon">
              <v-icon>{{ child.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content >
              <v-list-item-title >
                {{ child.text }}
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>
      
    <!-- NAVBAR -->
    <v-app-bar app color="rosa" dark  v-if ="Logeado">
      <img src="@/assets/logo.png" height="30" @click.stop="drawer = !drawer">
      <v-spacer></v-spacer>
      <v-btn icon @click="salir" >
        <v-icon>exit_to_app</v-icon>
      </v-btn>
    </v-app-bar>

    <v-content class="">
      <v-container >
        <router-view v-if="Logeado"/>

        <v-row col="12"  class="pa-3"  v-if="!Logeado">
          <v-row class="pa-3">
            <v-col cols="12">

              <v-snackbar v-model="snackbar" :timeout="3000" top color= "red darken-4" > {{ text }}
                <v-btn color="white" text @click="snackbar = false"> <v-icon>clear</v-icon></v-btn>
              </v-snackbar>

              <v-row col="12" justify="center" class="pa-3" >

                <v-col cols="12">
                  <v-col cols="12" class="text-center my-5"> <!-- LOGO DE LA VISTA -->
                    <img src="@/assets/logo2.png" width="120" height="100%"  > <br>
                  </v-col>

                  <v-form v-model="valid" :lazy-validation="lazy">
                    <v-text-field  
                      label="Usuario" :rules="usarioRules" v-model="correo" append-icon="person" color="rosa" clearable outlined dense > <!-- CORREO -->
                    </v-text-field>

                    <v-text-field
                      :append-icon="show ? 'visibility' : 'visibility_off'"
                      :type="show ? 'text' : 'password'"
                      name="input-10-2"
                      label="Contraseña"
                      v-model="contrasenia"
                      @click:append="show = !show"
                      clearable
                      outlined
                      dense
                      color="rosa"
                      :rules="contraRules"
                    ></v-text-field> <!-- CONTRASEÑA -->
                  </v-form>
                  <v-card-actions> <!-- INICIAR SESION -->
                    <v-spacer></v-spacer>
                    <v-btn small color="celeste white--text"  :disabled="!valid" @click="iniciarSesion">Iniciar Sesión</v-btn> 
                  </v-card-actions>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-row>

        <v-footer absolute class="rosa" dark v-if="!Logeado" >
          <v-col class="text-center" cols="12" > <strong>Gama Etiquetas</strong>  —  {{ new Date().getFullYear() }}</v-col>
        </v-footer>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  import {mapGetters, mapActions} from 'vuex'
  export default {
    name: 'App',
    components:{
    },
    data: () => ({
      valid:true,
      lazy:false,
      correo: '',
      contrasenia: '',
      // ALERTAS
      snackbar: false,
      text: '',
      show: false,
      drawer: false,
      usarioRules: [v => !!v || 'Es requerido'],
      contraRules: [v => !!v || 'Es requerido'],
      items: [
        {
          icon: 'store',
          text: 'Administración',
          model: false,
          children: [
            { text: 'Inicio'                ,path: '/'                , icon: 'home'                ,nivel:{vend:3, chofer:true}},
            { text: 'Compromisos'           ,path: 'compromisos'      , icon: 'chrome_reader_mode'  ,nivel:{vend:3, chofer:false}},
            { text: 'Proyectos Cotizados'   ,path: 'fases.venta'      , icon: 'business_center'     ,nivel:{vend:3, chofer:false}},
            { text: 'Pendientes'            ,path: 'pendientes'       , icon: 'calendar_today'      ,nivel:{vend:3, chofer:false}},
            { text: 'Compromisos Realizados',path:'compromisos.hechos', icon:'assignment_turned_in' ,nivel:{vend:3, chofer:false} },
            { text: 'Entregas'              ,path:'entregas'          , icon:'airport_shuttle'      ,nivel:{vend:false, chofer:true}  }
          ],
        },
      ],
    }),

    created(){
      const n =  new Date(); var y = n.getFullYear(); var m = n.getMonth() + 1; var d = n.getDate();
      var hora = n.getHours(); var minutos = n.getMinutes();
      if(hora<10){ hora = '0'+ hora }
      if(minutos<10){ minutos = '0'+ minutos}

    },

    computed:{
			// IMPORTANDO USO DE VUEX - CLIENTES (GETTERS)
      ...mapGetters('Usuarios',['getUsuarios']),
			...mapGetters('Usuarios',['Logeado']),
		},

    methods:{
      // IMPORTANDO USO DE VUEX - CLIENTES(ACCIONES)
      ...mapActions('Usuarios' ,['Login','Salir','Logear']),
      
      iniciarSesion(){
        var md5 = require('md5');
        var usuario = { correo: this.correo, contrasenia: md5(this.contrasenia) }
      
        this.Login(usuario).then((response)=>{
          if(response){
            if(this.getUsuarios.nivel === 3){
              this.drawer= false;
              this.Logear(true);
              this.$router.push({ name: 'compromisos'})
              
            }else if(this.getUsuarios.nivel === 4){
              this.drawer= false;
              this.Logear(true);
              this.$router.push({ name: 'entregas'})
            }else{
              this.snackbar = true; this.text="Tu nivel no tiene acceso a la aplicación"; return;
            }
          }else{
            this.snackbar = true; this.text="Está cuenta no se encuentra registrada."; return;
          }
        }).catch(err =>{
          console.log('err', err)
        })
      },

      salir(){
        this.Salir();
      },

    }
  };
</script>
