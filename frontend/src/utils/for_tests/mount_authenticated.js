import Vue from 'vue'
import Vuex from 'vuex'
import Vuetify from 'vuetify'
import { mount } from '@vue/test-utils'
import authTestUser from '@/utils/for_tests/auth_test_user'

Vue.config.silent = true
Vue.use(Vuex)
Vue.use(Vuetify)

const appendDataApp = () => {
  const el = document.createElement('div')
  el.setAttribute('data-app', true)
  document.body.appendChild(el)
}

export default async (component, options, shouldAuth = true) => {
  appendDataApp()
  if (shouldAuth) await authTestUser()
  const vuexStore = options.store ? new Vuex.Store(options.store) : {}

  return mount(component, {
    ...options,
    store: vuexStore,
    localVue: Vue,
    vuetify: new Vuetify()
  })
}
