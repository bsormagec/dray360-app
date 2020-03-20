import Vue from 'vue'
import Vuex from 'vuex'

import orders from '@/store/modules/orders'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    orders
  }
})
