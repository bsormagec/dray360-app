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
    } else if (location.includes('actionSection')) {
      const valueToSet = valueToSetFromInventory()
      Vue.set(detailsState.form.sections[parts[0]][parts[1]], 'value', valueToSet)
    }
  },

  toggleIsEditing () {
    detailsState.isEditing = !detailsState.isEditing
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
