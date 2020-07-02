import { reqStatus } from '@/enums/req_status'
import { getSearchAddress } from '@/store/api_calls/address'

export const types = {
  getSearchAddress: 'GET_ADDRESS',
  setAddress: 'SET_ADDRESS'
}

const initialState = {
  list: []
}

const mutations = {
  [types.setAddress] (state, { DataArray }) {
    state.list = DataArray.map(item => {
      item.key = `${item.id}-${item.address_line_1 || null}`
      return item
    })
  },
  [types.getSearchAddress] (state, { DataArray }) {
    state.list = DataArray.map(item => {
      item.key = `${item.id}-${item.address_line_1 || null}`
      return item
    })
  }
}

const actions = {
  async [types.getSearchAddress] ({ commit }, filters) {
    const [error, data] = await getSearchAddress(filters)

    if (error) return reqStatus.error

    commit(types.setAddress, { DataArray: data.data.address_list })

    return reqStatus.success
  }
}

export default {
  moduleName: 'ADDRESS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
