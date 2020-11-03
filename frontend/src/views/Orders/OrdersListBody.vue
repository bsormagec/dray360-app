<template>
  <div class="listbody">
    <v-data-table
      class="order-table"
      :headers="headers"
      :items="list()"
      item-key="key"
      :items-per-page="list().length"
      :hide-default-footer="true"
      mobile-breakpoint="319"
    >
      <template v-slot:item.latest_ocr_request_status.display_status="{ item }">
        <OrdersListBodyStatus :item="item" />
      </template>

      <template v-slot:item.action="{ item }">
        <OrdersListBodyAction :item="item" />
      </template>
    </v-data-table>
  </div>
</template>

<script>
import { providerStateName } from '@/views/Orders/inner_types'
import OrdersListBodyStatus from '@/views/Orders/OrdersListBodyStatus'
import OrdersListBodyAction from '@/views/Orders/OrdersListBodyAction'

export default {
  name: 'OrdersListBody',

  inject: [providerStateName],

  components: {
    OrdersListBodyStatus,
    OrdersListBodyAction
  },

  props: {
    headers: {
      type: Array,
      required: true
    }
  },

  data () {
    const { list } = this[providerStateName]

    return {
      list
    }
  },
  created () {

  }

}
</script>

<style lang="scss" scoped>
.listbody {
  width: 100%;
}
.order-table::v-deep td {
  font-size: rem(12);
}
</style>
