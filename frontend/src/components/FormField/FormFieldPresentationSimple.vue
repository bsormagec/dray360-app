<template>
  <div
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>

    <FormFieldEditingSetByDocument
      v-show="field.editing_set_by_document"
      :field="field"
    />

    <span
      v-show="!field.editing_set_by_document"
      class="field__value"
    >{{ field.value ? field.value : '--' }}</span>
  </div>
</template>

<script>
import FormFieldEditingSetByDocument from '@/components/FormField/FormFieldEditingSetByDocument'

export default {
  name: 'FormFieldPresentationSimple',

  components: {
    FormFieldEditingSetByDocument
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
