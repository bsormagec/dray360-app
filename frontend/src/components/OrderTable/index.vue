<template>
  <div class="table-root">
    <v-data-table
      v-model="selected"
      :loading="loading"
      :headers="showHeaders"
      :server-items-length="total"
      :options.sync="options"
      :items="orders"
      :class="{'table': true, 'loading': loading }"
      :hide-default-footer="true"
      :header-props="{ sortIcon: 'mdi-chevron-down'}"
    >
      <template v-slot:top>
        <h6 v-if="tableTitle">
          {{ tableTitle }} ({{ total }})
        </h6>
        <v-toolbar
          flat
          color="white"
          class="table-tools"
        >
          <Filters
            ref="orderFilters"
            :search="initFilters.search"
            :date-range="initFilters.dateRange"
            :status="initFilters.status"
            :system-status="initFilters.systemStatus"
            :update-type="initFilters.updateType"
            @change="updateFilters"
          />

          <v-select
            v-model="selectedHeaders"
            :items="headers"
            color="primary"
            class="table-column-filter"
            :menu-props="{ contentClass: 'column-selector-menu', bottom: true, offsetY: true }"
            dense
            height="30"
            min-height="30"
            outlined
            hide-details
            multiple
            return-object
            :chips="true"
          >
            <template v-slot:selection="{ index }">
              <span
                v-if="index === 2"
                class=""
              > Columns</span>
            </template>
          </v-select>
        </v-toolbar>
      </template>

      <template v-slot:[`item.id`]="{ item }">
        <a :href="`/order/${item.id}`">{{ item.id }}</a>
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        {{ formatDate(item.created_at) }}
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ formatDate(item.updated_at || item.created_at) }}
      </template>
      <template v-slot:[`item.tms_shipment_id`]="{ item }">
        {{ item.tms_shipment_id }}
        <v-tooltip
          v-if="item.tms_shipment_id !== null"
          top
        >
          <template v-slot:activator="{ on }">
            <v-icon
              v-clipboard:copy="item.tms_shipment_id"
              small
              dark
              v-on="on"
              @click.stop="() =>{}"
            >
              mdi-content-paste
            </v-icon>
          </template>
          <span>Copy TMS ID</span>
        </v-tooltip>
      </template>

      <template v-slot:[`item.latest_ocr_request_status.display_status`]="{ item }">
        <span
          v-if="item.latest_ocr_request_status.display_status.toLowerCase() === 'processed'"
          class="processed-status"
        >
          {{ item.latest_ocr_request_status.display_status }}
        </span>
        <Chip
          v-else
          x-small
          v-bind="getStatusChip(item)"
        >
          {{ item.latest_ocr_request_status.display_status }}
        </Chip>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <OutlinedButtonGroup
          v-if="currentUser !== undefined && currentUser.is_superadmin"
          :main-action="{
            title: 'DETAILS',
            to: `/order/${item.id}`,
            hasPermission: hasPermission('orders-view')
          }"
          :options="[
            { title: 'Download Source File', action: () => downloadSourceFile(item.id) },
            { title: 'Reprocess Order', action: () => reprocessOrder(item.request_id) },
            { title: 'Delete Order', action: () => deleteOrder(item) }
          ]"
        />

        <v-btn
          v-else
          color="primary"
          primary
          outlined
          small
          :href="`/order/${item.id}`"
        >
          View
        </v-btn>
      </template>
      <template v-slot:no-data>
        <v-container>
          <v-row>
            <v-col
              cols="12"
              class="py-6 mt-4"
            >
              <h6>
                <strong>No Data<strong /></strong>
              </h6>
              <div class="mt-5 py-5">
                <v-btn
                  small
                  color="error"
                  class="reset-filters"
                  @click="resetFilters"
                >
                  Reset Filters
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-container>
      </template>
      <template v-slot:footer>
        <Pagination
          v-if="!waitForRequestId"
          :loading="loading"
          :page-data="meta"
          :links="links"
          @pageIndexChange="onPageIndexChange"
        />
      </template>
    </v-data-table>
    <v-snackbar
      v-model="changesDetected"
      :timeout="-1"
    >
      <div class="refresh-msg d-flex align-center justify-space-between">
        <p>New orders available.</p>
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
import auth from '@/store/modules/auth'
import Filters from './components/filters'
import Pagination from './components/Pagination'
import Chip from '@/components/Chip'
import hasPermission from '@/mixins/permissions'
import { formatDate } from '@/utils/dates'
import utils, { type as utilTypes } from '@/store/modules/utils'
import { getOrders, getSourceFileDownloadURL, reprocessOcrRequest, delDeleteOrder } from '@/store/api_calls/orders'
import { getRequestFilters } from '@/utils/filters_handling'

import { mapState, mapActions } from 'vuex'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'

export default {
  name: 'OrderTable',
  components: {
    Pagination,
    Chip,
    OutlinedButtonGroup,
    Filters
  },
  mixins: [hasPermission],
  props: {
    activePage: {
      type: Number,
      required: false,
      default: 1
    },
    itemsPerPage: {
      type: Number,
      required: false,
      default: 25
    },
    headers: {
      type: Array,
      required: false,
      default: () => [
        { text: 'Order ID', sortable: false, value: 'id' },
        { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
        { text: 'Container', sortable: false, value: 'unit_number' },
        { text: 'Bill To', value: 'bill_to_address.location_name' },
        { text: 'Direction', value: 'shipment_direction', align: 'center' },
        { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
      ]
    },
    extraUrlParams: {
      type: Array,
      required: false,
      default: () => []
    },
    requestId: {
      type: String,
      required: false,
      default: null
    },
    urlFilters: {
      type: Boolean,
      required: false,
      default: true
    },
    waitForRequestId: {
      type: Boolean,
      required: false,
      default: false
    },
    tableTitle: {
      type: String,
      required: false,
      default: ''
    }
  },
  data () {
    return {
      // polling stuff
      pollingInterval: 10000,
      pollingTimer: null,
      payload: '',
      changesDetected: false,
      // main filters array
      filters: [],
      dialog: false,
      loading: false,
      page: 1,
      requestID: this.requestId,
      options: {
      },
      minPause: 500, // 0.5 second minimum delay
      randomizeDelay: true,
      ordersPayload: null,
      // total number of returned orders
      total: 0,
      initFilters: {
        search: '',
        dateRange: [],
        status: [],
        systemStatus: [],
        updateType: '',
        page: 1
      },
      selected: [],
      selectedHeaders: [],
      orders: [],
      // sorting params
      sortDesc: false,
      sortColumn: 'created_at',
      sortColumnDefault: 'created_at',
      // pagination links
      links: null,
      // query meta data
      meta: null
    }
  },
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    showHeaders () {
      return this.headers.filter(s => {
        return this.selectedHeaders.reduce((exists, current) => {
          return exists || current.value === s.value
        }, false)
      })
    }
  },
  watch: {
    headers () {
      this.selectedHeaders = Object.values(this.headers)
    },
    options: {
      handler () {
        if (this.waitForRequestId && this.requestId === null) {
          return
        }

        const sortColumnMap = {
          shipment_direction: 'order.shipment_direction',
          'bill_to_address.location_name': 'order.bill_to_address',
          'latest_ocr_request_status.display_status': 'status'
        }
        const sortCol = sortColumnMap.hasOwnProperty(this.options.sortBy.join())
          ? sortColumnMap[this.options.sortBy.join()]
          : this.sortColumn

        this.sortColumn = sortCol
        this.sortDesc = this.options.sortDesc.join() == 'true'
        this.setURLParams()
        this.getOrderData()
      },
      deep: true
    },
    requestId () {
      this.getOrderData()
    }
  },

  created () {
    this.selectedHeaders = Object.values(this.headers)

    if (this.waitForRequestId || !this.urlFilters) {
      return
    }
    // set get params if there are any

    const params = this.$route.query
    const sortValue = params.sort ? params.sort : this.sortColumn

    this.page = params.page
    this.sortColumn = sortValue.replace('-', '')
    this.sortDesc = !sortValue.includes('-')
    this.initFilters.search = params.search
    this.initFilters.dateRange = params.dateRange?.split(',')
    this.initFilters.status = params.status?.split(',')
    this.initFilters.systemStatus = params.system_status?.split(',')
    this.initFilters.updateType = params.updateType
    this.requestID = params.request_id
    this.initFilters.page = params.page
  },

  mounted () {
    if (this.waitForRequestId) {
      return
    }

    this.initialize()
    if (this.urlFilters) {
      window.onpopstate = this.onHistoryChange.bind(this)
    }
  },

  unmounted () {
    if (this.waitForRequestId) {
      return
    }
    // remove popstate event handling
    if (this.urlFilters) {
      window.onpopstate = null
    }
    this.stopPolling()
  },

  methods: {
    formatDate,
    initialize () {
      // if there are init filters set from get params grab the active filters from the filters component before querying the DB
      if (Object.keys(this.initFilters).some(key => this.initFilters[key] && this.initFilters[key].length > 0)) {
        this.filters = [...this.$refs.orderFilters.getActiveFilters()]
      }

      // get all orders
      // this.getOrderData()

      this.startPolling()
    },

    ...mapActions(utils.moduleName, {
      setSnackbar: utilTypes.setSnackbar,
      setConfirmDialog: utilTypes.setConfirmationDialog
    }),

    // polling
    async startPolling () {
      const initPayload = await getOrders([])
      this.payload = JSON.stringify(initPayload)
      this.pollingTimer = window.setInterval(this.checkForChanges.bind(this), this.pollingInterval)
    },

    async checkForChanges () {
      const newPayload = await getOrders([])
      if (this.payload !== JSON.stringify(newPayload)) {
        this.changesDetected = true
      }
    },

    stopPolling () {
      window.clearInterval(this.pollingTimer)
    },

    reloadPage () {
      this.changesDetected = false
      window.location.reload()
    },

    async downloadSourceFile (orderId) {
      const [error, data] = await getSourceFileDownloadURL(orderId)

      if (!error) {
        // not entirely sure why this is necessary, but this is the logic for triggering a DL elsewhere in the app.
        const link = document.createElement('a')
        link.href = data.data
        link.click()
        link.remove()
      } else {
        console.log('error', error)
      }
    },

    // reprocess order
    async reprocessRequest ({ request_id }) {
      this.setConfirmDialog({
        title: 'Are you sure you want to reprocess the request associated to this order?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await reprocessOcrRequest(request_id)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ show: true, message: 'There was an error trying to send the message to reprocess' })
            return
          }

          this.setSnackbar({ show: true, message: 'Request sent for reprocessing' })
          this.loading = false
        }
      })
    },

    async deleteOrder (item) {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to delete this order?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await delDeleteOrder(item.id)
          let message = ''

          if (!error) {
            this.loading = false
            message = 'Order deleted'
            this.resetFilters()
            if (this.orders.length <= 2) {
              this.$emit('order-deleted')
            }
          } else {
            message = 'Error trying to delete the order'
          }
          await this.setSnackbar({ show: true, message })
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async getOrderData () {
      const startTime = new Date().getTime()
      this.loading = true

      const { data, links, meta } = await getOrders(this.getRequestFilters())

      const now = new Date().getTime()

      const deltaTime = now - startTime

      if (deltaTime < this.minPause) {
        this.pause(this.minPause - deltaTime).then(() => {
          this.orders = data
          this.links = links
          this.meta = meta
          this.total = this.meta.total
          this.loading = false
        })
      } else {
        this.orders = data
        this.links = links
        this.meta = meta
        this.total = this.meta.total
        this.loading = false
      }
    },
    updateFilters (filters) {
      // copy filters to local scope
      this.filters = [...filters]
      // when filters change from within the filter component always reset the pagination
      this.resetPagination()
      this.setURLParams()
      this.getOrderData()
    },

    resetPagination () {
      this.page = 1
      this.sortColumn = this.sortColumnDefault
    },

    onHistoryChange (e) {
      const { search, status, system_status, dateRange, updateType, page } = e.state

      const f = {
        search: search || '',
        dateRange: dateRange ? dateRange.split(',') : [],
        status: status ? status.split(',') : [],
        systemStatus: system_status ? system_status.split(',') : [],
        updateType: updateType || '',
        page: page || 1
      }

      // set page and sort from state if present
      this.page = e.state?.page || this.page
      this.sortColumn = e.state?.sort || this.sortColumn

      this.$refs.orderFilters.setFiltersFromState(f)
      this.filters = [...this.$refs.orderFilters.getActiveFilters()]
      this.getOrderData()
    },

    // sets url get params from current filter set.
    setURLParams () {
      if (!this.urlFilters) {
        return
      }
      const filters = [...this.getFilters(), ...this.extraUrlParams].filter(item => item.type !== 'items_per_page')
      const filterState = filters.reduce((o, element) => ({ ...o, [element.type]: Array.isArray(element.value) ? element.value.join(',') : element.value }), {})
      // const params = filters.map(element => `${element.type}=${element.value}`).join('&')
      this.$router.replace({ path: 'dashboard', query: filterState }).catch(() => {})
      // history.pushState(filterState, document.title, `${window.location.pathname}?${params}`)
    },

    // this is just for UX aesthetics. A brief pause provides feedback to the user that something is happening. Randomizing it slightly makes it feel even more natural
    pause (duration) {
      const modifiedDuration = this.randomizeDelay ? parseInt(duration + (Math.random() * 1000)) : duration
      return {
        then: (callback) => {
          window.setTimeout(callback, modifiedDuration)
        }
      }
    },

    getFilters () {
      const metaParams = [
        { type: 'page', value: this.page },
        { type: 'sort', value: this.sortDesc ? this.sortColumn : `-${this.sortColumn}` },
        // this field is stubbed in, but the number is currently hard coded in the API as 25
        { type: 'items_per_page', value: this.waitForRequestId ? 1000 : this.itemsPerPage },
        { type: 'request_id', value: this.waitForRequestId ? this.requestId : this.requestID }
      ]

      return [...this.filters, ...metaParams.filter(param => param.value)]
    },
    // format filters for endpoint interface
    getRequestFilters () {
      const filterKeyMap = {
        request_id: 'filter[request_id]',
        search: 'filter[query]',
        dateRange: 'filter[created_between]',
        system_status: 'filter[status]',
        status: 'filter[display_status]', // Processing, Exception, Rejected, Intake, Processed, Sending to TMS, Sent to TMS, Accepted by TMS
        page: 'page',
        sort: 'sort',
        items_per_page: 'perPage'
      }

      return getRequestFilters(this.getFilters(), filterKeyMap)
    },

    getStatusChip (item) {
      // different colors for different status types
      switch (item.latest_ocr_request_status.display_status.toLowerCase()) {
        case 'processing':
          return { color: 'blue' }
        case 'updated':
          return { color: 'blue', outlined: true, textColor: 'blue' }
        case 'rejected':
          return { color: '#FB7660' }
        case 'error':
          return { color: '#FB7660' }
        case 'warning':
          return { color: '#FB7660' }
        default:
          return { color: 'blue' }
      }
    },

    resetFilters () {
      this.$refs.orderFilters.clearFilters()
    },

    deleteItem (e) {
      this.$emit('deleteItem', e.id)
    },

    onPageIndexChange (pageIndex) {
      this.page = pageIndex
      this.setURLParams()
      this.getOrderData()
    }

  }
}
</script>

<style lang="scss" scoped>
    .table-root {
      .processed-status {
        &:before {
          content: '';
          display: inline-block;
          width: rem(10);
          height: rem(10);
          border-radius: 50%;
          background: #77C19A;
          margin-right: rem(5);
        }
      }
    }
    .table.loading::v-deep tbody {
      opacity: 0.5;
      transition: opacity 0.3s linear;
    }
    .table::v-deep tbody {
        tr:nth-of-type(even) {
            background-color: rgba(0, 0, 0, .05);
            background-color: #F5F6F7;
        }
    }
    .table::v-deep .v-data-table__wrapper {
        border: solid 1px #E6ECF1;
        border-radius: 5px;
    }
    .table::v-deep th {
        height: rem(40);
    }
    .table::v-deep th.sortable .v-icon {
        position: absolute;
    }
    .table::v-deep .v-data-table-header th {
        background-color: #F5F6F7 !important;
    }
    .table-tools::v-deep .v-toolbar__content {
      padding-left: 0;
      padding-right: 0;
      justify-content: space-between;
    }
    .table::v-deep .v-data-table__empty-wrapper {
      margin-top:rem(46);
    }
    .table-column-filter {
      max-width:rem(100);
      font-size: rem(12);
      margin-bottom: rem(8);
    }
    .table-column-filter::v-deep .v-icon {
      margin-top: -6px !important;
      font-weight: bold;
      transform: scale(1.2);
    }
    .table-column-filter::v-deep .v-text-field__details {
      padding: 0 10px;
    }
    .table-column-filter::v-deep .v-list-item__action {
      margin-right: 13px !important;
      transform: scale(0.7) !important;
    }

    .table-column-filter::v-deep .v-input__slot,
    .table-column-filter::v-deep .v-select__slot {
      min-height: rem(20) !important;
      height: rem(32);

    }
    .table-column-filter::v-deep .v-input__append-inner {
      margin-top: 0;
    }
    .column-selector-menu {
      background: black;
    }
    .column-selector-menu::v-deep .v-list-item__action {
      margin-right: 13px !important;
      transform: scale(0.7) !important;
    }
    .refresh-msg {
      width: 100%;
      p { margin: 0; }
    }
  .v-icon {
    vertical-align: baseline;
    color: #7BAFD4 !important;
  }
  .v-icon:hover {
    color: #326295 !important;
  }
</style>
