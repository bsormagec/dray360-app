import { reqStatus } from '@/enums/req_status'
import { updateOrderDetail } from '@/store/api_calls/orders'
import { getHighlights, parseChanges } from '@/utils/order_form_general_functions'

export const types = {
  setFormOrder: 'SET_FORM_ORDER',
  setHighlights: 'SET_HIGHLIGHTS',
  setHighlight: 'SET_HIGHLIGHT',
  updateOrder: 'UPDATE_ORDER',
  toggleEdit: 'TOGGLE_EDIT',
  loadHighlights: 'LOAD_HIGHLIGHTS',
  startHover: 'START_HOVER',
  stopHover: 'STOP_HOVER',
  startFieldEdit: 'START_FIELD_EDIT',
  stopFieldEdit: 'STOP_FIELD_EDIT'
}

const initialState = {
  order: {},
  editMode: false,
  highlights: {},
  pages: [],
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
  }
}

const actions = {
  [types.setFormOrder] ({ commit, dispatch }, order) {
    commit(types.setFormOrder, order)
    dispatch(types.loadHighlights, order)
  },
  async [types.updateOrder] ({ commit, state }, { path, value, useOrder = false }) {
    let changes = {}

    console.log('path to set highlight: ', path)

    commit(types.setHighlight, { path, highlight: { loading: true } })

    if (useOrder) {
      changes = { ...state.order }
    } else {
      changes = parseChanges({ path, value, originalOrder: state.order })
    }

    if (!useOrder && state.editMode) {
      commit(types.updateOrder, { changes })
      return
    }

    const [error, data] = await updateOrderDetail({ id: state.order.id, changes })

    if (error !== undefined) return { status: reqStatus.error, data: error }

    commit(types.setFormOrder, data)

    commit(types.setHighlight, { path, highlight: { loading: false } })

    return { status: reqStatus.success, data }
  },
  [types.toggleEdit] ({ commit, dispatch, state }) {
    const { editMode } = state
    commit(types.toggleEdit)
    const newEditMode = !editMode

    if (editMode === true && newEditMode === false) {
      dispatch(types.updateOrder, { useOrder: true })
    }
  },
  [types.loadHighlights] ({ commit, state }, order) {
    const pages = []
    for (const key in order.ocr_data.page_index_filenames.value) {
      pages.push({
        number: key,
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
  }
}

export default {
  moduleName: 'ORDER_FORM',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
