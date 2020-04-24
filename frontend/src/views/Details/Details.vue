<template>
  <div
    class="details"
  >
    <DetailsSidebar />

    <DetailsFormEditing
      v-show="isEditing"
    />
    <DetailsFormViewing
      v-show="!isEditing"
    />

    <DetailsDocument />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'
import DetailsFormEditing from '@/views/Details/DetailsFormEditing'
import DetailsFormViewing from '@/views/Details/DetailsFormViewing'
import DetailsDocument from '@/views/Details/DetailsDocument'
import { detailsState, detailsMethods } from '@/views/Details/inner_store'
import { exampleForm as form } from '@/views/Details/inner_utils/example_form'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsFormEditing,
    DetailsFormViewing,
    DetailsDocument
  },

  computed: {
    isEditing () {
      return detailsState.isEditing
    }
  },

  beforeMount () {
    detailsMethods.setForm(form)
  }
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
