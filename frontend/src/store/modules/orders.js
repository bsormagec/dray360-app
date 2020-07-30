import { reqStatus } from '@/enums/req_status'
import { getOrders, getOrderDetail, updateOrderDetail, postUploadPDF, postSendToTms } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  setCurrentOrder: 'SET_CURRENT_ORDER',
  getOrders: 'GET_ORDERS',
  getOrderDetail: 'GET_ORDER_DETAIL',
  updateOrderDetail: 'UPDATE_ORDER_DETAIL',
  postUploadPDF: 'POST_UPLOAD_PDF',
  postSendToTms: 'POST_SEND_TMS',
  setSetTms: 'SET_SEND_TMS'
}

const initialState = {
  list: [],
  links: {},
  meta: {},
  currentOrder: {},
  pdf: {},
  tmsData: {}
}

const mutations = {
  [types.setOrders] (state, { data, links, meta }) {
    state.list = data.map(item => {
      item.key = `${item.id}-${item.order.id || null}`
      return item
    })
    state.links = links
    state.meta = meta
  },
  [types.setCurrentOrder] (state, orderData) {
    state.currentOrder = orderData
  },
  [types.setPDF] (state, pdfData) {
    state.pdf = pdfData
  },
  [types.setSetTms] (state, tmsData) {
    state.tmsdata = tmsData
  }
}

const actions = {
  async [types.getOrders] ({ commit }, filters) {
    const query = filters.query
    const filtersForParams = { ...filters }
    delete filtersForParams.query

    const [error, data] = await getOrders(filtersForParams, query)

    if (error) return reqStatus.error

    commit(types.setOrders, data)
    return reqStatus.success
  },

  async [types.getOrderDetail] ({ commit }, order) {
    const [error, data] = await getOrderDetail(order)

    if (error) return reqStatus.error

    commit(types.setCurrentOrder, data)
    return reqStatus.success
  },

  async [types.updateOrderDetail] ({ commit, state }, { id, changes }) {
    const [error] = await updateOrderDetail({ id, changes })
    const data = { ...state.currentOrder, ...changes }
    commit(types.setCurrentOrder, data)
    if (error) return reqStatus.error
    return reqStatus.success
  },

  async [types.postUploadPDF] ({ commit }, file) {
    const [error, data] = await postUploadPDF(file)

    if (error) return reqStatus.error

    commit(types.setPDF, data)
    return reqStatus.success
  },

  async [types.postSendToTms] ({ commit }, tmsData) {
    const [error, data] = await postSendToTms(tmsData)
    if (error) return error.message

    commit(types.setSetTms, data)
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
