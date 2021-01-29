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
        v-if="isSuperadmin()"
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
    </div>
    <v-divider />
    <v-virtual-scroll
      ref="virtualScroll"
      :items="items.length ? items : [ 'empty' ]"
      :item-height="105"
      max-height="100%"
    >
      <template v-slot="{ item: request }">
        <div
          v-if="request !== 'empty'"
        >
          <RequestItem
            :request="request"
            :active="requestSelected === request.request_id"
            @change="handleChange"
            @deleteRequest="refreshRequests"
          />
          <v-divider />
        </div>
        <div v-else-if="!loading">
          <div class="d-flex justify-center px-2 py-4">
            <h5>
              Nothing found
            </h5>
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

import { mapActions, mapState } from 'vuex'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import { getRequests } from '@/store/api_calls/requests'
import { getRequestFilters } from '@/utils/filters_handling'
import { formatDate } from '@/utils/dates'
import permissions from '@/mixins/permissions'
import isMobile from '@/mixins/is_mobile'
import { statuses } from '@/enums/app_objects_types'

import get from 'lodash/get'

export default {
  name: 'RequestList',
  components: {
    Filters,
    RequestItem
  },
  mixins: [permissions, isMobile],
  props: {
    extraUrlParams: {
      type: Array,
      required: false,
      default: () => []
    }
  },
  data () {
    return {
      bottom: false,
      requestSelected: null,
      items: [],
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
    })
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
    this.requestSelected = params.selected || null
  },
  async mounted () {
    this.addScrollEventToFetchMoreRequests()
    this.startLoading()
    this.initializeFilters()
    this.setURLParams()
    await this.fetchRequests()
    this.selectFirstActiveRequest()

    this.initialTotalMeta = this.meta.total
    this.startPolling()
  },
  beforeDestroy () {
    this.stopPolling()
  },

  methods: {
    ...mapActions(orders.moduleName, {
      setReloadRequests: ordersTypes.setReloadRequests
    }),
    formatDate,
    async filtersUpdated (filters) {
      this.filters = [...filters]
      this.startLoading()
      this.resetPagination()
      this.setURLParams()
      await this.fetchRequests()
      if (!this.isMobile) {
        this.selectFirstActiveRequest()
      }
    },
    async refreshRequests () {
      this.startLoading()
      this.resetPagination()
      this.setURLParams()
      await this.fetchRequests()
      if (!this.isMobile) {
        this.selectFirstActiveRequest()
      }
    },
    initializeFilters () {
      this.filters = [...this.$refs.requestFilters.getActiveFilters()]
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

      this.items = this.items.concat(data.data)
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
        { type: 'selected', value: this.requestSelected }
      ]
    },
    setURLParams () {
      const filters = [...this.getFilters(), ...this.extraUrlParams].filter(item => item.type !== 'page')
      const filterState = filters.reduce((o, element) => ({ ...o, [element.type]: Array.isArray(element.value) ? element.value.join(',') : element.value }), {})

      this.$router.replace({ path: 'dashboard', query: filterState }).catch(() => {})
    },
    selectFirstActiveRequest () {
      const filteredRequests = this.items.filter(request => {
        if (get(request, 'latest_ocr_request_status.status', '') === statuses.ocrPostProcessingReview) {
          return this.hasPermission('admin-review-view')
        }

        return request.orders_count !== 0
      })

      if (filteredRequests.length === 0) {
        this.handleChange({ request_id: null, orders_count: 0, first_order_id: null })
        return
      }

      this.handleChange(filteredRequests[0])
    },
    handleChange (request) {
      this.requestSelected = request.request_id
      this.$emit('change', request)
      this.setURLParams()
    },
    startLoading () {
      this.loading = true
    },
    resetPagination () {
      this.page = 1
      this.items = []
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
    }

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
      margin-right: rem(16);
    }
  }
}
</style>
