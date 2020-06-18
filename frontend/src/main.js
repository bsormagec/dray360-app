import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store/index'
import vuetify from './plugins/vuetify'
import * as Sentry from '@sentry/browser'
import { Vue as VueIntegration } from '@sentry/integrations'
Vue.config.productionTip = false

Sentry.init({
  dsn: 'https://8084d41e203242d381651d73ddbe7831@o182038.ingest.sentry.io/5281643',
  integrations: [new VueIntegration({ Vue, attachProps: true })]
})

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
