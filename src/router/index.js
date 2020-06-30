import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Compromisos from '@/views/Compromisos/Compromisos.vue';
import Det_Compromiso from '@/views/Compromisos/Det_Compromiso.vue';
import Compromisos_Hechos from '@/views/Compromisos/compromisos_hechos.vue';
import Pendientes from '@/views/Pendientes/Pendientes.vue';

import FaseVenta from '@/views/FaseVenta/fasesVenta.vue';
import Entregas from '@/views/Entregas/Entregas.vue';
import DetEntrega from '@/views/Entregas/DetEntrega.vue';
import Prospectos from '@/views/Prospectos/Prospectos.vue';

Vue.use(VueRouter)

var router = new VueRouter({
  mode: process.env.CORDOVA_PLATFORM ? 'hash' : 'history',
  base: process.env.BASE_URL,
  routes : [
    { path: '/'                  ,  name: 'Home'              ,  component: Home },
    { path: '/compromisos'       ,  name: 'compromisos'       ,  component: Compromisos },
    { path: '/det_compromiso'    ,  name: 'det_compromiso'    ,  component: Det_Compromiso },
    { path: '/compromisos.hechos',  name: 'compromisos.hechos',  component: Compromisos_Hechos },
    { path: '/pendientes'        ,  name: 'pendientes'        ,  component: Pendientes },
    { path: '/fases.venta'       ,  name: 'fases.venta'       ,  component: FaseVenta },
    { path: '/entregas'          ,  name: 'entregas'          ,  component: Entregas },
    { path: '/det.entrega'       ,  name: 'det.entrega'       ,  component: DetEntrega },
    { path: '/prospectos'        ,  name: 'prospectos'          ,  component: Prospectos },


  ]
})




export default router
