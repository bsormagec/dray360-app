import Vue from 'vue'
import Vuex from 'vuex'
import orders from '@/store/modules/orders'
import auth from '@/store/modules/auth'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    [orders.moduleName]: orders,
    [auth.moduleName]: auth
  }
})
