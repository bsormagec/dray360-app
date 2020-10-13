<template>
  <OutlinedButtonGroup
    :main-action="{
      title: 'View',
      path: `/order/${item.order.id}`,
      hasPermission: hasPermission('orders-view')
    }"
    :options="[
      { title: 'View Details', action: () => item.action(item.order.id), hasPermission: hasPermission('orders-view') },
      { title: 'Download PDF', action: () => downloadPDF(item.order.id) },
      { title: 'View Order History', action: () => viewOrderHistory() }
    ]"
    :disabled="checkId(item.order.id)"
  />
</template>

<script>
import hasPermissions from '@/mixins/permissions'
import { reqStatus } from '@/enums/req_status'
import { mapActions } from 'vuex'
import orders, { types } from '@/store/modules/orders'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'

export default {
  name: 'OrdersListBodyAction',
  components: { OutlinedButtonGroup },
  mixins: [hasPermissions],
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    ...mapActions(orders.moduleName, [types.getDownloadPDFURL]),
    async downloadPDF (orderId) {
      const request = await this[types.getDownloadPDFURL](orderId)

      if (request.status === reqStatus.success) {
        const link = document.createElement('a')
        link.href = request.data.data
        link.download = `order-${orderId}.pdf`
        link.click()
        link.remove()
      } else {
        console.log('error')
      }
    },
    viewOrderHistory () {
      console.log('view order history')
    },
    checkId (orderId) {
      if (orderId) {
        return false
      }
      return true
    }
  }
}
</script>