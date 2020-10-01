import Vue from 'vue'
import Vuex from 'vuex'
import orders from '@/store/modules/orders'
import address from '@/store/modules/address'
import auth from '@/store/modules/auth'
import companies from '@/store/modules/companies'
import rulesEditor from '@/store/modules/rules_editor'
import userDashboard from '@/store/modules/users'
import utils from '@/store/modules/utils'
import accesorialmapping from '@/store/modules/accesorialmapping'
import profile from '@/store/modules/profile'
import orderForm from '@/store/modules/order-form'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    [orders.moduleName]: orders,
    [address.moduleName]: address,
    [auth.moduleName]: auth,
    [rulesEditor.moduleName]: rulesEditor,
    [userDashboard.moduleName]: userDashboard,
    [companies.moduleName]: companies,
    [utils.moduleName]: utils,
    [accesorialmapping.moduleName]: accesorialmapping,
    [profile.moduleName]: profile,
    [orderForm.moduleName]: orderForm
  }
})
