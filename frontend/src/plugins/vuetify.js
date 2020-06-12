import Vue from 'vue'
import Vuetify from 'vuetify/lib'

Vue.use(Vuetify)

/*
  Reference assets/styles/variables.scss
*/
export default new Vuetify({
  theme: {
    themes: {
      light: {
        primary: '#397E92',
        secondary: '#5F7F00'
      }
    }
  }
})
