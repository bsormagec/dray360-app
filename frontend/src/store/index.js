import Vue from 'vue'
import Vuex from 'vuex'
import orders from '@/store/modules/orders'
import address from '@/store/modules/address'
import auth from '@/store/modules/auth'
import companies from '@/store/modules/companies'
import rulesEditor from '@/store/modules/rules_editor'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    [orders.moduleName]: orders,
    [address.moduleName]: address,
    [auth.moduleName]: auth,
    [rulesEditor.moduleName]: rulesEditor,
    [companies.moduleName]: companies
  }
})
