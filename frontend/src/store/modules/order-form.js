import { updateOrderDetail, updateAllOrders } from '@/store/api_calls/orders'
import { getHighlights, parseChanges, baseHighlight } from '@/utils/order_form_general_functions'
import cloneDeep from 'lodash/cloneDeep'
import get from 'lodash/get'
import requestList from './requests-list'

const mutationTypes = {
  setFormOrder: 'SET_FORM_ORDER',
  updateOrder: 'UPDATE_ORDER',
  toggleEdit: 'TOGGLE_EDIT',
  setHighlights: 'SET_HIGHLIGHTS',
  setHighlight: 'SET_HIGHLIGHT',
  setBackupOrder: 'SET_BACKUP_ORDER',
  setPage: 'SET_PAGE',
  setOrderLock: 'SET_ORDER_LOCKED',
  updateOrderStatus: 'UPDATE_ORDER_STATUS',
}

export const actionTypes = {
  addHighlight: 'addHighlight',
  setFormOrder: 'setFormOrder',
  updateOrder: 'updateOrder',
  toggleEdit: 'toggleEdit',
  cancelEdit: 'cancelEdit',
  loadHighlights: 'loadHighlights',
  startHover: 'startHover',
  stopHover: 'stopHover',
  startFieldEdit: 'startFieldEdit',
  stopFieldEdit: 'stopFieldEdit',
  setPage: 'setPage',
  setOrderLock: 'setOrderLock',
  updateOrderStatus: 'updateOrderStatus',
  clearErrors: 'clearErrors',
}

const initialState = {
  order: {},
  backupOrder: {},
  editMode: false,
  highlights: {},
  pages: [],
  isLocked: false,
  sections: {
    shipment: { id: 'shipment-section', label: 'Shipment', subsection: false },
    equipment: { id: 'equipment-subsection', label: 'Equipment', subsection: true, parent: 'shipment' },
    origin: { id: 'origin-subsection', label: 'Shipment Details', subsection: true, parent: 'shipment' },
    bill_to: { id: 'bill-to-subsection', label: 'Bill To', subsection: true, parent: 'shipment' },
    charges: { id: 'charges-subsection', label: 'Charges', subsection: true, parent: 'shipment' },
    // pickup: { id: 'pickup-section', label: 'Pickup', subsection: false },
    itinerary: { id: 'itinerary-section', label: 'Itinerary', subsection: false },
    notes: { id: 'notes-section', label: 'Notes', subsection: false },
    inventory: { id: 'inventory-section', label: 'Inventory', subsection: false },
    ssrr_location_address: { id: 'ssrr-location-section', label: 'SSRR Location', subsection: true, parent: 'shipment' }
  }
}

const mutations = {
  [mutationTypes.setFormOrder] (state, order) {
    state.order = { ...order }
    state.isLocked = false
  },

  [mutationTypes.updateOrder] (state, { changes }) {
    state.order = {
      ...(state.order),
      ...changes
    }
  },

  [mutationTypes.toggleEdit] (state) {
    state.editMode = !state.editMode
  },

  [mutationTypes.setHighlights] (state, { highlights, pages }) {
    state.highlights = highlights
    state.pages = pages
  },

  [mutationTypes.setHighlight] (state, { path, highlight }) {
    state.highlights[path] = { ...state.highlights[path], ...highlight }
  },

  [mutationTypes.setBackupOrder] (state, order) {
    state.backupOrder = { ...order }
  },

  [mutationTypes.setPage] (state, { index, page }) {
    state.pages[index] = { ...page }
  },

  [mutationTypes.setOrderLock] (state, { locked, lock, ocrRequestLocked }) {
    state.order.is_locked = locked
    state.order.lock = lock
    if (ocrRequestLocked !== undefined) {
      state.order.ocr_request_is_locked = ocrRequestLocked
    }
  },

  [mutationTypes.updateOrderStatus] (state, { latestStatus }) {
    if (state.order.id !== latestStatus.order_id) {
      return
    }

    const order = cloneDeep(state.order)
    order.ocr_request.latest_ocr_request_status = latestStatus

    let tmsShipmentId = get(latestStatus, 'status_metadata.tms_shipment_id', null)
    tmsShipmentId = get(latestStatus, 'status_metadata.shipment_id', tmsShipmentId)

    if (!order.tms_shipment_id && tmsShipmentId) {
      order.tms_shipment_id = tmsShipmentId
    }

    state.order = order
    state.backupOrder = cloneDeep(order)
  },
}

const actions = {
  [actionTypes.addHighlight] ({ commit }, path) {
    commit(mutationTypes.setHighlight, { path, highlight: baseHighlight({}) })
  },

  [actionTypes.setFormOrder] ({ commit, dispatch }, order) {
    commit(mutationTypes.setFormOrder, order)
    dispatch(actionTypes.loadHighlights, order)
  },

  async [actionTypes.updateOrder] ({ commit, state }, { path, value, useOrder = false, saveAll = false }) {
    let changes = {}

    if (useOrder) {
      changes = { ...state.order }
    } else {
      changes = parseChanges({ path, value, originalOrder: state.order })
    }

    if (!useOrder && state.editMode) {
      commit(mutationTypes.updateOrder, { changes })
      return [undefined]
    }

    commit(mutationTypes.setHighlight, { path, highlight: { loading: true } })

    let error
    let data

    if (saveAll) {
      [error, data] = await updateAllOrders({ id: state.order.id, changes, path })
    } else {
      [error, data] = await updateOrderDetail({ id: state.order.id, changes })
    }

    if (error === undefined) {
      commit(mutationTypes.setFormOrder, data)
    }

    const errors = get(error, `response.data.errors.${path}`, [])
    commit(mutationTypes.setHighlight, { path, highlight: { loading: false, errors } })

    return [error, data]
  },

  [actionTypes.toggleEdit] ({ commit, dispatch, state }, { saveAll = false } = { saveAll: false }) {
    const { editMode } = state
    commit(mutationTypes.toggleEdit)
    const newEditMode = !editMode

    if (editMode === true && newEditMode === false) {
      commit(mutationTypes.setBackupOrder, {})
      dispatch(actionTypes.updateOrder, { useOrder: true, saveAll })
    } else {
      commit(mutationTypes.setBackupOrder, { ...cloneDeep(state.order) })
    }
  },

  [actionTypes.cancelEdit] ({ state, commit }) {
    commit(mutationTypes.toggleEdit)
    commit(mutationTypes.setFormOrder, { ...cloneDeep(state.backupOrder) })
    commit(mutationTypes.setBackupOrder, {})
  },

  [actionTypes.loadHighlights] ({ commit, state }, order) {
    const pages = []
    for (const key in order.ocr_data.page_index_filenames.value) {
      pages.push({
        number: key,
        loaded: false,
        name: order.ocr_data.page_index_filenames.value[key].name,
        image: order.ocr_data.page_index_filenames.value[key].presigned_download_uri
      })
    }

    commit(mutationTypes.setHighlights, { highlights: getHighlights(order), pages })
  },

  [actionTypes.startHover] ({ commit, state }, { path }) {
    if (state.highlights[path].hoverTimeout) clearTimeout(state.highlights[path].hoverTimeout)

    const hoverTimeout = setTimeout(() => {
      commit(mutationTypes.setHighlight, { path, highlight: { hover: true } })
    }, 200)
    commit(mutationTypes.setHighlight, { path, highlight: { hoverTimeout } })
  },

  [actionTypes.stopHover] ({ commit, state }, { path }) {
    if (state.highlights[path].hoverTimeout) clearTimeout(state.highlights[path].hoverTimeout)
    commit(mutationTypes.setHighlight, { path, highlight: { hover: false } })
  },

  [actionTypes.startFieldEdit] ({ commit, state }, { path }) {
    if (path.includes('bill_to_address') || path.includes('order_address_events')) {
      return
    }
    commit(mutationTypes.setHighlight, { path, highlight: { edit: true } })
  },

  [actionTypes.stopFieldEdit] ({ commit, state }, { path }) {
    commit(mutationTypes.setHighlight, { path, highlight: { edit: false } })
  },

  [actionTypes.setPage] ({ commit, state }, { index, page }) {
    commit(mutationTypes.setPage, { index, page: { ...page } })
  },

  [actionTypes.setOrderLock] ({ commit, state }, { locked, lock, ocrRequestLocked }) {
    commit(mutationTypes.setOrderLock, { locked, lock, ocrRequestLocked })
  },

  [actionTypes.updateOrderStatus] ({ commit }, { latestStatus }) {
    commit(mutationTypes.updateOrderStatus, { latestStatus })
  },

  [actionTypes.clearErrors] ({ commit }, { path }) {
    commit(mutationTypes.setHighlight, { path, highlight: { errors: [] } })
  },
}

const getters = {
  isMultiOrderRequest: state => {
    return state.order.siblings_count > 1
  },
  isLocked: (state, getters, rootState) => {
    return state.order.is_locked || state.isLocked || rootState[requestList.moduleName].supervise
  }
}

export default {
  moduleName: 'ORDER_FORM',
  namespaced: true,
  state: initialState,
  mutations,
  actions,
  getters
}
