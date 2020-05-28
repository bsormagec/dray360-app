<template>
  <div
    class="list"
  >
    <OrdersListHeader
      :statuses="statuses"
      :headers="headers"
      :set-headers="setHeaders"
    />

    <ContentLoading
      :loaded="loaded"
    >
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
        value: 'id'
      },
      {
        text: 'Status',
        value: 'latest_ocr_request_status.status'
      },
      { text: 'Bill to', value: 'bill_to_address_raw_text' },
      { text: 'Date', value: 'created_at' },
      { text: 'Shipment Direction', value: 'shipment_direction' },
      { text: 'Shipment Designation', value: 'shipment_designation' },
      { text: 'Eq. Type', value: 'equipment_type' },
      { text: 'Actions', value: 'action' }
    ],
    statuses: [
      'Pending',
      'Processing',
      'In review',
      'Verified',
      'Sent to TMS',
      'Error'
    ]
  }),

  methods: {
    setHeaders (newHeaders) {
      this.headers = newHeaders
    },

    async requestPage (n) {
      await this[providerMethodsName].fetchOrdersList(n)
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
