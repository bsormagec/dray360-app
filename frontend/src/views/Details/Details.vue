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
import { exampleForm } from '@/views/Details/inner_utils'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsForm
  },

  data: () => ({
    exampleForm
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
