import { reqStatus } from '@/enums/req_status'
import { updateAccesorialMapping, getAccesorialMapping } from '@/store/api_calls/accesorialmapping'

export const accesorialmappingtype = {
  updateAccesorialMapping: 'UPDATE_ACCESORIAL_MAPPING',
  setAccesorialMapping: 'SET_ACCESORIAL_MAPPING',
  getAccesorialMapping: 'GET_ACCESORIAL_MAPPING'
}

const initialState = {
  AccesorialMapping: {}
}

const mutations = {
  [accesorialmappingtype.setAccesorialMapping] (state, mappingData) {
    state.AccesorialMapping = mappingData
  }
}

const actions = {
  // update updateAccesorialMapping
  async [accesorialmappingtype.updateAccesorialMapping] ({ commit }, { id, variant, mapping }) {
    const [error] = await updateAccesorialMapping({ id, variant, mapping })

    if (error) return reqStatus.error
    commit(accesorialmappingtype.setAccesorialMapping, { mapping })
    return reqStatus.success
  },
  // Get AccesorialMapping by ID
  async [accesorialmappingtype.getAccesorialMapping] ({ commit }, { id, variant }) {
    const [error, data] = await getAccesorialMapping({ id, variant })

    if (error) return reqStatus.error

    commit(accesorialmappingtype.setAccesorialMapping, data)
    return reqStatus.success
  }
}

export default {
  moduleName: 'ACCESORIAL',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
