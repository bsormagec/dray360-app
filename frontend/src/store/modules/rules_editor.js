import { reqStatus } from '@/enums/req_status'
import { getLibrary, getAccountVariantRules, putEditRule, postSaveRuleSequence, postAddRule, getRuleCode } from '@/store/api_calls/rules_editor'

export const types = {
  setLibrary: 'SET_LIBRARY',
  getLibrary: 'GET_LIBRARY',
  getAccountVariantRules: 'GET_ACCOUNT_VARIANT_RULES',
  setAccountVariantRules: 'SET_ACCOUNT_VARIANT_RULES',
  setRule: 'SET_RULE',
  setSequence: 'SET_SEQUENCE',
  addRule: 'ADD_RULE',
  setRuleCode: 'SET_RULE_CODE'
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
  async [types.setRule] ({ commit }, ruleData) {
    await putEditRule(ruleData)

    // if (error) return reqStatus.error

    console.log('#types.setRule - ruleData to commit:', ruleData)

    commit(types.setRule, { ruleData })
    return reqStatus.succcess
  },
  async [types.setRuleCode] ({ commit }, i) {
    const ruleCodeData = await getRuleCode(i)

    const data = ruleCodeData[1].data[i].code
    console.log('data to commit:', data)

    return data
  },
  async [types.setSequence] ({ commit }, sequenceData) {
    await postSaveRuleSequence(sequenceData)

    // if (error) return reqStatus.error

    console.log('sequenceData to commit:', sequenceData)

    commit(types.setSequence, { sequenceData })
    // return reqStatus.succcess
  },
  async [types.addRule] ({ commit }, ruleData) {
    await postAddRule(ruleData)

    // if (error) return reqStatus.error

    console.log('ruleData to commit:', ruleData)

    commit(types.addRule, { ruleData })
    return reqStatus.succcess
  }
}

export default {
  moduleName: 'RULES_LIBRARY',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
