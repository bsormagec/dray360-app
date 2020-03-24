<template>
  <div class="list">
    <h1 class="list__title">
      Orders
    </h1>

    <OrdersListBody :headers="headers" />

    <OrdersListFooter
      :active-page="activePage"
      :set-active-page="setActivePage"
    />
  </div>
</template>

<script>
import OrdersListBody from '@/views/Orders/OrdersListBody'
import OrdersListFooter from '@/views/Orders/OrdersListFooter'
import { providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersList',

  inject: [providerMethodsName],

  components: {
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
      { text: 'Date', value: 'created_at' },
      { text: 'Shipment Direction', value: 'shipment_direction' },
      { text: 'Shipment Designation', value: 'shipment_designation' },
      { text: 'Eq. Type', value: 'equipment_type' }
    ]
  }),

  methods: {
    async setActivePage (n) {
      this.activePage = n
      await this[providerMethodsName].fetchOrdersList(n)
    }
  }
}
</script>

<style lang="scss" scoped>
.list {
  padding: 5.2rem 7.5rem;
  padding-bottom: 3rem;
  flex-grow: 1;
}

.list__title {
  margin-bottom: 1.5rem;
}
</style>
