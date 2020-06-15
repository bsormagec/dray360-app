import { reqStatus } from '@/enums/req_status'
import { getLibrary, getAccountVariantRules, putEditRule, postSaveRuleSequence, postAddRule } from '@/store/api_calls/rules_editor'

export const types = {
  setLibrary: 'SET_LIBRARY',
  getLibrary: 'GET_LIBRARY',
  getAccountVariantRules: 'GET_ACCOUNT_VARIANT_RULES',
  setAccountVariantRules: 'SET_ACCOUNT_VARIANT_RULES',
  setRule: 'SET_RULE',
  setSequence: 'SET_SEQUENCE',
  addRule: 'ADD_RULE'
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
  },
  [types.setRule] (state, { ruleData, i }) {
    state.account_variant_rules[i] = ruleData
  },
  [types.setSequence] (state, { sequenceData }) {
    state.account_variant_rules = sequenceData
  },
  [types.addRule] (state, { ruleData }) {
    state.rules_library.push(ruleData)
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
  },
  async [types.setRule] ({ commit }) {
    const [error, data] = await putEditRule()

    if (error) return reqStatus.error

    console.log('to commit: ', data)

    commit(types.setRule, { ruleData: data })
    return reqStatus.success
  },
  async [types.setSequence] ({ commit }) {
    const [error, data] = await postSaveRuleSequence()

    if (error) return reqStatus.error

    console.log('sequenceData to commit:', data.data)

    commit(types.setSequence, { sequenceData: data.data })
    return reqStatus.succcess
  },
  async [types.addRule] ({ commit }, ruleData) {
    await postAddRule(ruleData)

    // if (error) return reqStatus.error

    console.log('ruleData to commit:', ruleData)

    commit(types.addRule, { ruleData })
    return reqStatus.succcess
  }
  // async login ({ commit }, authData) {
  //   await getCsrfCookie()
  //   const [error] = await postLogin(authData)

  //   if (!error) commit('auth_success')
  // }
}

export default {
  moduleName: 'RULES_LIBRARY',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
