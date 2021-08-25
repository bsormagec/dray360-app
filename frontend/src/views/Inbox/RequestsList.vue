<template>
  <div class="requests__list__wrapper">
    <div class="request__filters">
      <Filters
        ref="requestFilters"
        :search="initFilters.search"
        :date-range="initFilters.dateRange"
        :status="initFilters.status"
        :system-status="initFilters.systemStatus"
        :company-id="initFilters.companyId"
        :update-type="initFilters.updateType"
        :display-hidden="initFilters.displayHidden"
        :hidden-items-filter="true"
        hidden-items-text="Show Completed Requests"
        hidden-items-label="Completed Requests Shown"
        @change="filtersUpdated"
      />
      <v-btn
        outlined
        dense
        small
        icon
        color="primary"
        class="refresh__button"
        :loading="loading"
        @click="refreshRequests"
      >
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
      <LockButtonEnabler
        v-if="hasPermission('supervise-view')"
        class-name="mr-2"
      />
    </div>
    <v-divider />
    <v-virtual-scroll
      ref="virtualScroll"
      :items="items.length ? items : [ 'empty' ]"
      :class="{'empty__list': items.length === 0}"
      :item-height="105"
      max-height="100%"
    >
      <template v-slot="{ item: request }">
        <div
          v-if="request !== 'empty'"
        >
          <RequestItem
            :request="request"
            :active="requestIdSelected === request.request_id"
            @change="handleChange"
            @request-deleted="requestDeleted"
            @reload-request="refreshRequests"
          />
          <v-divider />
        </div>
        <div v-else-if="!loading">
          <div class="empty__item">
            <img
              src="../../assets/images/container.png"
              width="40%"
            >
            <span class="primary--text font-weight-light h6 mt-8">
              No requests...
            </span>
            <v-btn
              class="mt-8"
              dense
              small
              dark
              color="blue-light"
              @click="clearFilters"
            >
              clear filters
            </v-btn>
          </div>
        </div>
      </template>
    </v-virtual-scroll>
    <v-overlay
      :value="loading"
      :absolute="true"
      :opacity="0.3"
    >
      <v-progress-circular
        indeterminate
        size="64"
      />
    </v-overlay>
    <v-snackbar
      v-model="changesDetected"
      :timeout="-1"
    >
      <div class="refresh-msg d-flex align-center justify-space-between">
        <p>New requests available.</p>
        <v-btn
          text
          @click="reloadPage"
        >
          REFRESH
        </v-btn>
      </div>
    </v-snackbar>
  </div>
</template>
<script>

import Filters from '@/components/OrderTable/components/filters'
import RequestItem from './RequestItem'
import LockButtonEnabler from '@/components/LockButtonEnabler'

import { mapActions, mapState } from 'vuex'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import requestsList, { types as requestsListTypes } from '@/store/modules/requests-list'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'

import { getRequests } from '@/store/api_calls/requests'
import { getRequestFilters } from '@/utils/filters_handling'

import { formatDate } from '@/utils/dates'
import permissions from '@/mixins/permissions'
import locks from '@/mixins/locks'
import isMobile from '@/mixins/is_mobile'
import statusUpdatesSubscribe from '@/mixins/status_updates_subscribe'
import { objectLocks } from '@/enums/app_objects_types'
import events from '@/enums/events'
import cloneDeep from 'lodash/cloneDeep'

export default {
  name: 'RequestList',
  components: {
    Filters,
    RequestItem,
    LockButtonEnabler,
  },
  mixins: [permissions, isMobile, locks, statusUpdatesSubscribe],
  data () {
    return {
      bottom: false,
      requestIdSelected: null,
      page: 1,
      meta: {},
      loading: false,
      filters: [],
      initFilters: {
        search: '',
        dateRange: [],
        status: [],
        systemStatus: [],
        companyId: [],
        displayHidden: false,
        updateType: ''
      },
      // polling stuff
      pollingInterval: 10000,
      pollingTimer: null,
      changesDetected: false,
      initialTotalItems: 0
    }
  },
  computed: {
    ...mapState(orders.moduleName, {
      reloadRequests: state => state.reloadRequests
    }),
    ...mapState(requestsList.moduleName, {
      items: state => state.requests,
      supervise: state => state.supervise,
    }),
    requestSelected () {
      return this.items.filter(item => item.request_id === this.requestIdSelected)[0] || {}
    }
  },
  watch: {
    bottom (isBottom) {
      if (this.loading || !isBottom || this.page === this.meta.last_page) {
        return
      }
      this.page++
      this.startLoading()
      this.fetchRequests()
    },
    reloadRequests () {
      if (this.reloadRequests) {
        this.filtersUpdated([])
        this.setReloadRequests(false)
      }
    }
  },
  created () {
    const params = this.$route.query

    this.page = params.page || 1
    this.initFilters.search = params.search
    this.initFilters.dateRange = params.dateRange?.split(',')
    this.initFilters.status = params.status?.split(',')
    this.initFilters.displayHidden = !!params.displayHidden
    this.initFilters.systemStatus = params.system_status?.split(',')
    this.initFilters.companyId = params.company_id?.split(',')
      .map(item => parseInt(item))
      .filter(item => !isNaN(item))
    this.initFilters.updateType = params.updateType
    this.requestIdSelected = params.selected || null
  },

  async mounted () {
    this.addScrollEventToFetchMoreRequests()
    this.startLoading()
    this.initializeFilters()
    this.setURLParams()
    await this.fetchRequests()

    this.initialTotalMeta = this.meta.total
    this.startPolling()
    this.initializeLockingListeners()
    this.initializeStateUpdatesListeners()

    if (this.requestIdSelected) {
      this.handleChange({ ...this.requestSelected })
    }
  },

  beforeDestroy () {
    this.stopPolling()
    this.stopRefreshingLock()
    this.$echo.leave('object-locking')
    this.leaveRequestStatusUpdatesChannel()
    this.releaseLockRequest({ requestId: this.requestIdSelected })
    this.resetPagination()
  },

  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setConfirmationDialog]),
    ...mapActions(orders.moduleName, {
      setReloadRequests: ordersTypes.setReloadRequests,
    }),
    ...mapActions(requestsList.moduleName, {
      setRequests: requestsListTypes.setRequests,
      appendRequests: requestsListTypes.appendRequests,
      updateRequestStatus: requestsListTypes.updateRequestStatus,
    }),
    formatDate,

    clearFilters () {
      this.$refs.requestFilters.clearFilters()
    },

    async filtersUpdated (filters) {
      this.filters = [...filters]
      this.startLoading()
      this.resetPagination()
      this.setURLParams()
      await this.fetchRequests()
      this.handleChange({ request_id: null, lock: null })
    },

    async requestDeleted () {
      await this.refreshRequests()
      this.handleChange({ request_id: null, lock: null })
    },

    async refreshRequests () {
      this.$root.$emit(events.requestsRefreshed)
      this.startLoading()
      this.resetPagination()
      this.setURLParams()
      await this.fetchRequests()
    },

    initializeFilters () {
      this.filters = [...this.$refs.requestFilters.getActiveFilters()]
    },

    initializeStateUpdatesListeners () {
      this.listenToRequestStatusUpdates(({ latestStatus, requestId } = {}) => {
        if (latestStatus.order_id) {
          return
        }

        this.updateRequestStatus({ latestStatus })

        if (requestId !== this.requestIdSelected) {
          return
        }

        const index = this.items.findIndex(item => item.request_id === this.requestIdSelected)

        if (index === -1) {
          return
        }

        this.handleChange(cloneDeep(this.items[index]))
      })
    },

    initializeLockingListeners () {
      this.$root.$on(events.lockClaimed, request => this.startRefreshingLock(request.request_id))
      this.$root.$on(events.lockReleased, request => this.stopRefreshingLock())
      this.$root.$on(events.lockRefreshFailed, request => this.stopRefreshingLock())
      if (!this.hasPermission('object-locks-create')) {
        return
      }
      this.$echo.private('object-locking')
        .listen(events.objectLocked, (e) => {
          const { objectLock: lock = undefined } = e

          if (!lock || this.userOwnsLock(lock)) {
            return
          }

          if (
            lock.lock_type === objectLocks.lockTypes.claimLock &&
            lock.object_id === this.requestIdSelected &&
            !this.requestSelected.is_locked
          ) {
            this.setConfirmationDialog({
              title: 'Edit-lock taken for this request',
              text: `${lock.user.name} took the edit-lock for this request`,
              confirmText: 'Ok',
              noWrap: true,
              cancelText: '',
              onConfirm: () => {},
              onCancel: () => {}
            })
          }

          this.wsLockRequest({ requestId: lock.object_id, lock })
          this.$root.$emit(events.objectLocked, e)
        })
        .listen(events.objectUnlocked, async (e) => {
          const { objectLock: lock = undefined } = e

          if (!lock || this.userOwnsLock(lock)) {
            return
          }

          this.wsReleaseLockRequest({ requestId: lock.object_id })
          this.$root.$emit(events.objectUnlocked, e)
          if (
            this.requestIdSelected !== lock.object_id ||
            !this.hasPermission('object-locks-create') ||
            this.supervise
          ) {
            return
          }

          await this.setConfirmationDialog({
            title: 'Request Unlocked',
            text: 'Do you want to take the edit-lock?',
            noWrap: true,
            onConfirm: () => {
              this.attemptToLockRequest({
                requestId: this.requestSelected.request_id,
                lockType: objectLocks.lockTypes.selectRequest,
                updateList: true
              })
              // this.handleChange({ ...this.requestSelected, lock: null, is_locked: false, })
              this.$root.$emit(events.lockClaimed, this.requestSelected)
            },
            onCancel: () => {}
          })
        })
    },

    addScrollEventToFetchMoreRequests () {
      this.$refs.virtualScroll.$el.addEventListener('scroll', () => {
        this.bottom = this.isBottomVisible()
      })
    },

    isBottomVisible () {
      const element = this.$refs.virtualScroll.$el
      const visible = element.clientHeight
      const pageHeight = element.scrollHeight
      const bottomOfPage = visible + element.scrollTop >= pageHeight
      return bottomOfPage || pageHeight < visible
    },

    async fetchRequests () {
      const [error, data] = await getRequests(this.getRequestFilters())

      if (error !== undefined) {
        return
      }

      this.appendRequests(data.data)
      this.meta = data.meta
      this.loading = false
    },

    getRequestFilters () {
      const filterKeyMap = {
        request_id: 'filter[request_id]',
        search: 'filter[query]',
        dateRange: 'filter[created_between]',
        displayHidden: 'filter[show_done]',
        system_status: 'filter[status]',
        status: 'filter[display_status]', // Processing, Exception, Rejected, Intake, Processed, Sending to TMS, Sent to TMS, Accepted by TMS
        company_id: 'filter[company_id]',
        selected: 'selected',
        page: 'page',
        sort: 'sort'
      }

      return getRequestFilters(this.getFilters(), filterKeyMap)
    },

    getFilters () {
      return [
        ...this.filters,
        { type: 'page', value: this.page },
        { type: 'selected', value: this.requestIdSelected }
      ]
    },

    setURLParams () {
      const filters = [...this.getFilters()].filter(item => item.type !== 'page')
      const filterState = filters.reduce((o, element) => ({ ...o, [element.type]: Array.isArray(element.value) ? element.value.join(',') : element.value }), {})

      this.$router.replace({ path: 'inbox', query: filterState }).catch(() => {})
    },

    async handleChange (request) {
      await this.handleRequestLock(this.requestSelected, request)
      this.requestIdSelected = request.request_id
      this.$emit('change', request)
      this.setURLParams()
    },

    async handleRequestLock (oldRequest, newRequest) {
      if (this.shouldReleaseLock(oldRequest, newRequest)) {
        await this.releaseLockRequest({ requestId: oldRequest.request_id, updateList: true })
        this.stopRefreshingLock()
      }

      if (this.shouldOmitAutolocking(newRequest)) {
        return
      }

      if (this.userOwnsLock(newRequest.lock)) {
        this.refreshCurrentLock(newRequest.request_id)
        this.startRefreshingLock(newRequest.request_id)
        return
      }

      if (newRequest.request_id === null) return

      this.attemptToLockRequest({
        requestId: newRequest.request_id,
        lockType: objectLocks.lockTypes.selectRequest,
        updateList: true
      })
    },

    startLoading () {
      this.loading = true
    },

    resetPagination () {
      this.page = 1
      this.setRequests([])
    },

    async startPolling () {
      this.initialTotalItems = await this.getTotalRequests()
      this.pollingTimer = window.setInterval(this.checkForChanges.bind(this), this.pollingInterval)
    },

    async checkForChanges () {
      const totalItems = await this.getTotalRequests()
      if (this.initialTotalItems < totalItems) {
        this.changesDetected = true
        this.initialTotalItems = totalItems
      } else {
        this.initialTotalItems = this.totalItems
      }
    },

    async getTotalRequests () {
      const [error, data] = await getRequests([])

      if (error !== undefined) {
        return this.initialTotalItems
      }

      return data.meta.total
    },

    stopPolling () {
      window.clearInterval(this.pollingTimer)
    },

    reloadPage () {
      this.changesDetected = false
      this.refreshRequests()
    },
  }
}
</script>
<style lang="scss" scoped>
.requests__list__wrapper {
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
  .request__filters {
    display: flex;
    flex-grow: 0;
    padding: rem(6) 0 rem(6);
    .refresh__button {
      margin-left: auto;
      margin-right: rem(8);
    }
  }

  .empty__list {
    background-color: #F5F6F7;
    .empty__item {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding-top: rem(85);
    }
  }
}
</style>
