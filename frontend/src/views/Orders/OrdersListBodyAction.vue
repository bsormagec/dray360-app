<template>

  <!-- <v-btn
    v-if="hasPermission('orders-view')"
    color="primary"
    :disabled="item.order.id === undefined"
    @click="() => item.action(item.order.id)"
  >
    View
  </v-btn> -->

  <!-- :disabled="item.order.id === undefined" -->

  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        v-if="hasPermission('orders-view')"
        color="primary"
        dark
        v-bind="attrs"
        v-on="on"
      >
        View
      </v-btn>
    </template>
    <v-list>
      <v-list-item>
        <v-list-item-title
          @click="() => item.action(item.order.id)"
        >
          {{ "View Details" }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item>
        <v-list-item-title
          @click="downloadPDF(item.order.id)"
        >
          {{ "Download PDF" }}
        </v-list-item-title>
      </v-list-item>
      <v-list-item>
        <v-list-item-title
          @click="viewOrderHistory()"
        >
          {{ "View Order History" }}
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>

import hasPermissions from '@/mixins/permissions'
import { reqStatus } from '@/enums/req_status'
import { mapActions } from '@/utils/vuex_mappings'
import orders, { types } from '@/store/modules/orders'

export default {
  name: 'OrdersListBodyAction',
  mixins: [hasPermissions],
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data: () => ({
  }),
  // mounted () {
  //   console.log('item prop: ', this.item)
  // },
  methods: {
    ...mapActions(orders.moduleName, [types.getDownloadPDF]),

    async downloadPDF (orderId) {
      const status = await this[types.getDownloadPDF](orderId)

      if (status === reqStatus.success) {
        console.log('success')
        console.log('status: ', status)
      } else {
        console.log('error')
      }
    },

    viewOrderHistory () {
      console.log('view order history')
    }
  }
}
</script>
