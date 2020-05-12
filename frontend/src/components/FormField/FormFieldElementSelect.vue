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
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    selected: undefined
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  beforeMount () {
    if (this.initialized) {
      this.selected = this.field.el.options[0]
      this.$emit('change', this.selected)
    }
  },

  methods: {
    syncValue () {
      if (!this.field.value) return
      this.selected = this.field.value
    }
  }
}
</script>
