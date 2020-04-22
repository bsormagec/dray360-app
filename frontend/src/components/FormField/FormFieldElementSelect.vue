<template>
  <div class="form-field-element-select">
    <v-select
      :items="field.el.options"
      :label="field.name"
      :value="selected"
      outlined
      dense
      @change="e => $emit('change', e)"
    />
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementSelect',

  props: {
    field: {
      type: Object,
      required: true
    },
    initialized: {
      type: Boolean,
      required: false,
      default: () => false
    }
  },

  data: () => ({
    selected: undefined
  }),

  beforeMount () {
    if (this.initialized) {
      this.selected = this.field.el.options[0]
      this.$emit('change', this.selected)
    }
  }
}
</script>
