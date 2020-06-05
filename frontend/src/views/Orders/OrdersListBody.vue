<template>
  <div class="listbody">
    <v-data-table
      :headers="headers"
      :items="list()"
      :items-per-page="list().length"
      :hide-default-footer="true"
      mobile-breakpoint="319"
    >
      <template v-slot:item.ocr_request.latest_ocr_request_status.status="{ item }">
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
  }
}
</script>

<style lang="scss" scoped>
.listbody {
  width: 100%;
}
</style>
