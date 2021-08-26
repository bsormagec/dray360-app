import { reqStatus } from '@/enums/req_status'
import { getOrders, getOrderDetail, updateOrderDetail } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  setCurrentOrder: 'SET_CURRENT_ORDER',
  getOrders: 'GET_ORDERS',
  getOrderDetail: 'GET_ORDER_DETAIL',
  updateOrderDetail: 'UPDATE_ORDER_DETAIL',
}

const initialState = {
  list: [],
  links: {},
  meta: {},
  currentOrder: {},
}

const mutations = {
  [types.setOrders] (state, { data, links, meta }) {
    state.list = data.map(item => {
      // item.key = `${item.id}-${item.order.id || null}`
      return item
    })
    state.links = links
    state.meta = meta
  },
  [types.setCurrentOrder] (state, orderData) {
    state.currentOrder = orderData
  },
}

const actions = {
  async [types.getOrders] ({ commit }, filters) {
    const query = filters['filter[query]']
    const dateQuery = filters['filter[created_between]']
    const filtersForParams = { ...filters }
    delete filtersForParams.query
    delete filtersForParams.dateQuery

    const [error, data] = await getOrders(filtersForParams, query, dateQuery)

    if (error) return reqStatus.error

    commit(types.setOrders, data)
    return reqStatus.success
  },

  async [types.getOrderDetail] ({ commit }, order) {
    const [error, data] = await getOrderDetail(order)

    if (error) return reqStatus.error

    if (data.preceded_by_order_id) {
      data.ocr_request.latest_ocr_request_status.display_status += ' (update)'
    }

    commit(types.setCurrentOrder, data)
    return reqStatus.success
  },

  async [types.setCurrentOrder] ({ commit }, order) {
    commit(types.setCurrentOrder, order)
  },

  async [types.updateOrderDetail] ({ commit, state }, { id, changes }) {
    const [error, data] = await updateOrderDetail({ id, changes })
    let newOrder = {}

    if (!error) {
      delete data.ocr_data
      newOrder = { ...state.currentOrder, ...data, ...changes }
    } else {
      newOrder = { ...state.currentOrder, ...changes }
    }

    commit(types.setCurrentOrder, newOrder)

    if (error) return reqStatus.error
    return reqStatus.success
  },
}

export default {
  moduleName: 'ORDERS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
