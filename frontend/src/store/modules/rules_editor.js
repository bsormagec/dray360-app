import { reqStatus } from '@/enums/req_status'
import { getLibrary, getCompanyVariantRules, putEditRule, postSaveRuleSequence, postAddRule, getRuleCode, getTestingOutput, getVariantList, getCompanyList } from '@/store/api_calls/rules_editor'

export const types = {
  setLibrary: 'SET_LIBRARY',
  getLibrary: 'GET_LIBRARY',
  getCompanyVariantRules: 'GET_COMPANY_VARIANT_RULES',
  setCompanyVariantRules: 'SET_COMPANY_VARIANT_RULES',
  setRule: 'SET_RULE',
  setSequence: 'SET_SEQUENCE',
  addRule: 'ADD_RULE',
  setRuleCode: 'SET_RULE_CODE',
  getTestingOutput: 'GET_TESTING_OUTPUT',
  setTestingOutput: 'SET_TESTING_OUTPUT',
  getCompanyList: 'GET_COMPANY_LIST',
  setCompanyList: 'SET_COMPANY_LIST',
  getVariantList: 'GET_VARIANT_LIST',
  setVariantList: 'SET_VARIANT_LIST'
}

const initialState = {
  rules_library: [],
  company_variant_rules: [],
  testing_output: null,
  company_list: [],
  variant_list: []
}

const mutations = {
  [types.setLibrary] (state, { libraryData }) {
    state.rules_library = libraryData
  },
  [types.setCompanyVariantRules] (state, { companyVariantData }) {
    console.log('seCompanyVariantRules')
    state.company_variant_rules = companyVariantData
  },
  [types.setRule] (state, { ruleData, i }) {
    state.company_variant_rules[i] = ruleData
  },
  [types.setSequence] (state, { sequenceData }) {
    state.company_variant_rules = sequenceData
  },
  [types.addRule] (state, { ruleData }) {
    state.rules_library.push(ruleData)
  },
  [types.setTestingOutput] (state, { testingOutput }) {
    state.testing_output = testingOutput
  },
  [types.setCompanyList] (state, { companyList }) {
    state.company_list = companyList
  },
  [types.setVariantList] (state, { variantList }) {
    state.variant_list = variantList
  }
}

const actions = {
  async [types.getLibrary] ({ commit }) {
    const [error, data] = await getLibrary()

    if (error) return reqStatus.error

    commit(types.setLibrary, { libraryData: data.data })
    return reqStatus.success
  },
  async [types.getCompanyVariantRules] ({ commit }, pairIds) {
    const [error, data] = await getCompanyVariantRules(pairIds.company_id, pairIds.variant_id)

    if (error) return reqStatus.error

    commit(types.setCompanyVariantRules, { companyVariantData: data.data })
    return reqStatus.success
  },
  async [types.setRule] ({ commit }, ruleData) {
    await putEditRule(ruleData)

    // if (error) return reqStatus.error

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

    commit(types.setSequence, { sequenceData })
    // return reqStatus.succcess
  },
  async [types.addRule] ({ commit }, ruleData) {
    const [error, data] = await postAddRule(ruleData)

    if (error) return reqStatus.error

    commit(types.addRule, { ruleData: data })
    return reqStatus.succcess
  },
  async [types.getTestingOutput] ({ commit }, dataObject) {
    const data = await getTestingOutput(dataObject.orderId, dataObject.ruleToTest)

    commit(types.setTestingOutput, { testingOutput: data })
  },

  async [types.getCompanyList] ({ commit }) {
    const [error, data] = await getCompanyList()
    if (error) return error.message

    commit(types.setCompanyList, { companyList: data })
    return reqStatus.success
  },

  async [types.getVariantList] ({ commit }) {
    const [error, data] = await getVariantList()
    if (error) return error.message

    commit(types.setVariantList, { variantList: data })
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
