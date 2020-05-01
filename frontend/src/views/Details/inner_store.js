import Vue from 'vue'

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
      const valueToSet = valueToSetFromInventory()
      Vue.set(detailsState.form.sections[parts[0]][parts[1]], 'value', valueToSet)
    }
  },

  setFormFieldEditingByDocument ({ value, location }) {
    const parts = location.split('/')
    console.log('setFormFieldEditingByDocument', location)

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

function valueToSetFromInventory () {
  const inventorySubSections = detailsState.form.sections.inventory.subSections
  const inventoryItemsCount = Object.keys(inventorySubSections).length
  let hazardousCount = 0

  for (const key in inventorySubSections) {
    if (inventorySubSections[key].fields.hazardous.value === 'yes') {
      hazardousCount += 1
    }
  }

  return {
    inventoryItemsCount,
    hazardousCount
  }
}
