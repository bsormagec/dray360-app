/*
  TODO:
    - highlight complex
    - fix bug switch not updating
    - fix bug radio not updating
    - fix bug hazardous in inventory not highlighting
    - fix bug highlighting fields on new added inventory item
    - fix bug date not updating
    - fix bug time not updating (or wrong value being initialized)
    - fix bug radio (again)
    - fix select not updating
    - fix input-select (when sync select value is also set in input)
    - add scrolling *
    - highlight modal-select
    - update highlight for input and textarea styles
    - update inventory fields --> description
*/

<template>
  <div
    :class="`form-field-highlight ${field.highlight || ''}`"
    @mouseover="callbacks.startHover({ field })"
    @mouseleave="callbacks.stopHover({ field })"
    @click="callbacks.startEdit({ field })"
  >
    <FormFieldHighlightView
      v-show="!editMode"
      :field="field"
    />

    <div :class="`highlight__edit ${field.highlight || ''}`">
      <FormFieldElement
        v-show="editMode"
        :field="field"
        :is-editing="isEditing"
        @change="e => (value = e)"
      />

      <FormFieldHighlightBtns
        v-show="field.highlight"
        :edit-mode="editMode"
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
    isEditing: {
      type: Boolean,
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
    editMode () {
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
