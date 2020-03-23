import { reqStatus } from '@/enums/req_status'
import { getOrders } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  getOrders: 'GET_ORDERS'
}

const initialState = {
  list: []
}

const mutations = {
  [types.setOrders] (state, orders) {
    state.list = orders
  }
}

const actions = {
  async [types.getOrders] ({ commit }) {
    const [error, data] = await getOrders()

    if (error) return reqStatus.error

    commit(types.setOrders, data)
    return reqStatus.success
  }
}

export default {
  moduleName: 'ORDERS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
