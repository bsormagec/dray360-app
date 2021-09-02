<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div class="col-12 orders__list">
          <OrderTable :headers="headersToBeShown" />
        </div>
      </div>
    </v-container>
  </div>
</template>

<script>
import OrderTable from '@/components/OrderTable'
import permissions from '@/mixins/permissions'

export default {
  name: 'RequestsOrdersCombined',

  components: {
    OrderTable,
  },

  mixins: [permissions],

  computed: {
    headersToBeShown () {
      return [
        { text: 'Date', value: 'created_at', hasPermission: true },
        { text: 'Order ID', value: 'id', hasPermission: true },
        { text: 'Request ID', value: 'request_id', hasPermission: true },
        { text: 'Company', value: 'company', align: 'center', hasPermission: this.canViewOtherCompanies() },
        { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center', hasPermission: true },
        { text: 'TMS ID', value: 'tms_shipment_id', align: 'center', hasPermission: true },
        { text: 'Last Update', value: 'updated_at', align: 'center', hasPermission: true },
        { text: 'Reference', value: 'reference_number', align: 'center', hasPermission: true },
        { text: 'Container', value: 'unit_number', hasPermission: true },
        { text: 'Bill To/Template', sortable: false, value: 'bill_to_or_template', hasPermission: true },
        { text: 'Direction', value: 'shipment_direction', align: 'center', hasPermission: true },
        { text: 'Actions', value: 'actions', sortable: false, align: 'center', hasPermission: true }
      ].filter(item => item.hasPermission)
    }
  }
}
</script>

<style lang="scss" scoped>
.orders__list {
  height: calc(100vh - 40px);
  overflow-y: auto;
  padding: rem(14) rem(28) 0 rem(28);
}
</style>
