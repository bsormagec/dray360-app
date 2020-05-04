import Vue from 'vue'
import { getInventoryCount } from '@/views/Details/inner_utils/get_inventory_count'

export const detailsState = Vue.observable({
  isEditing: false,
  form: {}
})

export const detailsMethods = {
  toggleIsEditing () {
    detailsState.isEditing = !detailsState.isEditing
  },

  setForm (newForm) {
    detailsState.form = newForm
  },

  setFormFieldValue ({ value, location }) {
    const parts = location.split('/')

    if (location.includes('rootFields')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]], 'value', value)
    } else if (location.includes('subSections')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]], 'value', value)
    } else if (location.includes('actionSection')) {
      const valueToSet = getInventoryCount(detailsState.form)
      Vue.set(detailsState.form.sections[parts[0]][parts[1]], 'value', valueToSet)
    }
  },

  setFormFieldEditingByDocument ({ value, location }) {
    const parts = location.split('/')

    if (location.includes('rootFields')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]], 'editing_set_by_document', value)
    } else if (location.includes('subSections')) {
      Vue.set(detailsState.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]], 'editing_set_by_document', value)
    }
  },

  addFormInventoryItem ({ key, fields }) {
    Vue.set(detailsState.form.sections.inventory, 'subSections', detailsState.form.sections.inventory.subSections)
    Vue.set(detailsState.form.sections.inventory.subSections, key, {})
    Vue.set(detailsState.form.sections.inventory.subSections[key], 'fields', fields)
  },

  deleteFormInventoryItem ({ key }) {
    Vue.delete(detailsState.form.sections.inventory.subSections, key)
  }
}
