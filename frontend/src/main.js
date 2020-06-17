import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store/index'
import vuetify from './plugins/vuetify'
import * as Sentry from '@sentry/browser'
import { Vue as VueIntegration } from '@sentry/integrations'
Vue.config.productionTip = false

Sentry.init({
  dsn: 'https://8a6b06b12891489eac4e8a979d2d31db@o408962.ingest.sentry.io/5280608',
  integrations: [new VueIntegration({ Vue, attachProps: true })]
})

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
