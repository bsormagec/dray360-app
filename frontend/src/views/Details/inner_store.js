import Vue from 'vue'

export const detailsState = Vue.observable({
  form: {},
  isEditing: true
})

export const detailsMethods = {
  setForm (newForm) {
    detailsState.form = newForm
  },
  setFormFieldValue ({ value, location }) {
    const parts = location.split('/')

    if (location.includes('rootFields')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]], 'value', value)
    } else if (location.includes('subSections')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]], 'value', value)
    }
  },
  toggleIsEditing () {
    detailsState.isEditing = !detailsState.isEditing
  }
}
