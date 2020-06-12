import { reqStatus } from '@/enums/req_status'
import { getLibrary, getAccountVariantRules } from '@/store/api_calls/rules_editor'

export const types = {
  setLibrary: 'SET_LIBRARY',
  getLibrary: 'GET_LIBRARY',
  getAccountVariantRules: 'GET_ACCOUNT_VARIANT_RULES',
  setAccountVariantRules: 'SET_ACCOUNT_VARIANT_RULES'
}

const initialState = {
  rules_library: [],
  account_variant_rules: []
}

const mutations = {
  [types.setLibrary] (state, { libraryData }) {
    state.rules_library = libraryData
  },
  [types.setAccountVariantRules] (state, { accountVariantData }) {
    state.account_variant_rules = accountVariantData
  }
}

const actions = {
  async [types.getLibrary] ({ commit }) {
    const [error, data] = await getLibrary()

    if (error) return reqStatus.error

    commit(types.setLibrary, { libraryData: data.data })
    return reqStatus.success
  },
  async [types.getAccountVariantRules] ({ commit }) {
    const [error, data] = await getAccountVariantRules()

    if (error) return reqStatus.error

    commit(types.setAccountVariantRules, { accountVariantData: data.data })
    return reqStatus.success
  }
}

export default {
  moduleName: 'RULES_LIBRARY',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
