<template>
  <div class="list">
    <h1 class="list__title">
      Orders
    </h1>

    <v-data-table
      :headers="headers"
      :items="list()"
      :items-per-page="list().length"
      :hide-default-footer="true"
    />

    <OrdersListFooter />
  </div>
</template>

<script>
import OrdersListFooter from '@/views/Orders/OrdersListFooter'
import { providerStateName } from '@/views/Orders/inner_types'

const tableHeaders = [
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

export default {
  name: 'OrdersList',

  inject: [providerStateName],

  components: {
    OrdersListFooter
  },

  data () {
    const { list } = this[providerStateName]

    return {
      headers: tableHeaders,
      list
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
