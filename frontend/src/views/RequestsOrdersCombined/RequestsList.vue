<template>
  <div class="requests__list__wrapper">
    <div class="request__filters">
      <Filters
        ref="requestFilters"
        :search="initFilters.search"
        :date-range="initFilters.dateRange"
        :status="initFilters.status"
        :system-status="initFilters.systemStatus"
        :update-type="initFilters.updateType"
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
      :items="items"
      :item-height="105"
      max-height="100%"
    >
      <template v-slot="{ item: request }">
        <RequestItem
          :request="request"
          :active="requestSelected === request.request_id"
          @change="handleChange"
        />
        <v-divider />
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
import utils, { type } from '@/store/modules/utils'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import { getRequests } from '@/store/api_calls/requests'
import { getRequestFilters } from '@/utils/filters_handling'
import { formatDate } from '@/utils/dates'
import permissions from '@/mixins/permissions'

export default {
  name: 'RequestList',
  components: {
    Filters,
    RequestItem
  },
  mixins: [permissions],
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
        updateType: ''
      },
      // polling stuff
      pollingInterval: 10000,
      pollingTimer: null,
      payload: '',
      changesDetected: false,
      initialTotalMeta: 0
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
    this.initFilters.systemStatus = params.system_status?.split(',')
    this.initFilters.updateType = params.updateType
    this.requestSelected = params.selected || null
  },
  beforeMount () {
    this[type.setSidebar]({ show: true })
  },
  async mounted () {
    this.addScrollEventToFetchMoreRequests()
    this.startLoading()
    this.initializeFilters()
    this.setURLParams()
    await this.fetchRequests()
    this.selectFirstRequestWithOrders()

    this.initialTotalMeta = this.meta.total
    this.startPolling()
  },

  methods: {
    ...mapActions(utils.moduleName, [type.setSidebar]),
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
      this.selectFirstRequestWithOrders()
    },
    async refreshRequests () {
      this.startLoading()
      this.resetPagination()
      this.setURLParams()
      await this.fetchRequests()
      this.selectFirstRequestWithOrders()
    },
    initializeFilters () {
      if (Object.keys(this.initFilters).some(key => this.initFilters[key] && this.initFilters[key].length > 0)) {
        this.filters = [...this.$refs.requestFilters.getActiveFilters()]
      }
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
      const [error, { data, meta }] = await getRequests(this.getRequestFilters())

      if (error !== undefined) {
        return
      }

      this.items = this.items.concat(data)
      this.meta = meta
      this.loading = false
    },
    getRequestFilters () {
      const filterKeyMap = {
        request_id: 'filter[request_id]',
        search: 'filter[query]',
        dateRange: 'filter[created_between]',
        system_status: 'filter[status]',
        status: 'filter[display_status]', // Processing, Exception, Rejected, Intake, Processed, Sending to TMS, Sent to TMS, Accepted by TMS
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
    selectFirstRequestWithOrders () {
      const filteredRequests = this.items.filter(request => request.orders_count !== 0)

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
      const initPayload = await getRequests([])
      this.payload = JSON.stringify(initPayload)
      this.pollingTimer = window.setInterval(this.checkForChanges.bind(this), this.pollingInterval)
    },

    async checkForChanges () {
      if (this.initialTotalMeta < this.meta.total) {
        this.changesDetected = true
      }
    },

    stopPolling () {
      window.clearInterval(this.pollingTimer)
    },

    reloadPage () {
      this.changesDetected = false
      // window.location.reload()
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
