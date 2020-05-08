<template>
  <div class="form-field">
    <FormFieldPresentation
      v-show="readonly"
      :field="field"
      :callbacks="callbacks"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
      @close="e => $emit('close', e)"
    />

    <FormFieldElement
      v-show="!readonly"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />
  </div>
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
    },
    isEditing: {
      type: Boolean,
      required: true
    },
    callbacks: {
      type: Object,
      required: true
    }
  }
}
</script>
