import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import Vuetify from 'vuetify'
import { mount } from 'vue-test-utils-champi'
import authTestUser from '@/utils/for_tests/auth_test_user'

Vue.config.silent = true
Vue.use(VueRouter)
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
    router: new VueRouter({
      mode: 'history',
      routes: [
        {
          path: '/test',
          name: 'test',
          component
        }
      ]
    }),
    vuetify: new Vuetify(),
    store: vuexStore,
    localVue: Vue,
    sync: false
  })
}
