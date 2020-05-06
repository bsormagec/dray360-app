import Vue from 'vue'
import { modes, pools } from '@/views/Details/inner_types'
import { getFieldLocation } from '@/views/Details/inner_utils/get_field_location'
import formModule from '@/views/Details/inner_store/modules/form'

const state = Vue.observable({
  document: [],
  lastMode: undefined,
  hoverTimeouts: {}
})

const methods = {
  setDocument (newDocument) {
    state.document = newDocument
  },

  setDocumentFieldProp ({ prop, value, location, validation }) {
    if (!location) return
    const parts = location.split('/')
    const locatedObj = state.document[parts[0]][parts[1]][parts[2]]

    if (validation && !validation(locatedObj)) return
    Vue.set(locatedObj, prop, value)
  },

  startEdit ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return

    methods.stopHover({ fieldName, pageIndex, highlightIndex })

    methods.setDocumentFieldProp({
      prop: modes.edit,
      value: true,
      location: triggerFromDocument({ pageIndex, highlightIndex })
        ? `${pageIndex}/highlights/${highlightIndex}`
        : getLocationOnDoc(fieldName)
    })

    formModule.methods.setFormFieldProp({
      prop: 'editing_set_by_document',
      value: modes.edit,
      location: getLocationOnForm(fieldName)
    })

    state.lastMode = modes.edit
  },

  stopEdit ({ fieldName }) {
    if (!fieldName) return

    methods.setDocumentFieldProp({
      prop: modes.edit,
      value: false,
      location: getLocationOnDoc(fieldName)
    })

    formModule.methods.setFormFieldProp({
      prop: 'editing_set_by_document',
      value: undefined,
      location: getLocationOnForm(fieldName)
    })

    state.lastMode = undefined
  },

  startHover ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return
    if (state.hoverTimeouts[fieldName]) clearTimeout(state.hoverTimeouts[fieldName])

    state.hoverTimeouts[fieldName] = setTimeout(() => {
      methods.setDocumentFieldProp({
        prop: modes.hover,
        value: true,
        location: triggerFromDocument({ pageIndex, highlightIndex })
          ? `${pageIndex}/highlights/${highlightIndex}`
          : getLocationOnDoc(fieldName),
        validation: v => Boolean(v[modes.edit]) === false
      })

      formModule.methods.setFormFieldProp({
        prop: 'editing_set_by_document',
        value: modes.hover,
        location: getLocationOnForm(fieldName),
        validation: v => v.editing_set_by_document !== modes.edit
      })

      state.lastMode = modes.hover
    }, 200)
  },

  stopHover ({ fieldName, pageIndex, highlightIndex }) {
    if (!fieldName) return
    if (state.hoverTimeouts[fieldName]) clearTimeout(state.hoverTimeouts[fieldName])

    methods.setDocumentFieldProp({
      prop: modes.hover,
      value: false,
      location: triggerFromDocument({ pageIndex, highlightIndex })
        ? `${pageIndex}/highlights/${highlightIndex}`
        : getLocationOnDoc(fieldName),
      validation: v => Boolean(v[modes.hover]) === true
    })

    formModule.methods.setFormFieldProp({
      prop: 'editing_set_by_document',
      value: undefined,
      location: getLocationOnForm(fieldName),
      validation: v => v.editing_set_by_document === modes.hover
    })

    state.lastMode = undefined
  }
}

export default {
  state,
  methods
}

function triggerFromDocument ({ pageIndex, highlightIndex }) {
  return pageIndex !== undefined && highlightIndex !== undefined
}

function getLocationOnDoc (fieldName) {
  return getFieldLocation({
    pool: state.document,
    poolType: pools.document,
    fieldName
  })
}

function getLocationOnForm (fieldName) {
  return getFieldLocation({
    pool: formModule.state.form,
    poolType: pools.form,
    fieldName
  })
}
