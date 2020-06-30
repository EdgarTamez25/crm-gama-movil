import Vue from 'vue'
import Vuex from 'vuex'

import Usuarios from '@/modules/Usuarios';
import Compromisos from '@/modules/Compromisos';
// import Rutas from '@/modules/Rutas';
import Entregas from '@/modules/Entregas';
import Prospectos from '@/modules/Prospectos';


Vue.use(Vuex)

export default new Vuex.Store({
  state: {
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    Usuarios,
    Compromisos,
    // Rutas,
    Entregas,
    Prospectos
  }
})
