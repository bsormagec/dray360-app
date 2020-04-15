<template>
  <div class="form-field-element-select">
    <v-select
      :items="field.el.options"
      :label="field.name"
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
    }
  }
}
</script>
