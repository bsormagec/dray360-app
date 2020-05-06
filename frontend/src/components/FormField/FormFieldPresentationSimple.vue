<template>
  <div
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>

    <FormFieldEditingSetByDocument
      v-show="field.editing_set_by_document"
      :field="field"
      @change="e => $emit('change', e)"
      @close="e => $emit('close')"
    />

    <span
      v-show="!field.editing_set_by_document"
      class="field__value"
      @mouseover="startHover(field.name)"
    >{{ field.value ? field.value : '--' }}</span>
  </div>
</template>

<script>
import { modes } from '@/views/Details/inner_types'
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
    },
    callbacks: {
      type: Object,
      required: true
    }
  },

  computed: {
    isEditing () {
      return this.field.editing_set_by_document === modes.edit
    },

    isHovering () {
      return this.field.editing_set_by_document === modes.hover
    }
  },

  methods: {
    startHover (fieldName) {
      this.callbacks.startHover({ fieldName })
    },

    stopHover (fieldName) {
      this.callbacks.stopHover({ fieldName })
    }
  }
}
</script>
