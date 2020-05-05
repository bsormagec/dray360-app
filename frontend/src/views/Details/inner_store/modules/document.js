import Vue from 'vue'

const state = Vue.observable({
  document: []
})

const methods = {
  setDocument (newDocument) {
    state.document = newDocument
  },

  setDocumentFieldEdit ({ value, location }) {
    const parts = location.split('/')
    Vue.set(state.document[parts[0]][parts[1]][parts[2]], 'edit', value)
  }
}

export default {
  state,
  methods
}
