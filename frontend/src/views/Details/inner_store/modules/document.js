import Vue from 'vue'
import { modes, pools } from '@/views/Details/inner_types'
import { getFieldLocation } from '@/views/Details/inner_utils/get_field_location'
import formModule from '@/views/Details/inner_store/modules/form'

const state = Vue.observable({
  document: [],
  lastMode: undefined
})

const methods = {
  setDocument (newDocument) {
    state.document = newDocument
  },

  setDocumentFieldEdit ({ value, location }) {
    const parts = location.split('/')
    Vue.set(state.document[parts[0]][parts[1]][parts[2]], 'edit', value)
  },

  startEdit ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return
    methods.stopHover({ fieldName, pageIndex, highlightIndex })

    Vue.set(state.document[pageIndex].highlights[highlightIndex], 'edit', true)
    formModule.methods.setFormFieldEditingByDocument({
      value: modes.edit,
      location: getFieldLocation({
        pool: formModule.state.form,
        poolType: pools.form,
        fieldName
      })
    })

    state.lastMode = modes.edit
  },

  stopEdit ({ fieldName }) {
    if (!fieldName) return

    methods.setDocumentFieldEdit({
      value: false,
      location: getFieldLocation({
        pool: state.document,
        poolType: pools.document,
        fieldName
      })
    })

    formModule.methods.setFormFieldEditingByDocument({
      value: undefined,
      location: getFieldLocation({
        pool: formModule.state.form,
        poolType: pools.form,
        fieldName
      })
    })

    state.lastMode = undefined
  },

  startHover ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return
    if (state.lastMode === modes.edit) return

    Vue.set(state.document[pageIndex].highlights[highlightIndex], 'hover', true)
    formModule.methods.setFormFieldEditingByDocument({
      value: modes.hover,
      location: getFieldLocation({ pool: formModule.state.form, poolType: pools.form, fieldName })
    })

    state.lastMode = modes.hover
  },

  stopHover ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return

    if (state.lastMode === modes.hover) {
      Vue.set(state.document[pageIndex].highlights[highlightIndex], 'hover', false)
      formModule.methods.setFormFieldEditingByDocument({
        value: undefined,
        location: getFieldLocation({ pool: formModule.state.form, poolType: pools.form, fieldName })
      })

      state.lastMode = undefined
    }
  }
}

export default {
  state,
  methods
}
