/* eslint-disable camelcase */
import cloneDeep from 'lodash/cloneDeep'
import { getFieldMaps, createFieldMaps } from '../api_calls/field_maps'
import deepDiff from 'deep-diff'

export const types = {
  SET_FIELD_MAPS: 'SET_FIELD_MAPS',
  SET_FIELD_MAP: 'SET_FIELD_MAP',
  GET_FIELD_MAPS: 'GET_FIELD_MAPS',
  RESET_FIELD_MAP: 'RESET_FIELD_MAP',
  SET_FIELD_MAPS_FILTERS: 'SET_FIELD_MAPS_FILTERS',
  SAVE_FIELD_MAPS: 'SAVE_FIELD_MAPS',
  UPDATE_DEFAULT_FIELD_MAPS: 'UPDATE_DEFAULT_FIELD_MAPS',
}

const initialState = {
  fieldMaps: null,
  previousLevelFieldMaps: null,
  defaultFieldMaps: null,
  filters: {
    companyId: null,
    tmsProviderId: null,
    variantId: null,
  }
}

const mutations = {
  [types.SET_FIELD_MAPS] (state, { fieldMaps, systemDefault = false }) {
    if (systemDefault) {
      state.defaultFieldMaps = { ...cloneDeep(fieldMaps.current) }
    }

    state.fieldMaps = { ...(fieldMaps.current) }
    state.previousLevelFieldMaps = { ...(fieldMaps.previous) }
  },

  [types.SET_FIELD_MAP] (state, { field, fieldMap }) {
    const newFieldMaps = { ...cloneDeep(state.fieldMaps) }

    newFieldMaps[field] = fieldMap

    state.fieldMaps = newFieldMaps
  },

  [types.SET_FIELD_MAPS_FILTERS] (state, { filters = {} }) {
    state.filters = { ...filters }
  },

  [types.UPDATE_DEFAULT_FIELD_MAPS] (state, { fieldMaps }) {
    state.defaultFieldMaps = { ...cloneDeep(fieldMaps) }
  }
}

const actions = {
  async [types.GET_FIELD_MAPS] ({ commit }, params) {
    const {
      companyId: company_id = null,
      tmsProviderId: tms_provider_id = null,
      variantId: variant_id = null,
    } = params

    const [error, data] = await getFieldMaps({ company_id, variant_id, tms_provider_id })

    if (error !== undefined) {
      return error
    }

    commit(types.SET_FIELD_MAPS, {
      fieldMaps: data.data,
      systemDefault: !company_id && !variant_id && !tms_provider_id,
    })
  },

  [types.SET_FIELD_MAP] ({ commit }, { field, fieldMap }) {
    commit(types.SET_FIELD_MAP, { field, fieldMap })
  },

  [types.RESET_FIELD_MAP] ({ commit, state }, { field }) {
    const fieldMap = { ...cloneDeep(state.defaultFieldMaps[field]) }
    commit(types.SET_FIELD_MAP, { field, fieldMap })
  },

  [types.SET_FIELD_MAPS_FILTERS] ({ commit, state }, { filters }) {
    commit(types.SET_FIELD_MAPS_FILTERS, { filters })
  },

  async [types.SAVE_FIELD_MAPS] ({ commit, state }) {
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
      commit(types.UPDATE_DEFAULT_FIELD_MAPS, {
        fieldMaps: createData.fieldmap_config
      })
    }

    return await createFieldMaps(createData)
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
  }
}

export default {
  moduleName: 'FIELD_MAPS',
  namespaced: true,
  state: initialState,
  mutations,
  actions,
  getters
}
