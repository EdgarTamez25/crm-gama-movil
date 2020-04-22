<template>
  <v-app id="inspire">
    
    <!-- MENU -->
    <v-navigation-drawer left app v-model="drawer" v-ripple dark v-if="logeado">

      <v-list dense nav class="py-0 white--text" >
        <v-list-item two-line>
          <v-list-item-avatar >
            <img src="@/assets/person.png" >
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title >Edgar Geovani Tamez García</v-list-item-title>
            <v-list-item-subtitle>Administrador</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
      </v-list>
      
      <v-list dense>
        <template v-for="item in items">
          <v-layout
            v-if="item.heading"
            :key="item.heading"
            align-center
          >
          </v-layout>

          <!-- esto va en el list de abajov-if="Nivel === child.nivel" -->
          <v-list-item
            v-for="(child, i) in item.children"
            :key="i"
            :to="child.path"
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

      <!-- SALIR -->
      <v-divider></v-divider>

      <v-list-item>
        <v-list-item-action>
          <v-icon>exit_to_app</v-icon>
        </v-list-item-action>
        <v-list-item-content >
          <v-list-item-title @click="logeado = false">
            <h5>Salir</h5>
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-navigation-drawer>
      
    <!-- NAVBAR -->
    <v-app-bar app color="rosa" dark  v-if ="logeado">
       <v-app-bar-nav-icon @click.stop="drawer = !drawer" ></v-app-bar-nav-icon>
      <v-spacer></v-spacer>
     <img src="@/assets/logo.png" height="30px">
      
    </v-app-bar>

    <v-content >
      <v-container >
        <router-view v-if="logeado"/>

        <v-row col="12"  class="pa-3"  v-if="!logeado">
          <v-row class="pa-3">
            <v-col cols="12">

              <v-snackbar v-model="snackbar" :timeout="3000" color= "red darken-4" > {{ text }}
                <v-btn color="white" text @click="snackbar = false"> Cerrar</v-btn>
              </v-snackbar>

              <v-row col="12" justify="center" class="pa-3" >

                <v-col cols="12">
                  <v-col cols="12" class="text-center my-5"> <!-- LOGO DE LA VISTA -->
                    <img src="@/assets/logo.png" width="120" height="100%"  > <br>
                      <strong> CRM 2020 <br>  GAMA ETIQUETAS </strong>
                  </v-col>

                  <v-text-field  
                    label="Correo" v-model="correo" append-icon="person" color="rosa" clearable outlined dense > <!-- CORREO -->
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
                  ></v-text-field> <!-- CONTRASEÑA -->

                  <v-card-actions> <!-- INICIAR SESION -->
                    <v-spacer></v-spacer>
                    <!-- <v-btn  small outlined color="primary" @click="step=2">Registrarse</v-btn> -->
                    <v-btn small  color="celeste" dark  class="elevation-5" @click="iniciarSesion">Iniciar Sesión</v-btn> 
                  </v-card-actions>
                  
                  <v-card-actions> <!-- OLVIDE CONTRASEÑA-->
                    <v-spacer></v-spacer>
                    <v-btn x-small="" color="gris"  text >Olvide mi contraseña.</v-btn>
                  </v-card-actions>

                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-row>

        <v-footer absolute class="rosa" dark v-if="!logeado">
          <v-col class="text-center" cols="12" > <strong>Gama Etiquetas</strong>  —  {{ new Date().getFullYear() }}</v-col>
        </v-footer>

      </v-container>
    </v-content>

  </v-app>
</template>

<script>
// import HOME from '@/views/Home.vue'
export default {
  name: 'App',
  components:{
  },
  data: () => ({
     logeado   :false,
     correo: '',
     contrasenia: '',

      // ALERTAS
      snackbar: false,
      text: '',
      show: false,
      drawer: false,

      items: [
        {
          icon: 'store',
          text: 'Administración',
          model: false,

          children: [
            { text: 'Inicio'      ,path: '/'         , icon: 'home'          , nivel: 'Usuario'},
            { text: 'Compromisos' ,path: 'compromisos'  , icon: 'chrome_reader_mode'          , nivel: 'Usuario'},

          ],
        },
      ],
  }),

  created(){
    // this.$router.push({ name: 'Home'})
  },

  methods:{
    iniciarSesion(){
      this.logeado = true;
      this.drawer= false;
    }
  }
};
</script>
