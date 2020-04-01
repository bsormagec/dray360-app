
import Vue from 'vue'
import Vuetify from 'vuetify'
import { render as VTLRender } from '@testing-library/vue'
import authTestUser from '@/utils/for_tests/auth_test_user'

/*
  There's a known issue that is happening with vue-test-utils and vuetify
  https://github.com/vuejs/vue-test-utils/issues/1407

  it throws the folling warning: v-data-table causing vue warn infinite update loop in component render function
  that's why we're setting Vue.config.silent = true (that removes all Vue's warnings)
*/

Vue.config.silent = true
Vue.use(Vuetify)

export default async (component, options, callback, shouldAuth = true) => {
  if (shouldAuth) await authTestUser()
  return VTLRender(
    {
      render (createElement) {
        return createElement('div', { attrs: { 'data-app': true } }, [
          createElement(component)
        ])
      }
    },
    { vuetify: new Vuetify(), ...options },
    callback
  )
}
