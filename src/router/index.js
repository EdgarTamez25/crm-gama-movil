import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Compromisos from '@/views/Compromisos/Compromisos.vue';

Vue.use(VueRouter)

var router = new VueRouter({
  mode: process.env.CORDOVA_PLATFORM ? 'hash' : 'history',
  base: process.env.BASE_URL,
  routes : [
    { path: '/',  name: 'Home',  component: Home },
    { path: '/compromisos',  name: 'compromisos',  component: Compromisos },
  ]
})

// router.beforeEach( (to, from, next) => {
//   // console.log(store.state.Login.datosUsuario.nivel)
//   // infica a que ruta voy a acceder
//   // matched.some = compara si el meta es libre
//   if(to.matched.some(record => record.meta.libre)){
//     next()
//   }else if(store.state.Login.datosUsuario.nivel){
//     if(to.matched.some(record => record.meta.ADMIN)){
//       next()
//     }
//   }else{
//     next({
//       name: 'Login'
//     })
//   }
// })


export default router
