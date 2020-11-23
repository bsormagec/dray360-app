<template>
  <div class="list">
    <OrdersListHeader
      :statuses="statuses"
      :headers="headers"
      :set-headers="setHeaders"
      :selected-items="selectedItems"
      @refresh="refresh"
    />

    <ContentLoading :loaded="loaded">
      <OrdersListBody
        :headers="headers"
      />
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
    },
    selectedItems: {
      type: Array,
      required: true
    }
  },

  data: () => ({
    headers: [
      { text: 'Date', value: 'created_at', width: '60px' },
      { text: 'Shipment Direction', value: 'order.shipment_direction', width: '60px' },
      {
        text: 'Status',
        value: 'latest_ocr_request_status.display_status',
        width: '60px'
      },
      { text: 'Bill to', value: 'order.bill_to_address.location_name', width: '85px' },
      { text: 'Reference Number', value: 'order.reference_number', width: '60px' },
      { text: 'Container Number', value: 'order.unit_number', width: '60px' },
      { text: 'TMS ID', value: 'order.tms_shipment_id', width: '60px' },
      {
        text: 'Id',
        sortable: false,
        value: 'display_id',
        width: '40px'
      },
      { text: 'Actions', value: 'action', width: '85px' }

    ],
    statuses: [
      { text: 'intake-accepted', value: 'intake-accepted' },
      { text: 'intake-accepted-datafile', value: 'intake-accepted-datafile' },
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
      { text: 'success-sending-to-wint', value: 'success-sending-to-wint' },
      { text: 'failure-sending-to-wint', value: 'failure-sending-to-wint' },
      { text: 'shipment-created-by-wint', value: 'shipment-created-by-wint' },
      { text: 'shipment-not-created-by-wint', value: 'shipment-not-created-by-wint' },

      { text: 'updating-to-wint', value: 'updating-to-wint' },
      { text: 'success-updating-to-wint', value: 'success-updating-to-wint' },
      { text: 'failure-updating-to-wint', value: 'failure-updating-to-wint' },
      { text: 'shipment-updated-by-wint', value: 'shipment-updated-by-wint' },
      { text: 'shipment-not-updated-by-wint', value: 'shipment-not-updated-by-wint' },

      { text: 'updates-prior-order', value: 'updates-prior-order' },
      { text: 'updated-by-subsequent-order', value: 'updated-by-subsequent-order' },

      { text: 'success-imageuploding-to-blackfl', value: 'success-imageuploding-to-blackfl' },
      { text: 'failure-imageuploding-to-blackfl', value: 'failure-imageuploding-to-blackfl' },
      { text: 'untried-imageuploding-to-blackfl', value: 'untried-imageuploding-to-blackfl' }
    ]
  }),

  methods: {
    setHeaders (newHeaders) {
      this.headers = newHeaders
    },

    async requestPage (n) {
      await this[providerMethodsName].fetchOrdersList({ page: n })
    },
    async refresh () {
      this.$emit('refresh', true)
      await this[providerMethodsName].fetchOrdersList()
    }
  }
}
</script>

<style lang="scss" scoped>
.list {
  padding: rem(20) rem(10);
  display: flex;
  flex-direction: column;
  flex-grow: unset;
  width: 100%;

  // @include media("med") {
  //   padding: rem(52) rem(32);
  //   padding-left: rem(map-get($sizes, sidebar-desktop-width) + 32);
  // }
}
</style>
