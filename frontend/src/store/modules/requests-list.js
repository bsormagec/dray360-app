import { postLockObject, deleteReleaseLock } from '@/store/api_calls/object_locks'
import { objectLocks } from '@/enums/app_objects_types'

export const types = {
  setRequests: 'SET_REQUESTS',
  appendRequests: 'APPEND_REQUESTS',
  lockRequest: 'LOCK_REQUEST',
  releaseLockRequest: 'RELEASE_LOCK_REQUEST',
  wsLockRequest: 'WS_LOCK_REQUEST',
  wsReleaseLockRequest: 'WS_RELEASE_LOCK_REQUEST',
}

const initialState = {
  requests: [],
}

const mutations = {
  [types.setRequests] (state, { requests }) {
    state.requests = requests
  },
  [types.appendRequests] (state, { requests }) {
    state.requests = state.requests.concat(requests)
  },
  [types.lockRequest] (state, { requestId, lock, markAsLocked }) {
    const newList = state.requests.map(request => {
      if (request.request_id !== requestId) {
        return request
      }

      return { ...request, lock, is_locked: markAsLocked }
    })

    state.requests = newList
  },
  [types.releaseLockRequest] (state, { requestId }) {
    const newList = state.requests.map(request => {
      if (request.request_id !== requestId) {
        return request
      }

      return { ...request, lock: null, is_locked: false }
    })

    state.requests = newList
  }
}

const actions = {
  [types.setRequests] ({ commit }, requests) {
    commit(types.setRequests, { requests })
  },
  [types.appendRequests] ({ commit }, requests) {
    commit(types.appendRequests, { requests })
  },
  async [types.lockRequest] ({ commit, state }, { requestId, lockType, updateList = false, markAsLocked = true }) {
    const [error, data] = await postLockObject({
      object_id: requestId,
      lock_type: lockType,
      object_type: objectLocks.objectTypes.request
    })

    if (error !== undefined) {
      return [error, data]
    }

    if (updateList) {
      commit(types.lockRequest, { requestId, lock: data, markAsLocked })
    }

    return [error, data]
  },
  async [types.releaseLockRequest] ({ commit, state }, { requestId, updateList = false }) {
    const [error, data] = await deleteReleaseLock({
      object_id: requestId,
      object_type: objectLocks.objectTypes.request
    })

    if (error !== undefined) {
      return [error, data]
    }

    if (updateList) {
      commit(types.releaseLockRequest, { requestId })
    }

    return [error, data]
  },
  [types.wsLockRequest] ({ commit, state }, { requestId, lock }) {
    if (!lock || !lock.object_id) {
      return
    }

    commit(types.lockRequest, { requestId, lock, markAsLocked: true })
  },
  async [types.wsReleaseLockRequest] ({ commit, state }, { requestId }) {
    if (!requestId) {
      return
    }

    commit(types.releaseLockRequest, { requestId })
  }
}

const getters = {
}

export default {
  moduleName: 'REQUESTS_LIST',
  namespaced: true,
  state: initialState,
  mutations,
  actions,
  getters
}
