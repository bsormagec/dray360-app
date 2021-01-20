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
            'col-2': compressed && tab === 0,
            'col-3': !compressed && tab === 0,
            'col-12': tab === 1,
          }"
        >
          <v-tabs
            v-if="!compressed && !isMedium || tab === 1"
            :value="tab"
            background-color="primary"
            height="2.5rem"
            :show-arrows="false"
            :mobile-breakpoint="1405"
            dark
            @change="tabsChanged"
          >
            <v-btn
              v-if="tab===0"
              icon
              dense
              small
              class="requests__section_collapse"
              @click="togglePanels"
            >
              <v-icon
                medium
                white
              >
                mdi-unfold-less-vertical
              </v-icon>
            </v-btn>
            <v-btn
              v-else-if="isMedium"
              icon
              dense
              small
              class="requests__section_collapse"
            >
              <v-icon
                medium
                white
              >
                hamburger-menu
              </v-icon>
            </v-btn>
            <v-tab
              v-for="item in tabs"
              :key="item"
            >
              {{ item.toUpperCase() }}
            </v-tab>
            <div class="add__request">
              <v-btn
                outlined
                dense
                small
                @click="openUploadOrdersDialog = true"
              >
                ADD
              </v-btn>
            </div>
          </v-tabs>
          <div
            v-else
            class="menu__compressed"
          >
            <v-btn
              icon
              dense
              small
              class="requests__section_collapse"
              white
              @click="togglePanels"
            >
              <v-icon
                medium
                class="text-white"
              >
                mdi-unfold-more-vertical
              </v-icon>
            </v-btn>
            <v-menu
              absolute
              right
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  text
                  class="align-self-center mr-4"
                  v-bind="attrs"
                  white
                  v-on="on"
                >
                  {{ tabs[tab] }}
                  <v-icon right>
                    mdi-menu-down
                  </v-icon>
                </v-btn>
              </template>

              <v-list class="grey lighten-3">
                <v-list-item-group
                  v-model="tab"
                >
                  <v-list-item
                    v-for="item in tabs"
                    :key="item"
                    :disabled="item === 'orders' ? false : true"
                    @click="item === 'orders' ? tab = 1 : tab = 0"
                  >
                    {{ item.toUpperCase() }}
                  </v-list-item>

                  <div class="ml-3">
                    <v-btn
                      outlined
                      dense
                      small
                      @click="openUploadOrdersDialog = true"
                    >
                      ADD
                    </v-btn>
                  </div>
                </v-list-item-group>
              </v-list>
            </v-menu>
          </div>

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
                { text: 'Template', value: 'tms_template.item_display_name' },
                { text: 'Direction', value: 'shipment_direction', align: 'center' },
                { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
              ]"
              :extra-url-params="[{type: 'tab', value: 1}]"
            />
          </div>
        </div>
        <div
          v-if="tab === 0"
          :class="{
            'request__orders':true,
            'col-9': !compressed,
            'col-10': compressed,
          }"
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
              :headers="requestOrdersTableHeaders"
              @order-deleted="() => setReloadRequests(true)"
            />
          </div>
          <OrderDetails
            v-else-if="request.orders_count === 1"
            :back-button="false"
            :order-id="request.first_order_id"
            :starting-size="compressed ? 30 : 50"
            @order-deleted="() => setReloadRequests(true)"
          />
        </div>
      </div>
      <UploadOrdersDialog
        :open="openUploadOrdersDialog"
        @close="openUploadOrdersDialog = false"
        @uploaded="handleFilesUploaded"
      />
    </v-container>
  </div>
</template>
<script>

import OrderTable from '@/components/OrderTable'
import RequestsList from './RequestsList'
import OrderDetails from '@/views/OrderDetails/OrderDetails'
import UploadOrdersDialog from './UploadOrdersDialog'

import { mapActions, mapState } from 'vuex'
import permissions from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import auth from '@/store/modules/auth'
import { deleteRequest as delRequest } from '@/store/api_calls/requests'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'

import get from 'lodash/get'

export default {
  name: 'RequestsOrdersCombined',
  components: {
    OrderTable,
    OrderDetails,
    RequestsList,
    UploadOrdersDialog
  },
  mixins: [permissions, isMobile, isMedium],
  data () {
    return {
      tab: 0,
      tabs: ['requests', 'orders'],
      compressed: false,
      selectedItem: 0,
      openUploadOrdersDialog: false,
      request: {
        first_order_id: null,
        request_id: null,
        orders_count: 0,
        tms_template_name: null
      },
      filters: {
        search: '',
        dateRange: [],
        status: [],
        updateType: ''
      }
    }
  },
  computed: {
    ...mapState(utils.moduleName, {
      showingSidebar: state => state.sidebar.show
    }),
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    }),
    ordersTabFirst () {
      return get(this.currentUser, 'configuration.show_orders_tab_first', false)
    },
    requestOrdersTableHeaders () {
      return [
        { text: 'Date', sortable: false, value: 'created_at' },
        { text: 'Order ID', sortable: false, value: 'id' },
        { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
        { text: 'TMS ID', sortable: false, value: 'tms_shipment_id', align: 'center' },
        { text: 'Container', sortable: false, value: 'unit_number' },
        {
          text: this.request.tms_template_name === null ? 'Bill To' : 'Template',
          value: this.request.tms_template_name === null ? 'bill_to_address.location_name' : 'tms_template.item_display_name'
        },
        { text: 'Direction', value: 'shipment_direction', align: 'center' },
        { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
      ]
    }
  },
  watch: {
    isMedium: function (newVal, oldVal) {
      if (!newVal) {
        this.setSidebar({ show: true })
      }
    }
  },
  created () {
    const paramsTab = parseInt(this.$route.query.tab)

    this.tab = !isNaN(paramsTab) ? paramsTab : (this.ordersTabFirst ? 1 : 0)
  },
  beforeMount () {
    if (!this.isMobile) {
      return this.setSidebar({ show: true })
    }
    return this.setSidebar({ show: false })
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
      this.openUploadOrdersDialog = false
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
    },
    togglePanels () {
      this.compressed = !this.compressed
    }
  }
}
</script>
<style lang="scss" scoped>
.requests__section::v-deep {
  border-right: rem(1) solid rgba(var(--v-slate-gray-base-rgb), 15%);
  height: 100vh;
  .v-item-group .v-slide-group__next, .v-item-group .v-slide-group__prev{
      display: none !important;
  }
  .add__request{
      align-self: center;
      margin-left: auto;
      margin-right: rem(16);
    }

  .menu__compressed{
    display: flex;
    justify-content: space-between;
    background-color:var(--v-primary-base);
    height:rem(40);
    .requests__section_collapse, button{
      color: white;
      font-size: rem(14);
      font-weight: 500;
      font-style: normal;
      line-height: rem(16);
      letter-spacing: rem(0.75);
    }
    .add__request{
      margin-left: rem(15);
    }

  }
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
