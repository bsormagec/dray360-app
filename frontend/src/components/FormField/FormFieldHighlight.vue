<template>
  <div
    :class="`form-field-highlight ${field.highlight || ''} ${field.el.type}`"
    @mouseover="isMobile ? () => {} : callbacks.startHover({ field })"
    @mouseleave="isMobile ? () => {} : callbacks.stopHover({ field })"
    @click="callbacks.startEdit({ field })"
  >
    <FormFieldHighlightView
      v-show="!editMode"
      :field="field"
    />

    <div :class="`highlight__edit ${field.highlight || ''} ${field.el.type}`">
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
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'FormFieldHighlight',

  components: {
    FormFieldElement,
    FormFieldHighlightView,
    FormFieldHighlightBtns
  },

  mixins: [isMobile],

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
    &.input, &.text-area {
      min-height: unset;
    }
  }
}

.highlight__edit {
  &.edit {
    width: 100%;
    display: flex;
    align-items: center;
    padding: 1rem 3rem 0rem 1rem;

    &.input, &.text-area {
      padding: unset;
    }
  }
}
</style>
