<template>
  <div class="form-field-element-textarea">
    <v-textarea
      outlined
      :placeholder="field.el.placeholder"
      :label="field.name"
      @change="e => $emit('change', e)"
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
  }
}
</script>
