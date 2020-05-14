<template>
  <div
    class="list"
  >
    <OrdersListHeader
      :headers="headers"
      :set-headers="setHeaders"
    />

    <OrdersListBody :headers="headers" />

    <OrdersListFooter
      :active-page="activePage"
      :set-active-page="setActivePage"
    />
  </div>
</template>

<script>
import OrdersListHeader from '@/views/Orders/OrdersListHeader'
import OrdersListBody from '@/views/Orders/OrdersListBody'
import OrdersListFooter from '@/views/Orders/OrdersListFooter'
import { providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersList',

  inject: [providerMethodsName],

  components: {
    OrdersListHeader,
    OrdersListBody,
    OrdersListFooter
  },

  data: () => ({
    activePage: 1,
    headers: [
      {
        text: 'Id',
        sortable: false,
        value: 'id'
      },
      {
        text: 'Request Status',
        value: 'latest_ocr_request_status.status'
      },
      { text: 'Date', value: 'created_at' },
      { text: 'Shipment Direction', value: 'shipment_direction' },
      { text: 'Shipment Designation', value: 'shipment_designation' },
      { text: 'Eq. Type', value: 'equipment_type' },
      { text: 'Actions', value: 'action' }
    ]
  }),

  methods: {
    setHeaders (newHeaders) {
      this.headers = newHeaders
    },

    async setActivePage (n) {
      this.activePage = n
      await this[providerMethodsName].fetchOrdersList(n)
    }
  }
}
</script>

<style lang="scss" scoped>
.list {
  padding: 2rem 1rem;
  flex-grow: 1;
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
