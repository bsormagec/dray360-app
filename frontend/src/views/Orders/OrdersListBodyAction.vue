<template>
  <OutlinedButtonGroup
    :main-action="{
      title: 'View',
      path: `/order/${item.order.id}`,
      action: false,
      hasPermission: hasPermission('orders-view')
    }"
    :options="[
      { title: 'Download Source File', action: () => downloadSourceFile(item.order.id) },
      { title: 'Reprocess Request', action: () => reprocessRequest(item), hasPermission: hasPermission('ocr-request-statuses-create') },
      { title: 'View Details', action: () => item.action(item.order.id), hasPermission: hasPermission('orders-view') },
      { title: 'View Order History', action: () => viewOrderHistory() },
      { title: 'Delete Order', action: () => deleteOrder(item), hasPermission: hasPermission('orders-remove') }
    ]"
    :disabled="checkId(item.order.id)"
    :loading="loading"
  />
</template>

<script>
import hasPermissions from '@/mixins/permissions'
import { mapActions } from 'vuex'
import { getSourceFileDownloadURL } from '@/store/api_calls/orders'
import utils, { type as utilTypes } from '@/store/modules/utils'
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
  data () {
    return {
      loading: false
    }
  },
  methods: {
    ...mapActions(utils.moduleName, {
      setSnackbar: utilTypes.setSnackbar,
      setConfirmDialog: utilTypes.setConfirmationDialog
    }),
    async downloadSourceFile (orderId) {
      this.loading = true
      const [error, data] = await getSourceFileDownloadURL(orderId)

      if (error === undefined) {
        const link = document.createElement('a')
        link.href = data.data
        link.click()
        link.remove()
      } else {
        console.log('error')
      }
      this.loading = false
    },
    viewOrderHistory () {
      console.log('view order history')
    },
    async reprocessRequest ({ request_id }) {
      this.setConfirmDialog({
        title: 'Are you sure you want to reprocess the request associated to this order?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await reprocessOcrRequest(request_id)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ show: true, message: 'There was an error trying to send the message to reprocess' })
            return
          }

          this.setSnackbar({ show: true, message: 'Request sent for reprocessing' })
          this.loading = false
        }
      })
    },
    checkId (orderId) {
      if (orderId) {
        return false
      }
      return true
    },
    async deleteOrder (item) {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to delete this order?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await delDeleteOrder(item.order.id)

          if (!error) {
            await this.setSnackbar({
              show: true,
              showSpinner: false,
              message: 'Order deleted'
            })
            location.reload()
            this.loading = false
          } else {
            await this.setSnackbar({
              show: true,
              showSpinner: false,
              message: 'Error trying to delete the order'
            })
          }
        }
      })
    }
  }
}
</script>
