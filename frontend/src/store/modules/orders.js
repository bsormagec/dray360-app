import { reqStatus } from '@/enums/req_status'
import { getOrders } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  getOrders: 'GET_ORDERS'
}

const initialState = {
  list: [],
  links: {},
  meta: {}
}

const mutations = {
  [types.setOrders] (state, { data, links, meta }) {
    state.list = data
    state.links = links
    state.meta = meta
  }
}

const actions = {
  async [types.getOrders] ({ commit }, page = 1) {
    const [error, data] = await getOrders(page)

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
