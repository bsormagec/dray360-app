/*
  TODO:
    - highlight complex
    - fix bug switch not updating *
    - highlight modal-select
    - fix bug hazardous in inventory not highlighting
    - fix bug highlighting fields on new added inventory item
*/

<template>
  <div
    :class="`form-field-highlight ${field.highlight || ''}`"
    @mouseover="callbacks.startHover({ fieldName: field.name })"
    @mouseleave="callbacks.stopHover({ fieldName: field.name })"
    @click="callbacks.startEdit({ fieldName: field.name })"
  >
    <FormFieldHighlightView
      v-show="!isEditing"
      :field="field"
    />

    <div :class="`highlight__edit ${field.highlight || ''}`">
      <FormFieldElement
        v-show="isEditing"
        :field="field"
        @change="e => (value = e)"
      />

      <FormFieldHighlightBtns
        v-show="field.highlight"
        :is-editing="isEditing"
        @close="close"
        @accept="accept"
      />
    </div>
  </div>
</template>

<script>
import { modes } from '@/views/Details/inner_types'
import FormFieldElement from '@/components/FormField/FormFieldElement'
import FormFieldHighlightView from '@/components/FormField/FormFieldHighlightView'
import FormFieldHighlightBtns from '@/components/FormField/FormFieldHighlightBtns'

export default {
  name: 'FormFieldHighlight',

  components: {
    FormFieldElement,
    FormFieldHighlightView,
    FormFieldHighlightBtns
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

  data: () => ({
    modes,
    value: undefined
  }),

  computed: {
    isEditing () {
      return this.field.highlight === this.modes.edit
    }
  },

  methods: {
    close () {
      this.$emit('close')
    },

    accept () {
      this.$emit('change', this.value)
      this.close()
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-highlight {
  position: relative;
  cursor: pointer;
  display: flex;
  align-items: center;
  width: 100%;
  min-height: 3rem;
  margin-bottom: 1.1rem;
  border: 0.1rem solid;
  border-color: map-get($colors, white);
  border-radius: 0.2rem;
  transition: all 200ms ease-in-out;

  &.hover, &.edit {
    border-color: map-get($colors, blue);
  }

  &.hover {
    background: rgba(map-get($colors , blue), 0.15);
    padding-left: 1rem;
    padding-right: 3rem;
  }

  &.edit {
    min-height: 10rem;
  }
}

.highlight__edit {
  &.edit {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 1rem 3rem 0rem 1rem;
  }
}
</style>
