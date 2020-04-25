import Vue from 'vue'
import Vuex from 'vuex'

import Usuarios from '@/modules/Usuarios';
import Compromisos from '@/modules/Compromisos';

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
    Compromisos
  }
})
