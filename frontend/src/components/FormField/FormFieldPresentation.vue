<template>
  <div class="form-field-presentation">
    <div
      v-if="!isModalSelect"
      class="field"
    >
      <FormFieldPresentationSimple
        v-show="isSimple"
        :field="field"
        @change="e => $emit('change', e)"
        @close="e => $emit('close', e)"
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
import FormFieldPresentationSimple from '@/components/FormField/FormFieldPresentationSimple'
import FormFieldPresentationComplex from '@/components/FormField/FormFieldPresentationComplex'
import FormFieldPresentationModalSelect from '@/components/FormField/FormFieldPresentationModalSelect'

export default {
  name: 'FormFieldPresentation',

  components: {
    FormFieldPresentationSimple,
    FormFieldPresentationComplex,
    FormFieldPresentationModalSelect
  },

  props: {
    field: {
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
    justify-content: space-between;
    margin-bottom: 1.1rem;
  }

  .field__name,
  .field__children .field__name {
    font-size: 1.4rem !important;
    font-weight: bold;
    text-transform: capitalize;
  }

  .field__value {
    text-align: right;
    word-break: break-word;
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
