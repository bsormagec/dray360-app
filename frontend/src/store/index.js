import Vue from 'vue'
import Vuex from 'vuex'
import orders from '@/store/modules/orders'
import auth from '@/store/modules/auth'
import rulesEditor from '@/store/modules/rules_editor'
import userDashboard from '@/store/modules/users'
import utils from '@/store/modules/utils'
import profile from '@/store/modules/profile'
import orderForm from '@/store/modules/order-form'
import requestsList from '@/store/modules/requests-list'
import fieldMaps from '@/store/modules/field_maps'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    [orders.moduleName]: orders,
    [auth.moduleName]: auth,
    [rulesEditor.moduleName]: rulesEditor,
    [userDashboard.moduleName]: userDashboard,
    [utils.moduleName]: utils,
    [profile.moduleName]: profile,
    [orderForm.moduleName]: orderForm,
    [requestsList.moduleName]: requestsList,
    [fieldMaps.moduleName]: fieldMaps,
  }
})
