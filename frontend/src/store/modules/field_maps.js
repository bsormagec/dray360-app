/* eslint-disable camelcase */
import cloneDeep from 'lodash/cloneDeep'
import { getFieldMaps, createFieldMaps } from '../api_calls/field_maps'
import deepDiff from 'deep-diff'
import { getRoles } from '../api_calls/users'

const mutationTypes = {
  setFieldMaps: 'SET_FIELD_MAPS',
  setFieldMap: 'SET_FIELD_MAP',
  deleteFieldMap: 'DELETE_FIELD_MAP',
  setFieldMapsFilters: 'SET_FIELD_MAPS_FILTERS',
  updateDefaultFieldMaps: 'UPDATE_DEFAULT_FIELD_MAPS',
  setFieldMapsNames: 'SET_FIELD_MAPS_NAMES',
  setRoles: 'SET_ROLES',
  setAudits: 'SET_AUDITS',
}

export const actionTypes = {
  getFieldMaps: 'getFieldMaps',
  setFieldMap: 'setFieldMap',
  deleteFieldMap: 'deleteFieldMap',
  resetFieldMap: 'resetFieldMap',
  setFieldMapsFilters: 'setFieldMapsFilters',
  updateFieldMapsNames: 'updateFieldMapsNames',
  saveFieldMaps: 'saveFieldMaps',
  getRoles: 'getRoles',
}

const initialState = {
  roles: [],
  fieldMaps: null,
  fieldMapsNames: [],
  previousLevelFieldMaps: null,
  audits: [],
  defaultFieldMaps: null,
  filters: {
    companyId: null,
    tmsProviderId: null,
    variantId: null,
  },
}

const mutations = {
  [mutationTypes.setFieldMaps] (state, { fieldMaps, systemDefault = false }) {
    if (systemDefault) {
      state.defaultFieldMaps = { ...cloneDeep(fieldMaps.current) }
    }

    state.fieldMaps = { ...(fieldMaps.current) }
    state.previousLevelFieldMaps = { ...(fieldMaps.previous) }
  },

  [mutationTypes.setFieldMap] (state, { field, fieldMap }) {
    const newFieldMaps = { ...cloneDeep(state.fieldMaps) }

    newFieldMaps[field] = cloneDeep(fieldMap)

    state.fieldMaps = newFieldMaps
  },

  [mutationTypes.deleteFieldMap] (state, { field }) {
    const newFieldMaps = { ...cloneDeep(state.fieldMaps) }
    delete newFieldMaps[field]
    state.fieldMaps = newFieldMaps
  },

  [mutationTypes.setFieldMapsNames] (state) {
    const newFieldMapsNames = { ...cloneDeep(state.fieldMaps) }
    state.fieldMapsNames = Object.keys(newFieldMapsNames)
      .sort()
      .map(key => ({ id: key, name: newFieldMapsNames[key].d3canon_name }))
  },

  [mutationTypes.setFieldMapsFilters] (state, { filters = {} }) {
    state.filters = { ...cloneDeep(filters) }
  },

  [mutationTypes.updateDefaultFieldMaps] (state, { fieldMaps }) {
    state.defaultFieldMaps = { ...cloneDeep(fieldMaps) }
  },

  [mutationTypes.setRoles] (state, { roles }) {
    state.roles = [...roles]
  },

  [mutationTypes.setAudits] (state, { audits }) {
    state.audits = [...audits.fieldmap_config]
  },
}

const actions = {
  async [actionTypes.getFieldMaps] ({ commit }, params) {
    const {
      companyId: company_id = null,
      tmsProviderId: tms_provider_id = null,
      variantId: variant_id = null,
    } = params

    const [error, data] = await getFieldMaps({ company_id, variant_id, tms_provider_id })

    if (error !== undefined) {
      return error
    }

    commit(mutationTypes.setFieldMaps, {
      fieldMaps: data.data,
      systemDefault: !company_id && !variant_id && !tms_provider_id,
    })
    commit(mutationTypes.setAudits, { audits: data.data.changes })
  },

  [actionTypes.setFieldMap] ({ commit }, { field, fieldMap }) {
    commit(mutationTypes.setFieldMap, { field, fieldMap })
  },

  [actionTypes.deleteFieldMap] ({ commit }, { field }) {
    commit(mutationTypes.deleteFieldMap, { field })
  },

  [actionTypes.resetFieldMap] ({ commit, state }, { field }) {
    const fieldMap = { ...cloneDeep(state.defaultFieldMaps[field]) }
    commit(mutationTypes.setFieldMap, { field, fieldMap })
  },

  [actionTypes.setFieldMapsFilters] ({ commit, state }, { filters }) {
    commit(mutationTypes.setFieldMapsFilters, { filters })
  },

  async [actionTypes.saveFieldMaps] ({ commit, state }) {
    const {
      companyId: company_id = null,
      tmsProviderId: tms_provider_id = null,
      variantId: variant_id = null,
    } = state.filters
    const createData = {
      company_id,
      variant_id,
      tms_provider_id,
      fieldmap_config: state.fieldMaps
    }

    if (!company_id) {
      delete createData.company_id
    }
    if (!variant_id) {
      delete createData.variant_id
    }
    if (!tms_provider_id) {
      delete createData.tms_provider_id
    }
    if (!tms_provider_id && !company_id && !variant_id) {
      commit(mutationTypes.updateDefaultFieldMaps, {
        fieldMaps: createData.fieldmap_config
      })
    }

    const [error, data] = await createFieldMaps(createData)

    if (!error) {
      commit(mutationTypes.setAudits, { audits: data.changes })
    }

    return [error, data]
  },

  [actionTypes.updateFieldMapsNames] ({ commit }) {
    commit(mutationTypes.setFieldMapsNames)
  },

  async [actionTypes.getRoles] ({ commit, state }) {
    if (state.roles.length > 0) {
      return
    }

    const [error, data] = await getRoles()

    if (error !== undefined) {
      return error
    }

    commit(mutationTypes.setRoles, { roles: data.data })
  },
}

const getters = {
  sortedFieldMaps (state) {
    if (!state.fieldMaps) {
      return {}
    }

    const sortedKeys = Object.keys(state.fieldMaps).sort()
    const sortedFieldMaps = {}

    sortedKeys.forEach(key => {
      sortedFieldMaps[key] = state.fieldMaps[key]
    })

    return sortedFieldMaps
  },

  fieldMapsChanges (state) {
    if (Array.isArray(state.previousLevelFieldMaps) || Object.keys(state.previousLevelFieldMaps).length === 0) {
      return []
    }

    return (deepDiff(state.previousLevelFieldMaps, state.fieldMaps) || [])
      .filter(change => {
        if (
          (change.lhs === null && change.rhs === '') ||
          (change.lhs === '' && change.rhs === null)
        ) {
          return true
        }
        return change.kind !== 'D'
      })
  },
}

export default {
  moduleName: 'FIELD_MAPS',
  namespaced: true,
  state: initialState,
  mutations,
  actions,
  getters
}
