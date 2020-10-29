<template>
  <div class="wrapper">
    <v-container>
      <v-row py-4>
        <v-col col-12>
          <h2 class="text-center">
            Pattern Library
          </h2>
        </v-col>
      </v-row>
      <div class="row">
        <div class="col-12">
          <header class="mb-5">
            <h4>Order Table</h4>
          </header>
          <OrderTable
            :table-title="`Request`"
            :has-search="true"
            :has-calendar="true"
            :has-column-filters="true"
          />
        </div>
      </div>
    </v-container>
  </div>
</template>
<script>

import OrderTable from '@/components/OrderTable'

export default {
  name: 'UI',
  components: {
    OrderTable
  },
  props: {
  },
  data () {
    return {
      orderPayload: {
        data: [
          {
            id: 8490,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:39:22.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'TCKU6498587',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211228,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8489,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:39:11.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'OOLU9883080',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211227,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8488,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:39:00.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'OOLU8707321',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211226,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8487,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:38:50.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'OOCU6745326',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211225,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8486,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:38:39.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'MAGU5644696',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211224,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8485,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:38:28.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'CSLU6007920',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211223,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8484,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:38:17.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'CBHU8965300',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211222,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8483,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:38:06.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'CBHU8960057',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211221,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          },
          {
            id: 8479,
            request_id: '5b4b5201-3252-4023-8839-4ce04f16656c',
            created_at: '2020-10-07T12:37:55.000000Z',
            equipment_type: null,
            shipment_designation: null,
            shipment_direction: 'import',
            tms_shipment_id: null,
            bill_to_address_id: 46617,
            unit_number: 'BMOU5595834',
            reference_number: 'L07-120465',
            bill_to_address: {
              id: 46617,
              location_name: "LAUFER GROUP INT'L LTD"
            },
            latest_ocr_request_status: {
              id: 1211220,
              status: 'process-ocr-output-file-complete',
              display_status: 'Verified',
              display_message: null
            }
          }
        ],
        links: {
          first: 'http://local.ordermaster.com/api/orders-2?page=1',
          last: 'http://local.ordermaster.com/api/orders-2?page=1',
          prev: null,
          next: null
        },
        meta: {
          current_page: 1,
          from: 1,
          last_page: 1,
          path: 'http://local.ordermaster.com/api/orders-2',
          per_page: 25,
          to: 9,
          total: 9
        }
      },
      isEditing: true,
      loaded: false,
      items: ['a', 'b', 'c'],
      tableData: {
        headers: [
          { text: 'Order ID', value: 'id' },
          { text: 'Update Status', value: 'latest_ocr_request_status.display_status', align: 'center' },
          { text: 'Container', value: 'unit_number' },
          { text: 'Bill To', value: 'bill_to_address.location_name' },
          { text: 'Direction', value: 'shipment_direction', align: 'center' },
          //   { text: 'SSL', value: 'ssl', align: 'center' },
          { text: 'Actions', value: 'actions', align: 'center' }
        ],
        items: [{
          orderID: 93288,
          updateStatus: 'new',
          container: 'ABCD1234567 (40 ft)',
          billTo: 'Crane Worldwide Logistics - ATL',
          direction: 'Import',
          ssl: 'ICL',
          actions: 'test'
        },
        {
          orderID: 93288,
          updateStatus: 'canceled',
          container: 'ABCD1234567 (40 ft)',
          billTo: 'Crane Worldwide Logistics - ATL',
          direction: 'Import',
          ssl: 'ICL',
          actions: 'test'
        },
        {
          orderID: 93288,
          updateStatus: 'updated',
          container: 'ABCD1234567 (40 ft)',
          billTo: 'Crane Worldwide Logistics - ATL',
          direction: 'Import',
          ssl: 'ICL',
          actions: 'test'
        }
        ]
      }
    }
  },
  created () {

  },

  methods: {
    change (e) {
      console.log(e)
    }
  }
}
</script>
<style lang="scss" scoped>

</style>
