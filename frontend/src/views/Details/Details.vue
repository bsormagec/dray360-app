<template>
  <div
    class="details"
  >
    <DetailsSidebar />

    <DetailsForm :form="exampleForm" />

    <DetailsDocument />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsForm from '@/views/Details/DetailsForm'
import DetailsDocument from '@/views/Details/DetailsDocument.vue'
import { exampleForm } from '@/views/Details/inner_utils'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsForm,
    DetailsDocument
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
