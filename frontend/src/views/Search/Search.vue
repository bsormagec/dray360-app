<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div class="col-12 orders__list">
          <OrderTable
            :headers="[
              { text: 'Date', value: 'created_at' },
              { text: 'Order ID', value: 'id' },
              { text: 'Request ID', value: 'request_id' },
              { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
              { text: 'TMS ID', value: 'tms_shipment_id', align: 'center' },
              { text: 'Last Update', value: 'updated_at', align: 'center' },
              { text: 'Reference', value: 'reference_number', align: 'center' },
              { text: 'Container', value: 'unit_number' },
              { text: 'Bill To/Template', sortable: false, value: 'bill_to_or_template' },
              { text: 'Direction', value: 'shipment_direction', align: 'center' },
              { text: 'Actions', value: 'actions', sortable: false, align: 'center' }
            ]"
          />
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
}
</script>
<style lang="scss" scoped>
.orders__list {
    height: calc(100vh - 40px);
    overflow-y: auto;
    padding: rem(14) rem(28) 0 rem(28);
  }
</style>
