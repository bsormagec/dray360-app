/*
  TODO: Changes in the form are not being reflected, this could be a fault with Provide/Inject,
  could be fixed adding proxies to Provide/Inject though a simpler and more predictable solution
  cuold be to use a dedicated Vuex module for this.
*/

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
    formToModify: {},
    isEditing: true
  }),

  beforeMount () {
    this.formToModify = exampleForm
  },

  methods: {
    toggleIsEditing () {
      this.isEditing = !this.isEditing
    },
    setFormToModify (updatedForm) {
      this.formToModify = updatedForm
    }
  },

  provide () {
    return {
      [providerStateName]: {
        form: () => this.formToModify,
        isEditing: () => this.isEditing
      },
      [providerMethodsName]: {
        toggleIsEditing: this.toggleIsEditing,
        setFormToModify: this.setFormToModify
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
