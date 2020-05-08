<template>
  <div class="form-field-presentation">
    <div
      v-if="!isModalSelect"
      class="field"
    >
      <FormFieldHighlight
        v-show="isSimple"
        :field="field"
        :callbacks="callbacks"
        @change="e => $emit('change', e)"
        @close="e => $emit('close')"
      />

      <FormFieldPresentationComplex
        v-show="isComplex"
        :field="field"
      />
    </div>

    <FormFieldPresentationModalSelect
      v-else
      :field="field"
    />
  </div>
</template>

<script>
import { fieldType } from '@/enums/field_type'
import FormFieldHighlight from '@/components/FormField/FormFieldHighlight'
import FormFieldPresentationComplex from '@/components/FormField/FormFieldPresentationComplex'
import FormFieldPresentationModalSelect from '@/components/FormField/FormFieldPresentationModalSelect'

export default {
  name: 'FormFieldPresentation',

  components: {
    FormFieldHighlight,
    FormFieldPresentationComplex,
    FormFieldPresentationModalSelect
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
    isSimple () {
      return typeof this.field.value !== 'object'
    },

    isComplex () {
      return typeof this.field.value === 'object'
    },

    isModalSelect () {
      return this.field.el.type === fieldType.modalSelect
    }
  }
}
</script>

<style lang="scss">
.form-field-presentation {
  .field__group {
    display: flex;
    width: 100%;
    height: 100%;
    justify-content: space-between;
    align-items: center;
  }

  .field__name,
  .field__children .field__name {
    font-size: 1.4rem !important;
    font-weight: bold;
    text-transform: capitalize;
  }

  .field__name {
    width: 60%;
  }

  .field__value {
    cursor: pointer;
    text-align: right;
    word-break: break-word;
    width: 40%;
    transition: opacity 200ms ease-in-out;
  }

  .field__value,
  .field__children .field__value {
    font-size: 1.44rem !important;
    text-transform: capitalize;
  }

  .field__children {
    display: flex;
    justify-content: space-between;
    padding-left: 1rem;
  }
}
</style>
