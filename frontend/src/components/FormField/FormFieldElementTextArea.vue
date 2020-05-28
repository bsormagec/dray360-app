<template>
  <div class="form-field-element-textarea">
    <v-textarea
      outlined
      :placeholder="field.el.placeholder"
      :label="field.presentationName || field.name"
      :value="field.value"
      @change="e => change(e)"
    />
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementTextArea',

  props: {
    field: {
      type: Object,
      required: true
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  mounted () {
    if (this.field.value) {
      this.change(this.field.value)
    }
  },

  methods: {
    change (e) {
      this.$emit('change', e)
    },

    syncValue () {
      if (this.field.value) this.$emit('change', this.field.value)
    }
  }
}
</script>
