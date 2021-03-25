import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store/index'
import vuetify from './plugins/vuetify'
import * as Sentry from '@sentry/browser'
import { Vue as VueIntegration } from '@sentry/integrations'
import setupInterceptors from '@/store/api_calls/config/setupInterceptors'
import VueClipboard from 'vue-clipboard2'
import Echo from 'laravel-echo'
import pusher from 'pusher-js'
import toBool from '@/utils/to_bool'
import axios from '@/store/api_calls/config/axios'

Vue.config.productionTip = false

VueClipboard.config.autoSetContainer = true // add this line
Vue.use(VueClipboard)

if (process.env.NODE_ENV !== 'development') {
  Sentry.init({
    dsn: 'https://8084d41e203242d381651d73ddbe7831@o182038.ingest.sentry.io/5281643',
    integrations: [new VueIntegration({ Vue, attachProps: true })]
  })
}

setupInterceptors({ store, router })

window.Pusher = pusher
const echo = new Echo({
  broadcaster: 'pusher',
  authEndpoint: '/api/broadcasting/auth',
  key: process.env.VUE_APP_PUSHER_APP_KEY,
  wsHost: window.location.hostname,
  wsPort: process.env.VUE_APP_WEBSOCKETS_PORT,
  forceTLS: toBool(process.env.VUE_APP_WEBSOCKETS_TLS),
  authorizer: (channel, options) => {
    return {
      authorize: (socketId, callback) => {
        axios.post('/api/broadcasting/auth', {
          socket_id: socketId,
          channel_name: channel.name
        }).then(response => {
          callback(false, response.data)
        })
          .catch(error => {
            callback(true, error)
          })
      }
    }
  },
})

Vue.prototype.$echo = echo

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')

const addDraggableToDialogs = function () { // make vuetify dialogs movable
  const d = {}
  document.addEventListener('mousedown', e => {
    const closestDialog = e.target.closest('.v-dialog.v-dialog--active')
    const parent = e.target.parentNode
    const grandParent = parent.parentNode
    let hasVCardTitle = e.target.classList.contains('v-card__title')
    let isAddressRecognized = e.target.classList.contains('recognized__address')

    if (parent) {
      hasVCardTitle = hasVCardTitle || parent.classList.contains('v-card__title')
      isAddressRecognized = isAddressRecognized || parent.classList.contains('recognized__address')
    }

    if (grandParent) {
      hasVCardTitle = hasVCardTitle || grandParent.classList.contains('v-card__title')
      isAddressRecognized = isAddressRecognized || grandParent.classList.contains('recognized__address')
    }

    if (e.button === 0 && closestDialog != null && hasVCardTitle && !isAddressRecognized) { // element which can be used to move element
      d.el = closestDialog // element which should be moved
      d.mouseStartX = e.clientX
      d.mouseStartY = e.clientY
      d.elStartX = d.el.getBoundingClientRect().left
      d.elStartY = d.el.getBoundingClientRect().top
      d.el.style.position = 'fixed'
      d.el.style.margin = 0
      d.oldTransition = d.el.style.transition
      d.el.style.transition = 'none'
    }
  })
  document.addEventListener('mousemove', e => {
    if (d.el === undefined) return
    d.el.style.left = Math.min(
      Math.max(d.elStartX + e.clientX - d.mouseStartX, 0),
      window.innerWidth - d.el.getBoundingClientRect().width
    ) + 'px'
    d.el.style.top = Math.min(
      Math.max(d.elStartY + e.clientY - d.mouseStartY, 0),
      window.innerHeight - d.el.getBoundingClientRect().height
    ) + 'px'
  })
  document.addEventListener('mouseup', () => {
    if (d.el === undefined) return
    d.el.style.transition = d.oldTransition
    d.el = undefined
  })
  setInterval(() => { // prevent out of bounds
    const dialog = document.querySelector('.v-dialog.v-dialog--active')
    if (dialog === null) return
    dialog.style.left = Math.min(parseInt(dialog.style.left), window.innerWidth - dialog.getBoundingClientRect().width) + 'px'
    dialog.style.top = Math.min(parseInt(dialog.style.top), window.innerHeight - dialog.getBoundingClientRect().height) + 'px'
  }, 100)
}
addDraggableToDialogs()
