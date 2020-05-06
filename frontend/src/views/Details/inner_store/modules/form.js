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

  setFormFieldProp ({ prop, value, location, validation }) {
    if (!location) return
    const parts = location.split('/')
    let valueToSet = value
    let locatedObj

    if (location.includes('rootFields')) {
      locatedObj = state.form.sections[parts[0]][parts[1]][parts[2]]
    } else if (location.includes('subSections')) {
      locatedObj = state.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]]
    } else if (location.includes('actionSection')) {
      valueToSet = getInventoryCount(state.form)
      locatedObj = state.form.sections[parts[0]][parts[1]]
    }

    if (validation && !validation(locatedObj)) return
    Vue.set(locatedObj, prop, valueToSet)
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
