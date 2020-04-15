<template>
  <FormFieldElementInput
    v-if="isInput"
    :field="field"
    @change="e => $emit('change', e)"
  />
</template>

<script>
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'

export default {
  name: 'FormFieldElement',

  components: {
    FormFieldElementInput,
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  computed: {
    isInput () {
      return this.field.el.type === 'input'
    }
  }
}
</script>
