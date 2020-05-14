<template>
  <div class="form-field-element-textarea">
    <v-textarea
      outlined
      :placeholder="field.el.placeholder"
      :label="field.name"
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
    }
  }
}
</script>
