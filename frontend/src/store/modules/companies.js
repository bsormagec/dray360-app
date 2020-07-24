import { reqStatus } from '@/enums/req_status'
import { updateCompanies, getCompaniesbyId } from '@/store/api_calls/companies'

export const types = {
  updateCompaniesMappingField: 'UPDATE_COMPANIES_MAPPING',
  setMappingData: 'SET_COMPANIES_MAPPING',
  getCompany: 'GET_COMPANY',
  setCompany: 'SET_COMPANY'
}

const initialState = {
  mappingData: {},
  company: {}
}

const mutations = {
  [types.setMappingData] (state, mappingData) {
    state.mappingData = mappingData
  },
  [types.setCompany] (state, company) {
    state.company = company
  }
}

const actions = {
  // update company
  async [types.updateCompaniesMappingField] ({ commit }, { id, changes }) {
    const [error] = await updateCompanies({ id, changes })

    if (error) return reqStatus.error
    commit(types.setMappingData, { changes })
    return reqStatus.success
  },
  // Get Company by ID
  async [types.getCompany] ({ commit }, { id }) {
    const [error, data] = await getCompaniesbyId({ id })

    if (error) return reqStatus.error

    commit(types.setCompany, data)
    return reqStatus.success
  }
}

export default {
  moduleName: 'COMPANIES',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
