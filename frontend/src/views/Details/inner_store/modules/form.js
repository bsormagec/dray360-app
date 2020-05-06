import Vue from 'vue'
import { getInventoryCount } from '@/views/Details/inner_utils/get_inventory_count'

const state = Vue.observable({
  isEditing: false,
  form: {}
})

const methods = {
  toggleIsEditing () {
    state.isEditing = !state.isEditing
  },

  setForm (newForm) {
    state.form = newForm
  },

  setFormFieldProp ({ prop, value, location }) {
    if (!location) return
    const parts = location.split('/')

    if (location.includes('rootFields')) {
      Vue.set(state.form.sections[parts[0]][parts[1]][parts[2]], prop, value)
    } else if (location.includes('subSections')) {
      Vue.set(state.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]], prop, value)
    } else if (location.includes('actionSection')) {
      const valueToSet = getInventoryCount(state.form)
      Vue.set(state.form.sections[parts[0]][parts[1]], prop, valueToSet)
    }
  },

  addFormInventoryItem ({ key, fields }) {
    Vue.set(state.form.sections.inventory, 'subSections', state.form.sections.inventory.subSections)
    Vue.set(state.form.sections.inventory.subSections, key, {})
    Vue.set(state.form.sections.inventory.subSections[key], 'fields', fields)
  },

  deleteFormInventoryItem ({ key }) {
    Vue.delete(state.form.sections.inventory.subSections, key)
  }
}

export default {
  state,
  methods
}
