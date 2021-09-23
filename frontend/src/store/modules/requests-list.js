import { postLockObject, deleteReleaseLock } from '@/store/api_calls/object_locks'
import { objectLocks, statuses } from '@/enums/app_objects_types'
import toBool from '@/utils/to_bool'
import { requestMatchesFilters } from '@/utils/requests_list'
import cloneDeep from 'lodash/cloneDeep'
import get from 'lodash/get'

const superviseLocaStorageKey = 'TOGGLE_SUPERVISE'

const mutationTypes = {
  setRequests: 'SET_REQUESTS',
  appendRequests: 'APPEND_REQUESTS',
  setRequestLock: 'SET_REQUEST_LOCK',
  setSupervise: 'SET_SUPERVISE',
  updateRequestStatus: 'UPDATE_REQUEST_STATUS',
  updateNewRequests: 'UPDATE_NEW_REQUESTS',
  setSelectedRequest: 'SET_SELECTED_REQUEST',
  setFilters: 'SET_FILTERS',
}

export const actionTypes = {
  setRequests: 'setRequests',
  appendRequests: 'appendRequests',
  lockRequest: 'lockRequest',
  releaseLockRequest: 'releaseLockRequest',
  wsLockRequest: 'wsLockRequest',
  wsReleaseLockRequest: 'wsReleaseLockRequest',
  toggleSupervise: 'toggleSupervise',
  setSupervise: 'setSupervise',
  updateRequestStatus: 'updateRequestStatus',
  updateNewRequests: 'updateNewRequests',
  selectRequest: 'selectRequest',
  setFilters: 'setFilters',
}

const initialState = {
  filters: [],
  requests: [],
  newRequestIds: [],
  selectedRequestId: null,
  supervise: toBool(localStorage.getItem(superviseLocaStorageKey)) || false,
}

const mutations = {
  [mutationTypes.setRequests] (state, { requests }) {
    state.requests = requests
    state.newRequestIds = []
  },

  [mutationTypes.appendRequests] (state, { requests }) {
    state.requests = state.requests.concat(requests)
  },

  [mutationTypes.setRequestLock] (state, { requestId, lock, markAsLocked }) {
    const newList = state.requests.map(request => {
      if (request.request_id !== requestId) {
        return request
      }

      return { ...request, lock, is_locked: markAsLocked }
    })

    state.requests = newList
  },

  [mutationTypes.setSupervise] (state, value) {
    state.supervise = value
    localStorage.setItem(superviseLocaStorageKey, value)
  },

  [mutationTypes.updateRequestStatus] (state, { latestStatus }) {
    const index = state.requests.findIndex(item => item.request_id === latestStatus.request_id)
    if (index === -1) {
      return
    }

    if (!requestMatchesFilters(latestStatus, state.filters) && latestStatus.request_id !== state.selectedRequestId) {
      state.requests.splice(index, 1)
      return
    }

    const newRequest = cloneDeep(state.requests[index])
    newRequest.latest_ocr_request_status = latestStatus

    // Check if we have new orders in the latest state to update the respective request
    const orderIdList = get(latestStatus, 'status_metadata.order_id_list', [])
    if (orderIdList.length > 0) {
      newRequest.first_order_id = orderIdList[0]
      newRequest.orders_count = orderIdList.length
    }

    state.requests.splice(index, 1, newRequest)
  },

  [mutationTypes.updateNewRequests] (state, { latestStatus }) {
    // Check if we have new request in the latest state to show the snackbar and refresh button
    const hasNewRequestStatus = [
      statuses.intakeStarted,
      statuses.intakeRejected,
      statuses.uploadRequested
    ].includes(latestStatus.status)

    if (
      !hasNewRequestStatus ||
      state.newRequestIds.includes(latestStatus.request_id) ||
      state.requests.findIndex(item => item.request_id === latestStatus.request_id) !== -1
    ) {
      return
    }

    state.newRequestIds.push(latestStatus.request_id)
  },

  [mutationTypes.setSelectedRequest] (state, { requestId }) {
    state.selectedRequestId = requestId
  },

  [mutationTypes.setFilters] (state, { filters }) {
    state.filters = [...filters]
  },
}

const actions = {
  [actionTypes.setRequests] ({ commit }, requests) {
    commit(mutationTypes.setRequests, { requests })
  },

  [actionTypes.appendRequests] ({ commit }, requests) {
    commit(mutationTypes.appendRequests, { requests })
  },

  async [actionTypes.lockRequest] ({ commit, state }, { requestId, lockType, updateList = false, markAsLocked = true }) {
    const [error, data] = await postLockObject({
      object_id: requestId,
      lock_type: lockType,
      object_type: objectLocks.objectTypes.request
    })

    if (error !== undefined) {
      return [error, data]
    }

    if (updateList) {
      commit(mutationTypes.setRequestLock, { requestId, lock: data, markAsLocked })
    }

    return [error, data]
  },

  async [actionTypes.releaseLockRequest] ({ commit }, { requestId, updateList = false }) {
    const [error, data] = await deleteReleaseLock({
      object_id: requestId,
      object_type: objectLocks.objectTypes.request
    })

    if (error !== undefined) {
      return [error, data]
    }

    if (updateList) {
      commit(mutationTypes.setRequestLock, { requestId, lock: null, markAsLocked: false })
    }

    return [error, data]
  },

  [actionTypes.wsLockRequest] ({ commit }, { requestId, lock }) {
    if (!lock || !lock.object_id) {
      return
    }

    commit(mutationTypes.setRequestLock, { requestId, lock, markAsLocked: true })
  },

  async [actionTypes.wsReleaseLockRequest] ({ commit }, { requestId }) {
    if (!requestId) {
      return
    }

    commit(mutationTypes.setRequestLock, { requestId, lock: null, markAsLocked: false })
  },

  [actionTypes.toggleSupervise] ({ commit, state }) {
    commit(mutationTypes.setSupervise, !state.supervise)
  },

  [actionTypes.setSupervise] ({ commit }, value) {
    commit(mutationTypes.setSupervise, value)
  },

  [actionTypes.updateRequestStatus] ({ commit }, { latestStatus }) {
    commit(mutationTypes.updateRequestStatus, { latestStatus })
    commit(mutationTypes.updateNewRequests, { latestStatus })
  },

  [actionTypes.selectRequest] ({ commit }, { requestId }) {
    commit(mutationTypes.setSelectedRequest, { requestId })
  },

  [actionTypes.setFilters] ({ commit }, filters) {
    commit(mutationTypes.setFilters, { filters })
  },
}

const getters = {
  selectedRequest (state) {
    const index = state.requests.findIndex(request => request.request_id === state.selectedRequestId)

    return index === -1
      ? { orders_count: 0 }
      : state.requests[index]
  }
}

export default {
  moduleName: 'REQUESTS_LIST',
  namespaced: true,
  state: initialState,
  mutations,
  actions,
  getters
}
