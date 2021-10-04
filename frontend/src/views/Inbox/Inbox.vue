<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div
          v-show="displayStatus.requestList"
          :class="{
            'requests__section': true,
            'c-2': !isMobile,
            'col-12': isMobile,
          }"
        >
          <div class="inbox__title">
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
          v-show="displayStatus.orderDetail"
          :class="{
            'request__orders':true,
            'c-10': !isMobile,
            'col-12': isMobile,
          }"
        >
          <div
            v-if="!selectedRequest.request_id"
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
          <PtImageRequestDetails
            v-else-if="currentRequestIsPtImageUpload"
            :request="selectedRequest"
            @go-back="toggleMobileView"
          />
          <div
            v-else-if="selectedRequest.orders_count === 0 && selectedRequest.deleted_orders_count > 0"
            class="empty__order"
          >
            <img
              src="../../assets/images/emptyRequest.png"
              width="20%"
            >
            <span class="primary--text font-weight-light h6 mt-8">
              All orders in this request were deleted
            </span>
          </div>
          <div
            v-else-if="shouldShowEmptyProcessingRequest(selectedRequest)"
            class="empty__order"
          >
            <img
              src="../../assets/images/container.png"
              width="20%"
            >
            <span class="primary--text font-weight-light h6 mt-8">
              {{ emptyRequestText.text }}
            </span>
            <p
              v-if="emptyRequestText.errorMessage"
              class="primary--text font-weight-light h6 mt-8 text-center error-message"
            >
              {{ emptyRequestText.errorMessage }}
            </p>
          </div>
          <div
            v-else-if="selectedRequest.orders_count > 1 || !selectedRequest.first_order_id"
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
                Request # {{ selectedRequest.request_id.substring(0,8).toUpperCase() }} ({{ selectedRequest.orders_count }} orders)
              </h5>
              <RequestItemMenu
                :request="selectedRequest"
                @request-deleted="$root.$emit(events.orderDeleted)"
              />
            </div>
            <OrderTable
              :request-id="selectedRequest.request_id"
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
                { text: 'Actions', value: 'actions', sortable: false, align: 'center' },
              ]"
              @order-replicated="$root.$emit(events.orderReplicated)"
              @order-deleted="$root.$emit(events.orderReplicated)"
            />
          </div>
          <OrderDetails
            v-else-if="selectedRequest.orders_count === 1"
            :back-button="false"
            :refresh-lock="false"
            :order-id="selectedRequest.first_order_id"
            :details-only="displayStatus.requestList"
            @order-deleted="$root.$emit(events.orderDeleted)"
            @order-replicated="$root.$emit(events.orderReplicated)"
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
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton.vue'
import RequestsList from './RequestsList'
import OrderDetails from '@/views/OrderDetails/OrderDetails'
import UploadOrdersDialog from './UploadOrdersDialog'
import PtImageRequestDetails from './PtImageRequestDetails'

import { mapState, mapGetters } from 'vuex'
import requestsList from '@/store/modules/requests-list'
import auth from '@/store/modules/auth'

import permissions from '@/mixins/permissions'
import events from '@/enums/events'
import { statuses } from '@/enums/app_objects_types'

import { isInAdminReview, isPtImageUpload } from '@/utils/status_helpers'
import get from 'lodash/get'

export default {
  name: 'Inbox',

  components: {
    OrderTable,
    SidebarNavigationButton,
    OrderDetails,
    RequestsList,
    UploadOrdersDialog,
    RequestItemMenu,
    PtImageRequestDetails,
  },

  mixins: [permissions],

  data () {
    return {
      openUploadOrdersDialog: false,
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
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    }),
    ...mapState(requestsList.moduleName, {
      selectedRequestId: state => state.selectedRequestId
    }),
    ...mapGetters(requestsList.moduleName, ['selectedRequest']),

    events () {
      return events
    },

    emptyRequestText () {
      const requestStatus = get(this.selectedRequest, 'latest_ocr_request_status.status')

      switch (requestStatus) {
        case statuses.intakeRejected:
        case statuses.ocrTimedout:
          return {
            text: 'The request has been rejected',
            errorMessage: get(this.selectedRequest, 'latest_ocr_request_status.display_message'),
          }
        case statuses.intakeException:
        case statuses.ocrPostProcessingError:
        case statuses.processOcrOutputFileError:
          return {
            text: 'An error occurred while processing this request. Please, try uploading the document again',
            errorMessage: get(this.selectedRequest, 'latest_ocr_request_status.display_message'),
          }
        default:
          return { text: 'The request is being processed' }
      }
    },

    currentRequestIsPtImageUpload () {
      return isPtImageUpload(this.selectedRequest?.latest_ocr_request_status?.status)
    },

    isMobile () {
      return this.$vuetify.breakpoint.mobile
    },
  },

  watch: {
    isMobile: function (newVal, oldVal) {
      if (newVal) {
        this.displayStatus.requestList = true
        this.displayStatus.orderDetail = false
      } else {
        this.displayStatus.requestList = true
        this.displayStatus.orderDetail = true
      }
    },
  },

  beforeMount () {
    if (!this.isMobile) {
      return
    }
    this.displayStatus.orderDetail = false
  },

  methods: {
    shouldShowEmptyProcessingRequest (request) {
      return request.orders_count === 0 ||
        (
          isInAdminReview(request?.latest_ocr_request_status?.status) &&
          !this.hasPermission('admin-review-view')
        )
    },

    handleFilesUploaded (requestsList) {
      this.$root.$emit(events.requestUploaded, requestsList)
      this.openUploadOrdersDialog = false
    },

    requestChanged () {
      if (!this.firstLoad && this.isMobile) {
        this.displayStatus.orderDetail = true
        this.displayStatus.requestList = false
      }
      this.firstLoad = false
    },

    toggleMobileView () {
      this.displayStatus.orderDetail = false
      this.displayStatus.requestList = true
    }
  }
}
</script>

<style lang="scss" scoped>
.no-gutters > .col, .no-gutters > [class*=c-] {
    padding: 0;
}
.c-2 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 20%;
    flex: 0 0 20%;
    max-width: 20%;
    width: 100%;
  }
  .c-10 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 80%;
    flex: 0 0 80%;
    max-width: 80%;
    width: 100%;
  }
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

  & .error-message {
    width: 70%;
  }
}

.request__filters {
  display: flex;
  padding: rem(6) 0 rem(6);
  align-items: center;
}
</style>
