import { updateOrderDetail, updateAllOrders } from '@/store/api_calls/orders'
import { getHighlights, parseChanges, baseHighlight } from '@/utils/order_form_general_functions'
import cloneDeep from 'lodash/cloneDeep'
import requestList from './requests-list'

export const types = {
  setFormOrder: 'SET_FORM_ORDER',
  setHighlights: 'SET_HIGHLIGHTS',
  setHighlight: 'SET_HIGHLIGHT',
  setOrderLock: 'SET_ORDER_LOCKED',
  setPage: 'SET_PAGE',
  updateOrder: 'UPDATE_ORDER',
  toggleEdit: 'TOGGLE_EDIT',
  loadHighlights: 'LOAD_HIGHLIGHTS',
  startHover: 'START_HOVER',
  stopHover: 'STOP_HOVER',
  startFieldEdit: 'START_FIELD_EDIT',
  stopFieldEdit: 'STOP_FIELD_EDIT',
  addHighlight: 'ADD_HIGHLIGHT',
  setBackupOrder: 'SET_BACKUP_ORDER',
  cancelEdit: 'CANCEL_EDIT',
  updateOrderStatus: 'UPDATE_ORDER_STATUS',
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
    inventory: { id: 'inventory-section', label: 'Inventory', subsection: false }
  }
}

const mutations = {
  [types.setFormOrder] (state, order) {
    state.order = { ...order }
    state.isLocked = false
  },
  [types.updateOrder] (state, { changes }) {
    state.order = {
      ...(state.order),
      ...changes
    }
  },
  [types.toggleEdit] (state) {
    state.editMode = !state.editMode
  },
  [types.setHighlights] (state, { highlights, pages }) {
    state.highlights = highlights
    state.pages = pages
  },
  [types.setHighlight] (state, { path, highlight }) {
    state.highlights[path] = { ...state.highlights[path], ...highlight }
  },
  [types.setBackupOrder] (state, order) {
    state.backupOrder = { ...order }
  },
  [types.setPage] (state, { index, page }) {
    state.pages[index] = { ...page }
  },
  [types.setOrderLock] (state, { locked, lock, ocrRequestLocked }) {
    state.order.is_locked = locked
    state.order.lock = lock
    if (ocrRequestLocked !== undefined) {
      state.order.ocr_request_is_locked = ocrRequestLocked
    }
  },
  [types.updateOrderStatus] (state, { latestStatus }) {
    if (state.order.id !== latestStatus.order_id) {
      return
    }

    const order = cloneDeep(state.order)
    order.ocr_request.latest_ocr_request_status = latestStatus

    state.order = order
    state.backupOrder = cloneDeep(order)
  },
}

const actions = {
  [types.addHighlight] ({ commit }, path) {
    commit(types.setHighlight, { path, highlight: baseHighlight({}) })
  },
  [types.setFormOrder] ({ commit, dispatch }, order) {
    commit(types.setFormOrder, order)
    dispatch(types.loadHighlights, order)
  },
  async [types.updateOrder] ({ commit, state }, { path, value, useOrder = false, saveAll = false }) {
    let changes = {}

    if (useOrder) {
      changes = { ...state.order }
    } else {
      changes = parseChanges({ path, value, originalOrder: state.order })
    }

    if (!useOrder && state.editMode) {
      commit(types.updateOrder, { changes })
      return [undefined]
    }

    commit(types.setHighlight, { path, highlight: { loading: true } })

    let error
    let data

    if (saveAll) {
      [error, data] = await updateAllOrders({ id: state.order.id, changes, path })
    } else {
      [error, data] = await updateOrderDetail({ id: state.order.id, changes })
    }

    if (error === undefined) {
      commit(types.setFormOrder, data)
    }

    commit(types.setHighlight, { path, highlight: { loading: false } })
    return [error, data]
  },
  [types.toggleEdit] ({ commit, dispatch, state }, { saveAll = false } = { saveAll: false }) {
    const { editMode } = state
    commit(types.toggleEdit)
    const newEditMode = !editMode

    if (editMode === true && newEditMode === false) {
      commit(types.setBackupOrder, {})
      dispatch(types.updateOrder, { useOrder: true, saveAll })
    } else {
      commit(types.setBackupOrder, { ...cloneDeep(state.order) })
    }
  },
  [types.cancelEdit] ({ state, commit, dispatch }) {
    commit(types.toggleEdit)
    commit(types.setFormOrder, { ...cloneDeep(state.backupOrder) })
    commit(types.setBackupOrder, {})
  },
  [types.loadHighlights] ({ commit, state }, order) {
    const pages = []
    for (const key in order.ocr_data.page_index_filenames.value) {
      pages.push({
        number: key,
        loaded: false,
        name: order.ocr_data.page_index_filenames.value[key].name,
        image: order.ocr_data.page_index_filenames.value[key].presigned_download_uri
      })
    }

    commit(types.setHighlights, { highlights: getHighlights(order), pages })
  },
  [types.startHover] ({ commit, state }, { path }) {
    if (state.highlights[path].hoverTimeout) clearTimeout(state.highlights[path].hoverTimeout)

    const hoverTimeout = setTimeout(() => {
      commit(types.setHighlight, { path, highlight: { hover: true } })
    }, 200)
    commit(types.setHighlight, { path, highlight: { hoverTimeout } })
  },
  [types.stopHover] ({ commit, state }, { path }) {
    if (state.highlights[path].hoverTimeout) clearTimeout(state.highlights[path].hoverTimeout)
    commit(types.setHighlight, { path, highlight: { hover: false } })
  },
  [types.startFieldEdit] ({ commit, state }, { path }) {
    if (path.includes('bill_to_address') || path.includes('order_address_events')) {
      return
    }
    commit(types.setHighlight, { path, highlight: { edit: true } })
  },
  [types.stopFieldEdit] ({ commit, state }, { path }) {
    commit(types.setHighlight, { path, highlight: { edit: false } })
  },
  [types.setPage] ({ commit, state }, { index, page }) {
    commit(types.setPage, { index, page: { ...page } })
  },
  [types.setOrderLock] ({ commit, state }, { locked, lock, ocrRequestLocked }) {
    commit(types.setOrderLock, { locked, lock, ocrRequestLocked })
  },
  [types.updateOrderStatus] ({ commit }, { latestStatus }) {
    commit(types.updateOrderStatus, { latestStatus })
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
