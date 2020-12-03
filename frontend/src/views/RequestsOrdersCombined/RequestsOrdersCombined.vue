<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div
          :class="{
            'requests__section': true,
            'col-3': tab === 0,
            'col-12': tab === 1,
          }"
        >
          <v-tabs
            :value="tab"
            background-color="primary"
            height="2.5rem"
            :show-arrows="false"
            dark
            @change="tabsChanged"
          >
            <v-btn
              v-show="false"
              outlined
              dense
              small
              class="requests__section_collapse"
            >
              <v-icon medium>
                mdi-arrow-collapse-horizontal
              </v-icon>
            </v-btn>
            <v-tab
              v-for="item in tabs"
              :key="item"
            >
              {{ item.toUpperCase() }}
            </v-tab>
            <AddRequestButton @change="handleFilesUploaded" />
          </v-tabs>
          <div
            v-if="tab === 0"
            class="requests__list"
          >
            <RequestsList
              :extra-url-params="[{type: 'tab', value: 0}]"
              @change="requestChanged"
            />
          </div>
          <div
            v-else-if="tab === 1"
            class="orders__list"
          >
            <OrderTable
              :headers="[
                { text: 'Date', sortable: false, value: 'created_at' },
                { text: 'Order ID', sortable: false, value: 'id' },
                { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
                { text: 'TMS ID', sortable: false, value: 'tms_shipment_id', align: 'center' },
                { text: 'Last Update', sortable: false, value: 'updated_at', align: 'center' },
                { text: 'Container', sortable: false, value: 'unit_number' },
                { text: 'Bill To', value: 'bill_to_address.location_name' },
                { text: 'Direction', value: 'shipment_direction', align: 'center' },
                { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
              ]"
              :extra-url-params="[{type: 'tab', value: 1}]"
            />
          </div>
        </div>
        <div
          v-if="tab === 0"
          class="col-9 request__orders"
        >
          <div
            v-if="request.orders_count > 1 || request.first_order_id === null"
            class="pa-5"
          >
            <header class="mb-5">
              <div
                v-if="request.request_id !== null"
                class="d-flex"
              >
                <h5>
                  Request # {{ request.request_id.substring(0,8).toUpperCase() }} ({{ request.orders_count }} orders)
                </h5>
                <v-menu
                  v-if="hasPermission('ocr-requests-remove')"
                  offset-y
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      class="ml-4"
                      width="32"
                      height="32"
                      x-small
                      outlined
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-chevron-down</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="deleteRequest">
                      <v-list-item-title>Delete Request</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
              <h5 v-else>
                No request selected
              </h5>
            </header>
            <OrderTable
              v-if="request.request_id !== null"
              :request-id="request.request_id"
              :url-filters="false"
              wait-for-request-id
              :headers="[
                { text: 'Date', sortable: false, value: 'created_at' },
                { text: 'Order ID', sortable: false, value: 'id' },
                { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
                { text: 'TMS ID', sortable: false, value: 'tms_shipment_id', align: 'center' },
                { text: 'Container', sortable: false, value: 'unit_number' },
                { text: 'Bill To', value: 'bill_to_address.location_name' },
                { text: 'Direction', value: 'shipment_direction', align: 'center' },
                { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
              ]"
            />
          </div>
          <OrderDetails
            v-else-if="request.orders_count === 1"
            :back-button="false"
            :order-id="request.first_order_id"
          />
        </div>
      </div>
    </v-container>
  </div>
</template>
<script>

import OrderTable from '@/components/OrderTable'
import RequestsList from './RequestsList'
import AddRequestButton from './AddRequestButton'
import OrderDetails from '@/views/OrderDetails/OrderDetails'

import { mapActions } from 'vuex'
import permissions from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import { deleteRequest as delRequest } from '@/store/api_calls/requests'

export default {
  name: 'RequestsOrdersCombined',
  components: {
    OrderTable,
    OrderDetails,
    RequestsList,
    AddRequestButton
  },
  mixins: [permissions],
  data () {
    return {
      tab: 0,
      tabs: ['requests', 'orders'],
      request: {
        first_order_id: null,
        request_id: null,
        orders_count: 0
      },
      filters: {
        search: '',
        dateRange: [],
        status: [],
        updateType: ''
      }
    }
  },
  created () {
    this.tab = parseInt(this.$route.query.tab) || 0
  },
  beforeMount () {
    this.setSidebar({ show: true })
  },
  methods: {
    ...mapActions(utils.moduleName, {
      setSidebar: type.setSidebar,
      setSnackbar: type.setSnackbar,
      setConfirmDialog: type.setConfirmationDialog
    }),
    ...mapActions(orders.moduleName, {
      setReloadRequests: ordersTypes.setReloadRequests
    }),
    handleFilesUploaded () {
      this.setReloadRequests(true)
    },
    requestChanged (request) {
      this.request = request
    },
    tabsChanged (value) {
      this.$router.replace({ path: 'dashboard', query: { tab: value } }).catch(() => {})
      this.tab = value
    },
    async deleteRequest () {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to delete this request?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await delRequest(this.request.request_id)

          if (!error) {
            await this.setSnackbar({
              show: true,
              showSpinner: false,
              message: 'Request deleted'
            })
            location.reload()
            this.loading = false
          } else {
            await this.setSnackbar({
              show: true,
              showSpinner: false,
              message: 'Error trying to delete the request'
            })
          }
        },
        onCancel: () => {
          this.loading = false
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.requests__section {
  border-right: 1px solid rgba(map-get($colors, slate-gray), 15%);
  height: 100vh;
  .orders__list {
    overflow-y: auto;
    padding: rem(14) rem(24) 0 rem(24);
  }
  .requests__list, .orders__list {
    height: calc(100% - 2.5rem);
  }
  transition: max-width 0.5s;
  .requests__section_collapse {
    align-self: center;
    margin: 0 rem(8);
    height: rem(24);
    width: rem(24);
    min-width: rem(24);
  }
}
.request__orders {
  max-height: 100vh;
  overflow-y: auto;
  transition: width 0.5s;
}

.request__filters {
  display: flex;
  padding: rem(6) 0 rem(6);
  align-items: center;
  // min-height: rem(10);
}

</style>
