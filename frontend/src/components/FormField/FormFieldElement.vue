<template>
  <FormFieldElementInput
    v-if="isInput"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementSelect
    v-else-if="isSelect"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementSwitch
    v-else-if="isSwitch"
    :field="field"
    @change="e => $emit('change', e)"
  />
</template>

<script>
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementSelect from '@/components/FormField/FormFieldElementSelect'
import FormFieldElementSwitch from '@/components/FormField/FormFieldElementSwitch'

export default {
  name: 'FormFieldElement',

  components: {
    FormFieldElementInput,
    FormFieldElementSelect,
    FormFieldElementSwitch
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  computed: {
    isInput () {
      return this.field.el.type === 'input'
    },
    isSelect () {
      return this.field.el.type === 'select'
    },
    isSwitch () {
      return this.field.el.type === 'switch'
    }
  }
}
</script>
