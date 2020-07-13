<template>
  <div class="list">
    <OrdersListHeader
      :statuses="statuses"
      :headers="headers"
      :set-headers="setHeaders"
    />

    <ContentLoading :loaded="loaded">
      <OrdersListBody :headers="headers" />
    </ContentLoading>

    <OrdersListFooter
      v-if="loaded"
      :active-page="activePage"
      :request-page="requestPage"
    />
  </div>
</template>

<script>
import OrdersListHeader from '@/views/Orders/OrdersListHeader'
import OrdersListBody from '@/views/Orders/OrdersListBody'
import OrdersListFooter from '@/views/Orders/OrdersListFooter'
import ContentLoading from '@/components/ContentLoading'
import { providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersList',

  inject: [providerMethodsName],

  components: {
    OrdersListHeader,
    OrdersListBody,
    OrdersListFooter,
    ContentLoading
  },

  props: {
    activePage: {
      type: Number,
      required: true
    },
    loaded: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    headers: [
      {
        text: 'Id',
        sortable: false,
        value: 'display_id',
        width: '6rem'
      },
      {
        text: 'Status',
        value: 'latest_ocr_request_status.display_status',
        width: '12rem'
      },
      { text: 'Bill to', value: 'order.bill_to_address_raw_text', width: '30rem' },
      { text: 'Date', value: 'created_at', width: '6rem' },
      { text: 'Shipment Direction', value: 'order.shipment_direction', width: '8.5rem' },
      { text: 'Shipment Designation', value: 'order.shipment_designation', width: '8.5rem' },
      { text: 'Eq. Type', value: 'order.equipment_type', width: '8.5rem' },
      { text: 'Actions', value: 'action', width: '8.5rem' }
    ],
    statuses: [
      { text: 'intake-accepted', value: 'intake-accepted' },
      { text: 'intake-exception', value: 'intake-exception' },
      { text: 'intake-rejected', value: 'intake-rejected' },
      { text: 'intake-started', value: 'intake-started' },
      { text: 'ocr-completed', value: 'ocr-completed' },
      { text: 'ocr-post-processing-complete', value: 'ocr-post-processing-complete' },
      { text: 'ocr-post-processing-error', value: 'ocr-post-processing-error' },
      { text: 'ocr-waiting', value: 'ocr-waiting' },
      { text: 'process-ocr-output-file-complete', value: 'process-ocr-output-file-complete' },
      { text: 'process-ocr-output-file-error', value: 'process-ocr-output-file-error' },
      { text: 'upload-requested', value: 'upload-requested' },
      { text: 'sending-to-wint', value: 'sending-to-wint' },
      { text: 'failure-sending-to-wint', value: 'failure-sending-to-wint' },
      { text: 'success-sending-to-wint', value: 'success-sending-to-wint' }
    ]
  }),

  /*
  [
    'intake-accepted' => 'Processing',
    'intake-exception' => 'Exception',
    'intake-rejected' => 'Rejected',
    'intake-started' => 'Intake',
    'ocr-completed' => 'Processing',
    'ocr-post-processing-complete' => 'Verified',
    'ocr-post-processing-error' => 'Rejected',
    'ocr-waiting' => 'Processing',
    'process-ocr-output-file-complete' => 'Processing',
    'process-ocr-output-file-error' => 'Rejected',
    'upload-requested' => 'Intake'
  ]
  */

  methods: {
    setHeaders (newHeaders) {
      this.headers = newHeaders
    },

    async requestPage (n) {
      await this[providerMethodsName].fetchOrdersList({ page: n })
    }
  }
}
</script>

<style lang="scss" scoped>
.list {
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  flex-grow: unset;
  width: 100%;

  @media screen and (min-width: map-get($breakpoints, med)) {
    padding: 5.2rem 3.2rem;
    padding-left: map-get($sizes, sidebar-desktop-width) + 3.2rem;
  }
}
</style>
