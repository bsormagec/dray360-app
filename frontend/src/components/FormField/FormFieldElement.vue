<template>
  <FormFieldElementInput
    v-if="isInput"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementSelect
    v-else-if="isSelect"
    :field="field"
    @change="e => $emit('change', e)"
  />
</template>

<script>
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementSelect from '@/components/FormField/FormFieldElementSelect'

export default {
  name: 'FormFieldElement',

  components: {
    FormFieldElementInput,
    FormFieldElementSelect
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
    },
    isSelect () {
      return this.field.el.type === 'select'
    }
  }
}
</script>
