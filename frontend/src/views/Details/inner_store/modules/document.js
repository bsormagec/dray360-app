import Vue from 'vue'
import { objValFromLocation } from '@/utils/obj_val_from_loc'
import { modes, pools } from '@/views/Details/inner_types'
import { getFieldLocation } from '@/views/Details/inner_utils/get_field_location'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'
import formModule from '@/views/Details/inner_store/modules/form'

const state = Vue.observable({
  document: [],
  lastMode: undefined,
  hoverTimeouts: {}
})

const methods = {
  setDocument (newDocument) {
    state.document = newDocument
    methods.setFormValues()
  },

  setFormValues () {
    state.document.forEach(({ highlights }) => {
      highlights.forEach(({ name, value }) => {
        formModule.methods.setFormFieldProp({
          prop: 'value',
          value,
          formLocation: getLocationOnForm(name)
        })
      })
    })
  },

  setDocumentFieldProp ({ prop, value, location, validation }) {
    if (!location) return

    const locatedObj = objValFromLocation({
      obj: state.document,
      location,
      separator: '/'
    })

    if (validation && !validation(locatedObj)) return
    Vue.set(locatedObj, prop, value)
  },

  scrollTo ({ field, container }) {
    document.getElementById(`${cleanStrForId(field.name)}-${container}`).scrollIntoView()
  },

  startEdit ({ field, pageIndex, highlightIndex }) {
    if (!field.name) return

    methods.stopHover({ field, pageIndex, highlightIndex })

    const fieldLoc = formModule.methods.setFormFieldProp({
      prop: 'highlight',
      value: modes.edit,
      formLocation: field.formLocation || getLocationOnForm(field.name)
    })
    if (!fieldLoc) return

    methods.setDocumentFieldProp({
      prop: modes.edit,
      value: true,
      location: triggerFromDocument({ pageIndex, highlightIndex })
        ? `${pageIndex}/highlights/${highlightIndex}`
        : getLocationOnDoc(field.name)
    })

    methods.scrollTo({
      field,
      container: triggerFromDocument({ pageIndex, highlightIndex })
        ? formModule.state.isEditing ? 'editing' : 'viewing'
        : 'document'
    })

    state.lastMode = modes.edit
  },

  stopEdit ({ field }) {
    if (!field.name) return

    methods.setDocumentFieldProp({
      prop: modes.edit,
      value: false,
      location: getLocationOnDoc(field.name)
    })

    formModule.methods.setFormFieldProp({
      prop: 'highlight',
      value: undefined,
      formLocation: field.formLocation || getLocationOnForm(field.name)
    })

    state.lastMode = undefined
  },

  startHover ({ field, pageIndex, highlightIndex }) {
    if (!field.name) return
    if (state.hoverTimeouts[field.name]) clearTimeout(state.hoverTimeouts[field.name])

    state.hoverTimeouts[field.name] = setTimeout(() => {
      const fieldLoc = formModule.methods.setFormFieldProp({
        prop: 'highlight',
        value: modes.hover,
        formLocation: field.formLocation || getLocationOnForm(field.name),
        validation: v => v.highlight !== modes.edit
      })
      if (!fieldLoc) return

      methods.setDocumentFieldProp({
        prop: modes.hover,
        value: true,
        location: triggerFromDocument({ pageIndex, highlightIndex })
          ? `${pageIndex}/highlights/${highlightIndex}`
          : getLocationOnDoc(field.name),
        validation: v => Boolean(v[modes.edit]) === false
      })

      state.lastMode = modes.hover
    }, 200)
  },

  stopHover ({ field, pageIndex, highlightIndex }) {
    if (!field.name) return
    if (state.hoverTimeouts[field.name]) clearTimeout(state.hoverTimeouts[field.name])

    methods.setDocumentFieldProp({
      prop: modes.hover,
      value: false,
      location: triggerFromDocument({ pageIndex, highlightIndex })
        ? `${pageIndex}/highlights/${highlightIndex}`
        : getLocationOnDoc(field.name),
      validation: v => Boolean(v[modes.hover]) === true
    })

    formModule.methods.setFormFieldProp({
      prop: 'highlight',
      value: undefined,
      formLocation: field.formLocation || getLocationOnForm(field.name),
      validation: v => v.highlight === modes.hover
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
  const loc = getFieldLocation({
    pool: state.document,
    poolType: pools.document,
    fieldName
  })
  return loc
}

function getLocationOnForm (fieldName) {
  const loc = getFieldLocation({
    pool: formModule.state.form,
    poolType: pools.form,
    fieldName
  })
  return loc
}
