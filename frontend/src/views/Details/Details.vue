<template>
  <div
    class="details"
  >
    <DetailsSidebar />

    <DetailsForm :form="exampleForm" />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsForm from '@/views/Details/DetailsForm'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsForm
  },

  data: () => ({
    exampleForm: [
      {
        title: 'shipment',
        rootFields: [
          {
            name: 'shipment designation',
            value: 'international'
          },
          {
            name: 'shipment direction',
            value: 'import'
          },
          {
            name: 'shipment handling',
            value: 'drop & hook'
          },
          {
            name: 'one way',
            value: 'yes'
          },
          {
            name: 'expedite shipment',
            value: 'no'
          },
          {
            name: 'hazardous',
            value: 'yes'
          }
        ]
      }
    ]
  })
}
</script>

<style lang="scss" scoped>
.details {
  width: 100%;
  height: 100%;
  display: flex;
  padding-left: map-get($sizes , sidebar-desktop-width);
}
</style>
