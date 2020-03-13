<template>
  <v-app id="inspire">
    <v-app-bar app color="cyan darken-4" dark  >
       <v-app-bar-nav-icon @click.stop="drawer = !drawer" ></v-app-bar-nav-icon>
      <v-spacer></v-spacer>
     <img src="@/assets/logo.png" height="30px">
      
    </v-app-bar>

    <v-content class="ma-3">
      <router-view/>
      <HOME/>
    </v-content>

  </v-app>
</template>

<script>
import HOME from '@/views/Home.vue'
export default {
  name: 'App',
  components:{
    HOME
  },
  data: () => ({
    //
  }),

  created(){
    // this.$router.push({ name: 'Home'})
  }
};
</script>
