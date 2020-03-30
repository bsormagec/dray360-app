import Vue from 'vue'
import Vuetify from 'vuetify'
import { render } from '@testing-library/vue'

/*
  There's a known issue that is happening with vue-test-utils and vuetify
  https://github.com/vuejs/vue-test-utils/issues/1407

  it throws the folling warning: v-data-table causing vue warn infinite update loop in component render function
  that's why we're setting Vue.config.silent = true (that removes all Vue's warnings)
*/

Vue.config.silent = true
Vue.use(Vuetify)

const vuetify = new Vuetify()
export default (component, options) => render(component, { ...options, vuetify })
