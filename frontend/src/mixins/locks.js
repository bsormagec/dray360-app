import { mapActions, mapState } from 'vuex'
import requestsList, { types as requestsListTypes } from '@/store/modules/requests-list'
import utils, { type } from '@/store/modules/utils'
import { putRefreshLock } from '@/store/api_calls/object_locks'
import { objectLocks } from '@/enums/app_objects_types'
import events from '@/enums/events'
import auth from '@/store/modules/auth'
import permissions from '@/mixins/permissions'
import { isInProcessing } from '@/utils/status_helpers'

export default {
  mixins: [permissions],

  data: () => ({
    lockRefresher: null,
  }),

  computed: {
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    }),
    ...mapState(requestsList.moduleName, {
      supervise: state => state.supervise,
    }),
  },

  methods: {
    ...mapActions(requestsList.moduleName, {
      lockRequest: requestsListTypes.lockRequest,
      releaseLockRequest: requestsListTypes.releaseLockRequest,
      wsLockRequest: requestsListTypes.wsLockRequest,
      wsReleaseLockRequest: requestsListTypes.wsReleaseLockRequest,
    }),

    ...mapActions(utils.moduleName, [type.setSnackbar]),

    userOwnsLock (lock) {
      if (!lock) {
        return false
      }

      return lock.user_id === this.currentUser.id
    },

    async attemptToLockRequest ({ requestId, lockType, updateList, startRefresh = true }) {
      const [error] = await this.lockRequest({
        requestId,
        lockType,
        updateList,
        markAsLocked: false
      })

      if (error !== undefined && error.response.status === 409) {
        this[type.setSnackbar]({ show: true, message: 'Request is already locked' })
      }

      if (startRefresh) {
        this.startRefreshingLock(requestId)
      }
    },

    async startRefreshingLock (requestId) {
      if (this.lockRefresher) {
        this.stopRefreshingLock()
      }

      this.lockRefresher = window.setInterval(
        () => this.refreshCurrentLock(requestId),
        objectLocks.refreshIntervalTime
      )
    },

    async refreshCurrentLock (requestId) {
      const [error] = await putRefreshLock({
        object_id: requestId,
        object_type: objectLocks.objectTypes.request,
      })

      if (error !== undefined && error.response.status === 409) {
        this.$root.$emit(events.lockRefreshFailed, { request_id: requestId })
        this.stopRefreshingLock()
      }
    },

    shouldOmitAutolocking (requestToAutoLock) {
      const inProcessing = isInProcessing(requestToAutoLock?.latest_ocr_request_status?.display_status)

      return this.supervise ||
        requestToAutoLock.is_locked ||
        !this.hasPermission('object-locks-create') ||
        (inProcessing && !this.hasPermission('auto-lock-processing-create')) ||
        (!inProcessing && !this.hasPermission('auto-lock-not-processing-create'))
    },

    shouldReleaseLock (oldRequest, newRequest) {
      return oldRequest.request_id && newRequest.request_id &&
        oldRequest.request_id !== newRequest.request_id &&
        this.userOwnsLock(oldRequest.lock) &&
        this.hasPermission('object-locks-create')
    },

    stopRefreshingLock () {
      window.clearInterval(this.lockRefresher)
    },
  }
}
