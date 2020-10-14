<template>
  <div class="wrapper">
    <div class="container">
      <h1 class="text-center">
        Pattern Library
      </h1>
      <div class="row">
        <div class="col-12">
          <h2>Table</h2>
          <Table
            table-title="Title"
            :active-page="1"
            :customheaders="tableData.headers"
            :custom-items="tableData.items"
            :has-search="true"
            :has-calendar="false"
            :has-column-filters="true"
            :has-bulk-actions="true"
            :has-action-button="{showButton: true, action: '/'}"
            :has-add-button="{showButton: true, action: '/'}"
            :bulk-actions="['Reset Selected Users Passwords', 'Deactivate Selected Users', 'Delete Selected Users']"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import Table from '@/components/Table'
import { providerStateName } from '@/views/Orders/inner_types'

// const callbacks = {
//   startEdit: (obj) => {
//     obj.field.highlight = 'edit'
//   },
//   stopEdit: (obj) => {
//     obj.field.highlight = undefined
//   },
//   startHover: (obj) => {
//     if (obj.field.highlight === 'edit') return
//     obj.field.highlight = 'hover'
//   },
//   stopHover: (obj) => {
//     if (obj.field.highlight === 'edit') return
//     obj.field.highlight = undefined
//   }
// }
export default {
  name: 'UI',
  components: {
    Table
  },
  props: {
  },
  data () {
    return {
      isEditing: true,
      loaded: false,
      items: ['a', 'b', 'c'],
      tableData: {
        headers: [
          { text: 'Order ID', value: 'orderID' },
          { text: 'Update Status', value: 'updateStatus' },
          { text: 'Container', value: 'container' },
          { text: 'Bill To', value: 'billTo' },
          { text: 'Direction', value: 'direction' },
          { text: 'SSL', value: 'ssl' },
          { text: 'Actions', value: 'actions' }
        ],
        items: [{
          orderID: 93288,
          updateStatus: '',
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
