import { reqStatus } from '@/enums/req_status'
import { getOrders, getOrderDetail } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  setCurrentOrder: 'SET_CURRENT_ORDER',
  getOrders: 'GET_ORDERS',
  getOrderDetail: 'GET_ORDER_DETAIL'
}

const initialState = {
  list: [],
  links: {},
  meta: {},
  currentOrder: {}
}

const mutations = {
  [types.setOrders] (state, { data, links, meta }) {
    state.list = data
    state.links = links
    state.meta = meta
  },
  [types.setCurrentOrder] (state, orderData) {
    state.currentOrder = orderData
  }
}

const actions = {
  async [types.getOrders] ({ commit }, page) {
    const [error, data] = await getOrders(page)

    if (error) return reqStatus.error

    commit(types.setOrders, data)
    return reqStatus.success
  },

  async [types.getOrderDetail] ({ commit }, order) {
    const [error, data] = await getOrderDetail(order)

    if (error || !data.ocr_data) return reqStatus.error

    commit(types.setCurrentOrder, data)
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
