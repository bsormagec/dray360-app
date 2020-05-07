<template>
  <div
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>
    <span
      class="field__value"
    >{{ field.value ? field.value : '--' }}</span>
  </div>
</template>

<script>
import { modes } from '@/views/Details/inner_types'

export default {
  name: 'FormFieldHighlightView',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    modes
  }),

  computed: {
    isEditing () {
      return this.field.highlight === this.modes.edit
    }
  }
}
</script>
