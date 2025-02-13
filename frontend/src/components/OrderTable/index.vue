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
          height="auto"
        >
          <v-btn
            v-show="newOrders"
            outlined
            dense
            small
            icon
            color="primary"
            class="ml-0"
            :loading="loading"
            @click="getOrderData"
          >
            <v-icon class="primary--text">
              mdi-refresh
            </v-icon>
          </v-btn>
          <Filters
            ref="orderFilters"
            :search="initFilters.search"
            :date-range="initFilters.dateRange"
            :status="initFilters.status"
            :system-status="initFilters.systemStatus"
            :company-id="initFilters.companyId"
            :update-type="initFilters.updateType"
            :display-hidden="initFilters.displayHidden"
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

      <template v-slot:[`item.request_id`]="{ item }">
        <router-link :to="`/inbox?selected=${item.request_id}${item.request_is_hidden !== null? '&displayHidden=true' : ''}`">
          {{ item.request_id.substring(0,8).toUpperCase() }}
        </router-link>
      </template>
      <template v-slot:[`item.company`]="{ item }">
        {{ item.company }}
      </template>
      <template v-slot:[`item.id`]="{ item }">
        <router-link :to="`/order/${item.id}`">
          {{ item.id }}
        </router-link>
        <v-tooltip top>
          <template v-slot:activator="{on}">
            <v-icon
              v-if="item.is_hidden"
              small
              color="secondary"
              class="ml-1"
              v-on="on"
            >
              mdi-eye-off-outline
            </v-icon>
          </template>
          <span>This order is hidden</span>
        </v-tooltip>
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        {{ formatDate(item.created_at) }}
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ formatDate(item.updated_at) }}
      </template>
      <template v-slot:[`item.bill_to_or_template`]="{ item }">
        {{
          item.tms_template
            ? item.tms_template.item_display_name
            : item.bill_to_address_name
        }}
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
        <RequestStatus
          class="caption black--text"
          :status="item.latest_ocr_request_status"
        />
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <OutlinedButtonGroup
          v-if="currentUser !== undefined"
          :main-action="{
            title: 'DETAILS',
            to: `/order/${item.id}`,
            hasPermission: hasPermission('orders-view')
          }"
          :options="[
            { title: 'Replicate Order', action: () => replicateOrder(item), hidden: !hasPermission('admin-review-edit') || item.is_locked },
            { title: item.is_hidden ? 'Unhide Order' : 'Hide Order', action: () => changeOrderDisplayStatus(item), hidden: true },
            { title: 'Show status history', action: () => item.openStatusHistoryDialog = true },
            { title: 'Delete Order', action: () => deleteOrder(item), hidden: !hasPermission('orders-remove') || item.is_locked },
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
        <StatusHistoryDialog
          :open="item.openStatusHistoryDialog"
          :order="item"
          @close="item.openStatusHistoryDialog = false"
        />
      </template>
      <template v-slot:no-data>
        <v-container>
          <v-row>
            <v-col
              cols="12"
              class="py-6 mt-4"
            >
              <h6>
                <strong>No Data</strong>
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
      v-model="newOrders"
      :timeout="-1"
    >
      <div class="refresh-msg d-flex align-center justify-space-between">
        <p>New orders available.</p>
        <v-btn
          text
          @click="getOrderData"
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
import RequestStatus from '@/components/RequestStatus'

import hasPermission from '@/mixins/permissions'
import statusUpdatesSubscribe from '@/mixins/status_updates_subscribe'
import { formatDate } from '@/utils/dates'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import { getOrders, delDeleteOrder, updateOrderDetail, replicateOrder } from '@/store/api_calls/orders'
import { getRequestFilters } from '@/utils/filters_handling'
import { statuses } from '@/enums/app_objects_types'
import events from '@/enums/events'

import { mapState, mapActions } from 'vuex'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'
import StatusHistoryDialog from '@/views/OrderDetails/StatusHistoryDialog'
import cloneDeep from 'lodash/cloneDeep'
import get from 'lodash/get'

export default {
  name: 'OrderTable',

  components: {
    Pagination,
    OutlinedButtonGroup,
    RequestStatus,
    Filters,
    StatusHistoryDialog
  },

  mixins: [hasPermission, statusUpdatesSubscribe],

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
        { text: 'Bill To', value: 'bill_to_address_name' },
        { text: 'Direction', value: 'shipment_direction', align: 'center' },
        { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
      ]
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
      payload: '',
      newOrderIds: [],
      // main filters array
      filters: [],
      dialog: false,
      loading: false,
      page: 1,
      requestID: this.requestId,
      options: {
        sortBy: []
      },
      minPause: 500, // 0.5 second minimum delay
      randomizeDelay: true,
      ordersPayload: null,
      // total number of returned orders
      total: 0,
      initFilters: {
        search: '',
        dateRange: [],
        displayHidden: false,
        status: [],
        systemStatus: [],
        companyId: [],
        updateType: '',
        page: 1
      },
      selected: [],
      selectedHeaders: [],
      orders: [],
      // sorting params
      sortDesc: true,
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
    },
    newOrders () {
      return this.newOrderIds.length > 0
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
          bill_to_address_name: 'order.bill_to_address',
          'latest_ocr_request_status.display_status': 'status'
        }
        let sortCol = this.sortColumnDefault

        // eslint-disable-next-line no-prototype-builtins
        if (sortColumnMap.hasOwnProperty(this.options.sortBy.join())) {
          sortCol = sortColumnMap[this.options.sortBy.join()]
        } else if (this.options.sortBy.join() !== '') {
          sortCol = this.options.sortBy.join()
        }

        this.sortColumn = sortCol
        // eslint-disable-next-line eqeqeq
        this.sortDesc = this.options.sortDesc.join() == 'true'
        this.setURLParams()
        this.getOrderData()
      },
      deep: true
    },

    async requestId () {
      this.leaveRequestStatusUpdatesChannel('-orders')
      await this.getOrderData()
      this.initializeStateUpdatesListeners()
    }
  },

  created () {
    this.selectedHeaders = Object.values(this.headers)

    if (this.waitForRequestId || !this.urlFilters) {
      return
    }
    // set get params if there are any

    const params = this.$route.query
    const sortValue = params.sort ? params.sort : `-${this.sortColumn}`

    this.page = params.page
    this.sortColumn = sortValue.replace('-', '')
    this.sortDesc = !sortValue.includes('-')
    this.options.sortBy = [this.sortColumn]
    this.options.sortDesc = [this.sortDesc]
    this.initFilters.search = params.search
    this.initFilters.dateRange = params.dateRange?.split(',')
    this.initFilters.displayHidden = !!params.displayHidden
    this.initFilters.status = params.status?.split(',')
    this.initFilters.systemStatus = params.system_status?.split(',')
    this.initFilters.companyId = params.company_id?.split(',')
      .map(item => parseInt(item))
      .filter(item => !isNaN(item))
    this.initFilters.updateType = params.updateType
    this.requestID = params.request_id
    this.initFilters.page = params.page
  },

  mounted () {
    this.initializeStateUpdatesListeners()
    if (this.waitForRequestId) {
      return
    }

    this.initialize()
    if (this.urlFilters) {
      window.onpopstate = this.onHistoryChange.bind(this)
    }
  },

  beforeDestroy () {
    this.leaveRequestStatusUpdatesChannel('-orders')
    if (this.waitForRequestId) {
      return
    }
    // remove popstate event handling
    if (this.urlFilters) {
      window.onpopstate = null
    }
  },

  methods: {
    formatDate,

    initialize () {
      // if there are init filters set from get params grab the active filters from the filters component before querying the DB
      if (Object.keys(this.initFilters).some(key => this.initFilters[key] && this.initFilters[key].length > 0)) {
        this.filters = [...this.$refs.orderFilters.getActiveFilters()]
      }
    },

    ...mapActions(utils.moduleName, [utilsActionTypes.setSnackbar, utilsActionTypes.setConfirmationDialog]),

    async deleteOrder (item) {
      this.loading = true
      await this.setConfirmationDialog({
        title: 'Are you sure you want to delete this order?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await delDeleteOrder(item.id)
          let message = ''

          if (!error) {
            this.loading = false
            message = 'Order deleted'
            this.resetFilters()
            this.$emit('order-deleted')
          } else {
            message = 'Error trying to delete the order'
          }
          await this.setSnackbar({ message })
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async replicateOrder (item) {
      this.loading = true
      await this.setConfirmationDialog({
        title: 'Replicate Order',
        text: 'How many additional orders need to be created?',
        hasInputValue: true,
        inputProps: {
          type: 'number',
          min: 1,
          max: 50,
          'hide-details': false,
        },
        validate: true,
        onConfirm: async (userInput) => {
          this.loading = true
          let counter = 0
          let message = ''
          const maxCounter = !!Number(userInput) && Number(userInput)
          if (maxCounter) {
            this.setSnackbar({ message: 'Processing the order(s), please wait...', timeout: -1 })
            for (let i = 0; i < maxCounter; i++) {
              const [error] = await replicateOrder(item.id)
              if (!error) counter++
            }
            message = `${counter} of ${maxCounter} order(s) replicated successfully`
            this.resetFilters()
            this.$emit(events.orderReplicated)
            this.loading = false
            await this.setSnackbar({ message })
          }
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async changeOrderDisplayStatus (item) {
      this.loading = true
      const isHidden = item.is_hidden
      const [error] = await updateOrderDetail({ id: item.id, changes: { is_hidden: !isHidden } })
      let message = ''
      if (error === undefined) {
        this.loading = false
        message = !isHidden ? 'The order is now hidden' : 'The order is now visible'
        this.getOrderData()
      } else {
        this.loading = false
        message = 'An error has ocurred'
      }

      await this.setSnackbar({ message })
    },

    async getOrderData () {
      const startTime = new Date().getTime()
      this.loading = true
      this.newOrderIds = []

      const [error, responseData] = await getOrders(this.getRequestFilters())

      if (error !== undefined) return

      const { data, links, meta } = responseData

      const now = new Date().getTime()

      const deltaTime = now - startTime
      const orders = data.map((order) => {
        order.openStatusHistoryDialog = false

        if (order.preceded_by_order_id) {
          order.latest_ocr_request_status.display_status += ' (update)'
        }

        return order
      })

      if (deltaTime < this.minPause) {
        this.pause(this.minPause - deltaTime).then(() => {
          this.orders = orders
          this.links = links
          this.meta = meta
          this.total = this.meta.total
          this.loading = false
        })
      } else {
        this.orders = orders
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
      // eslint-disable-next-line camelcase
      const { search, status, system_status, dateRange, updateType, page } = e.state

      const f = {
        search: search || '',
        dateRange: dateRange ? dateRange.split(',') : [],
        status: status ? status.split(',') : [],
        // eslint-disable-next-line camelcase
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
      const filters = [...this.getFilters()].filter(item => item.type !== 'items_per_page')
      const filterState = filters.reduce((o, element) => ({ ...o, [element.type]: Array.isArray(element.value) ? element.value.join(',') : element.value }), {})
      // const params = filters.map(element => `${element.type}=${element.value}`).join('&')
      this.$router.replace({ path: 'search', query: filterState }).catch(() => {})
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
        displayHidden: 'filter[hidden]',
        system_status: 'filter[status]',
        status: 'filter[display_status]', // Processing, Exception, Rejected, Intake, Processed, Sending to TMS, Sent to TMS, Accepted by TMS
        company_id: 'filter[company_id]',
        page: 'page',
        sort: 'sort',
        items_per_page: 'perPage'
      }

      return getRequestFilters(this.getFilters(), filterKeyMap)
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
    },

    initializeStateUpdatesListeners () {
      this.listenToRequestStatusUpdates(({ latestStatus } = {}) => {
        if (!this.requestId) {
          this.checkForNewOrders(latestStatus)
        }

        const index = this.orders.findIndex(item => item.id === latestStatus.order_id)
        if (index === -1) {
          return
        }

        this.$root.$emit(events.orderStatusUpdated, latestStatus)

        const order = cloneDeep(this.orders[index])
        order.latest_ocr_request_status = latestStatus

        let tmsShipmentId = get(latestStatus, 'status_metadata.tms_shipment_id', null)
        tmsShipmentId = get(latestStatus, 'status_metadata.shipment_id', tmsShipmentId)

        if (!order.tms_shipment_id && tmsShipmentId) {
          order.tms_shipment_id = tmsShipmentId
        }

        this.orders.splice(index, 1, order)
      }, '-orders')
    },

    checkForNewOrders (latestStatus) {
      const hasNewOrderStatus = [
        statuses.processOcrOutputFileComplete,
        statuses.processOcrOutputFileReview,
        statuses.processOcrOutputFileError,
      ].includes(latestStatus.status)

      if (!hasNewOrderStatus || this.newOrderIds.includes(latestStatus.order_id)) {
        return
      }

      this.newOrderIds.push(latestStatus.order_id)
    }

  }
}
</script>

<style lang="scss" scoped>
.table-root::v-deep {
  position: relative;
  table > thead > tr > th {
    position: relative;
    & > span {
      display: inline-block;
      width: 100%;
      padding-right: rem(18);
    }
    & > .v-icon.v-icon {
      position: absolute;
      top: 50%;
      right: 0;
      transform: translateY(-50%) rotate(0deg);
    }
    &.asc > .v-icon.v-icon {
      transform: translateY(-50%)rotate(180deg);
    }
    &.desc > .v-icon.v-icon {
      transform: translateY(-50%) rotate(0deg);
    }
  }
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
}
.table::v-deep .v-data-table__empty-wrapper {
  margin-top:rem(46);
}
.table-column-filter {
  max-width:rem(100);
  font-size: rem(12);
  margin-bottom: rem(8);
  margin-left: auto;
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
