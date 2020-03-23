<template>
  <div class="list">
    <h1 class="list__title">
      Orders
    </h1>

    <v-data-table
      :headers="headers"
      :items="items"
    />
  </div>
</template>

<script>
export default {
  name: 'OrdersList',

  props: {
    items: {
      type: Array,
      required: true
    }
  },

  data: () => ({
    headers: [
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
  })
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
