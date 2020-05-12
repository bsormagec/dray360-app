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

  setFormFieldProp ({ prop, value, formLocation, validation }) {
    if (!formLocation) return
    const parts = formLocation.split('/')
    let valueToSet = value
    let locatedObj

    if (formLocation.includes('rootFields')) {
      locatedObj = state.form.sections[parts[0]][parts[1]][parts[2]]
    } else if (formLocation.includes('subSections')) {
      locatedObj = state.form.sections[parts[0]][parts[1]][parts[2]][parts[3]][parts[4]]
    } else if (formLocation.includes('actionSection')) {
      valueToSet = getInventoryCount(state.form)
      locatedObj = state.form.sections[parts[0]][parts[1]]
    }

    if (validation && !validation(locatedObj)) return
    Vue.set(locatedObj, prop, valueToSet)

    if (prop === 'value') methods.updateFormFieldChildren({ field: locatedObj, children: valueToSet })
  },

  updateFormFieldChildren ({ field, children }) {
    if (typeof children !== 'object') return

    const objToEdit = (key, opName) => field.el.children
      ? field.el.children[key]
      : field.el.options[opName].el.children[key]

    let optionName
    if (children.optionName) {
      optionName = children.optionName
      Vue.set(field, 'optionName', optionName)
      delete children.optionName
    }

    if (Object.keys(children).length) {
      for (const key in children) {
        Vue.set(objToEdit(key, optionName), 'value', children[key])
      }
      return
    }

    Vue.set(field, 'value', optionName)
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
