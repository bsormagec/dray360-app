<template>
  <div class="form-field-element-input">
    <v-text-field
      :label="field.name"
      :placeholder="field.el.placeholder"
      outlined
      dense
      :value="value"
      @change="e => $emit('change', e)"
    />
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementInput',

  props: {
    field: {
      type: Object,
      required: true
    },
    value: {
      type: String,
      required: false,
      default: ''
    }
  }
}
</script>
