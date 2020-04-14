<template>
  <v-text-field
    :label="field.name"
    :placeholder="field.name"
    outlined
  />
</template>

<script>
export default {
  name: 'FormField',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
