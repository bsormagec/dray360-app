<template>
  <FormFieldPresentation v-if="readonly" />

  <div v-else>
    <v-text-field
      :label="field.name"
      :placeholder="field.name"
      outlined
    />
  </div>
</template>

<script>
import FormFieldPresentation from '@/components/FormField/FormFieldPresentation'

export default {
  name: 'FormField',

  components: {
    FormFieldPresentation
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    readonly: {
      type: Boolean,
      required: false,
      default: () => false
    }
  }
}
</script>
