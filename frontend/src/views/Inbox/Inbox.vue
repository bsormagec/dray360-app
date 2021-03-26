<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div
          v-if="displayStatus.requestList"
          :class="{
            'requests__section': true,
            'col-2': compressed,
            'col-3': !compressed,
            'col-12': isMobile,
          }"
        >
          <div class="inbox__title">
            <v-btn
              v-if="!isMobile"
              icon
              dense
              small
              dark
              class="requests__section_collapse"
              @click="toggleCompress"
            >
              <v-icon
                medium
                white
              >
                {{ !compressed ? 'mdi-unfold-less-vertical' : 'mdi-unfold-more-vertical' }}
              </v-icon>
            </v-btn>
            <SidebarNavigationButton />
            <div class="inbox__title_description">
              Requests Inbox
            </div>
            <div
              v-if="hasPermission('ocr-requests-create')"
              class="add__request"
            >
              <v-btn
                outlined
                dense
                small
                dark
                @click="openUploadOrdersDialog = true"
              >
                ADD
              </v-btn>
            </div>
          </div>

          <div class="requests__list">
            <RequestsList
              @change="requestChanged"
            />
          </div>
        </div>
        <div
          v-if="displayStatus.orderDetail"
          :class="{
            'request__orders':true,
            'col-9': !compressed,
            'col-10': compressed,
            'col-12': isMobile,
          }"
        >
          <div
            v-if="request.request_id === null"
            style="height:100%"
            class="d-flex align-center justify-center"
          >
            <v-btn
              outlined
              dense
              color="blue-light"
              @click="openUploadOrdersDialog = true"
            >
              Upload a new request
            </v-btn>
          </div>
          <div
            v-else-if="shouldShowEmptyProcessingRequest(request)"
            class="empty__order"
          >
            <img
              src="../../assets/images/container.png"
              width="20%"
            >
            <span class="primary--text font-weight-light h6 mt-8">
              {{ emptyRequestText }}
            </span>
          </div>
          <div
            v-else-if="request.orders_count > 1 || request.first_order_id === null"
            class="pa-5"
          >
            <div
              class="d-flex mb-5"
            >
              <v-btn
                v-if="isMobile"
                color="primary"
                outlined
                x-small
                width="32"
                height="32"
                class="px-0 mr-4"
                title="Go back to Orders List"
                @click="toggleMobileView"
              >
                <v-icon>
                  mdi-chevron-left
                </v-icon>
              </v-btn>
              <h5 class="mr-1">
                Request # {{ request.request_id.substring(0,8).toUpperCase() }} ({{ request.orders_count }} orders)
              </h5>
              <RequestItemMenu
                :request="request"
                @request-deleted="() => setReloadRequests(true)"
              />
            </div>
            <OrderTable
              :request-id="request.request_id"
              :url-filters="false"
              wait-for-request-id
              :headers="[
                { text: 'Date', value: 'created_at' },
                { text: 'Order ID', value: 'id' },
                { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
                { text: 'TMS ID', value: 'tms_shipment_id', align: 'center' },
                { text: 'Last Update', value: 'updated_at', align: 'center' },
                { text: 'Reference', value: 'reference_number', align: 'center' },
                { text: 'Container', value: 'unit_number' },
                { text: 'Bill To/Template', sortable: false, value: 'bill_to_or_template' },
                { text: 'Direction', value: 'shipment_direction', align: 'center' },
                { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
              ]"
              @order-deleted="() => setReloadRequests(true)"
            />
          </div>
          <OrderDetails
            v-else-if="request.orders_count === 1"
            :back-button="false"
            :refresh-lock="false"
            :order-id="request.first_order_id"
            :starting-size="compressed ? 40 : 50"
            @order-deleted="() => setReloadRequests(true)"
            @go-back="toggleMobileView"
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
import RequestItemMenu from '@/components/RequestItemMenu'
import OrderTable from '@/components/OrderTable'
import RequestsList from './RequestsList'
import OrderDetails from '@/views/OrderDetails/OrderDetails'
import UploadOrdersDialog from './UploadOrdersDialog'
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton'

import { mapState, mapActions } from 'vuex'
import permissions from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import auth from '@/store/modules/auth'
import orders, { types as ordersTypes } from '@/store/modules/orders'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'
import get from 'lodash/get'
import { statuses } from '@/enums/app_objects_types'
import { isInAdminReview } from '@/utils/status_helpers'

export default {
  name: 'Inbox',
  components: {
    OrderTable,
    OrderDetails,
    RequestsList,
    UploadOrdersDialog,
    RequestItemMenu,
    SidebarNavigationButton
  },
  mixins: [permissions, isMobile, isMedium],
  data () {
    return {
      compressed: true,
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
      },
      firstLoad: true,
      displayStatus: {
        requestList: true,
        orderDetail: true
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
    emptyRequestText () {
      const requestStatus = get(this.request, 'latest_ocr_request_status.status')

      switch (requestStatus) {
        case statuses.intakeRejected:
        case statuses.ocrPostProcessingError:
        case statuses.ocrTimedout:
          return 'The request has been rejected'
        case statuses.intakeException:
        case statuses.processOcrOutputFileError:
          return 'There was an error processing the request'
        default:
          return 'The request is being processed'
      }
    }
  },
  watch: {
    isMedium: function (newVal, oldVal) {
      if (!newVal) {
        this.setSidebar({ show: true })
      }
    },
    isMobile: function (newVal, oldVal) {
      if (newVal) {
        this.setSidebar({ show: false })
        this.displayStatus.requestList = true
        this.displayStatus.orderDetail = false
        this.compressed = false
      } else {
        this.setSidebar({ show: true })
        this.displayStatus.requestList = true
        this.displayStatus.orderDetail = true
        this.compressed = true
      }
    }
  },
  beforeMount () {
    if (!this.isMobile) {
      return this.setSidebar({ show: true })
    }
    this.displayStatus.orderDetail = false
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
    isInAdminReview,
    shouldShowEmptyProcessingRequest (request) {
      return request.orders_count === 0 ||
        (
          isInAdminReview(request?.latest_ocr_request_status?.status) &&
          !this.hasPermission('admin-review-view')
        )
    },
    handleFilesUploaded () {
      this.setReloadRequests(true)
      this.openUploadOrdersDialog = false
    },
    requestChanged (request) {
      this.request = request
      if (!this.firstLoad && this.isMobile) {
        this.displayStatus.orderDetail = true
        this.displayStatus.requestList = false
      }
      this.firstLoad = false
    },
    toggleMobileView () {
      this.firstLoad = true
      this.displayStatus.orderDetail = false
      this.displayStatus.requestList = true
    },
    toggleCompress () {
      this.compressed = !this.compressed
    }
  }
}
</script>
<style lang="scss" scoped>
.requests__section::v-deep {
  border-right: rem(1) solid rgba(var(--v-slate-gray-base-rgb), 15%);
  height: 100vh;

  .add__request{
    align-self: center;
    margin-left: auto;
    margin-right: rem(16);
  }

  .inbox__title{
    display: flex;
    align-items: center;
    background-color:var(--v-primary-base);
    height:rem(40);

    .inbox__title_description {
      color: white;
      font-size: rem(14);
      font-weight: 500;
      letter-spacing: rem(1.5);
      line-height: normal;
      padding: 0 rem(16);
      text-align: center;
      text-transform: uppercase;
    }
  }

  .requests__list {
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

.empty__order{
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.request__filters {
  display: flex;
  padding: rem(6) 0 rem(6);
  align-items: center;
}
</style>
