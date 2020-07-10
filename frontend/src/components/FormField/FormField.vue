<template>
  <div
    :id="`${cleanStrForId(field.name)}-${(field.readonly) ? 'viewing' : 'editing'}`"
    :test-id="field.name"
    class="form-field"
  >
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
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'

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
  },

  methods: {
    cleanStrForId
  }
}
</script>
