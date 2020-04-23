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
import { exampleForm } from '@/views/Details/inner_utils/example_form'
import { providerStateName, providerMethodsName } from '@/views/Details/inner_types'

export default {
  name: 'Details',

  components: {
    DetailsSidebar,
    DetailsFormEditing,
    DetailsFormViewing,
    DetailsDocument
  },

  data: () => ({
    exampleForm,
    isEditing: true
  }),

  methods: {
    toggleIsEditing () {
      this.isEditing = !this.isEditing
    }
  },

  provide () {
    return {
      [providerStateName]: {
        form: () => this.exampleForm,
        isEditing: () => this.isEditing
      },
      [providerMethodsName]: {
        toggleIsEditing: this.toggleIsEditing
      }
    }
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

/*
  Adjust history.location when changing from viewing/editing and vice versa
*/
