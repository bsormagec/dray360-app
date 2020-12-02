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
        // primary: '#326295',
        // secondary: '#5F7F00',
        primary: '#003C71',
        secondary: '#61788A',
        warning: '#cc904c',
        error: '#FF5252'
      }
    },
    options: {
      customProperties: true
    }
  }
})
