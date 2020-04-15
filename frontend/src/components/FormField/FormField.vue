<template>
  <FormFieldPresentation
    v-if="readonly"
    :field="field"
  />

  <FormFieldElement
    v-else
    :field="field"
    @change="onChange"
  />
</template>

<script>
import FormFieldPresentation from '@/components/FormField/FormFieldPresentation'
import FormFieldElement from '@/components/FormField/FormFieldElement'

export default {
  name: 'FormField',

  components: {
    FormFieldPresentation,
    FormFieldElement
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
  },

  methods: {
    onChange (e) {
      console.log(this.field.name, e)
    }
  }
}
</script>
