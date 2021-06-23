import cloneDeep from 'lodash/cloneDeep'
import { reqStatus } from '@/enums/req_status'
import groupBy from 'lodash/groupBy'
import { getRules, getCompanyVariantRules, putEditRule, saveRulesAssigment, createRule, testRule, getVariants } from '@/store/api_calls/rules_editor'

export const mutationTypes = {
  setRules: 'SET_RULES',
  setRule: 'SET_RULE',
  setCompanyVariantRules: 'SET_COMPANY_VARIANT_RULES',
  setVariantRules: 'SET_VARIANT_RULES',
  setVariants: 'SET_VARIANTS',
  setTestOutput: 'SET_TEST_OUTPUT',
}

export const actionTypes = {
  fetchRules: 'fetchRules',
  fetchVariants: 'fetchVariants',
  fetchCompanyVariantRules: 'fetchCompanyVariantRules',
  fetchVariantRules: 'fetchVariantRules',
  setCompanyVariantRules: 'setCompanyVariantRules',
  setVariantRules: 'setVariantRules',
  createRule: 'createRule',
  editRule: 'editRule',
  saveRulesAssigment: 'saveRulesAssigment',
  testRule: 'testRule',
}

const initialState = {
  rules: [],
  companyVariantRules: [],
  variantRules: [],
  variants: [],
  testOutput: null,
}

const mutations = {
  [mutationTypes.setRules] (state, { rules }) {
    state.rules = rules
  },
  [mutationTypes.setRule] (state, { rule }) {
    const index = state.rules.findIndex(item => item.id === rule.id)
    const newRules = cloneDeep(state.rules)

    if (index === -1) {
      newRules.push(rule)
    } else {
      newRules[index] = rule
    }

    state.rules = newRules
  },
  [mutationTypes.setCompanyVariantRules] (state, { companyVariantRules }) {
    state.companyVariantRules = companyVariantRules
  },
  [mutationTypes.setVariantRules] (state, { variantRules }) {
    state.variantRules = variantRules
  },
  [mutationTypes.setVariants] (state, { variants }) {
    state.variants = variants
  },
  [mutationTypes.setTestOutput] (state, { testOutput }) {
    state.testOutput = testOutput
  },
}

const actions = {
  async [actionTypes.fetchRules] ({ commit }) {
    const [error, data] = await getRules()

    if (!error) {
      commit(mutationTypes.setRules, { rules: data.data })
    }

    return [error, data]
  },

  async [actionTypes.fetchVariants] ({ commit }) {
    const [error, data] = await getVariants()

    if (!error) {
      commit(mutationTypes.setVariants, { variants: data })
    }

    return [error, data]
  },

  async [actionTypes.createRule] ({ commit }, ruleData) {
    const [error, data] = await createRule(ruleData)

    if (!error) {
      commit(mutationTypes.setRule, { rule: data })
    }

    return [error, data]
  },

  async [actionTypes.fetchCompanyVariantRules] ({ commit }, params) {
    const [error, data] = await getCompanyVariantRules(params)

    if (!error) {
      commit(mutationTypes.setCompanyVariantRules, { companyVariantRules: data.data })
    }

    return [error, data]
  },

  async [actionTypes.fetchVariantRules] ({ commit }, params) {
    const [error, data] = await getCompanyVariantRules(params)

    if (!error) {
      commit(mutationTypes.setVariantRules, { variantRules: data.data })
    }

    return [error, data]
  },

  async [actionTypes.editRule] ({ commit }, rule) {
    const [error] = await putEditRule(rule)

    if (!error) {
      commit(mutationTypes.setRule, { rule })
    }

    return [error]
  },

  async [actionTypes.saveRulesAssigment] ({ state }, { companyId, variantId }) {
    let dataToUse = null
    if (companyId && variantId) {
      dataToUse = state.companyVariantRules
    } else if (!companyId && variantId) {
      dataToUse = state.variantRules
    }

    const [error, data] = await saveRulesAssigment({
      company_id: companyId,
      variant_id: variantId,
      rules: dataToUse.map(item => item.id)
    })

    return [error, data]
  },

  [actionTypes.setCompanyVariantRules] ({ commit }, companyVariantRules) {
    commit(mutationTypes.setCompanyVariantRules, { companyVariantRules })
  },

  [actionTypes.setVariantRules] ({ commit }, variantRules) {
    commit(mutationTypes.setVariantRules, { variantRules })
  },

  async [actionTypes.testRule] ({ commit }, dataObject) {
    const testOutput = await testRule(dataObject.orderId, dataObject.ruleToTest)
    commit(mutationTypes.setTestOutput, { testOutput })
    return reqStatus.success
  },
}

const getters = {
  groupedRules (state) {
    const grouped = groupBy(state.rules, 'description')
    const mappedRules = []

    for (const key in grouped) {
      mappedRules.push({ index: key, children: grouped[key], name: key })
    }

    return mappedRules
  },
}

export default {
  moduleName: 'RULES_LIBRARY',
  namespaced: true,
  state: initialState,
  getters,
  mutations,
  actions
}
