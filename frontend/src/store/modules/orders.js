import { reqStatus } from '@/enums/req_status'
import { getOrders, getOrderDetail, postUploadPDF } from '@/store/api_calls/orders'

export const types = {
  setOrders: 'SET_ORDERS',
  setCurrentOrder: 'SET_CURRENT_ORDER',
  getOrders: 'GET_ORDERS',
  getOrderDetail: 'GET_ORDER_DETAIL',
  postUploadPDF: 'POST_UPLOAD_PDF'
}

const initialState = {
  list: [],
  links: {},
  meta: {},
  currentOrder: {}
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
  }
}

const actions = {
  async [types.getOrders] ({ commit }, filters) {
    const query = filters.query
    const filtersForParams = { ...filters }
    delete filtersForParams.query

    const [error, data] = await getOrders(filters, query)

    if (error) return reqStatus.error

    commit(types.setOrders, data)
    return reqStatus.success
  },

  async [types.getOrderDetail] ({ commit }, order) {
    const [error, data] = await getOrderDetail(order)

    if (error || !data.ocr_data) return reqStatus.error

    commit(types.setCurrentOrder, data)
    return reqStatus.success
  },

  async [types.postUploadPDF] ({ commit }, file) {
    const [error, data] = await postUploadPDF(file)

    if (error || !data.ocr_data) return reqStatus.error

    commit(types.setPDF, data)
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
