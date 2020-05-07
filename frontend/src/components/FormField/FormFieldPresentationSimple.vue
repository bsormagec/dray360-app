<template>
  <div
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>

    <FormFieldPresentationValue
      :field="field"
      :callbacks="callbacks"
      @change="e => $emit('change', e)"
      @close="e => $emit('close')"
    />
  </div>
</template>

<script>
import FormFieldPresentationValue from '@/components/FormField/FormFieldPresentationValue'

export default {
  name: 'FormFieldPresentationSimple',

  components: {
    FormFieldPresentationValue
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    callbacks: {
      type: Object,
      required: true
    }
  }
}
</script>
