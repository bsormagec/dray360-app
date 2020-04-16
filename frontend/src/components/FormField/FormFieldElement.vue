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

  <FormFieldElementBothDateTime
    v-else-if="isDateTime"
    :field="field"
    @change="e => $emit('change', e)"
  />
</template>

<script>
import { fieldType } from '@/enums/field_type'
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementSelect from '@/components/FormField/FormFieldElementSelect'
import FormFieldElementSwitch from '@/components/FormField/FormFieldElementSwitch'
import FormFieldElementBothDateTime from '@/components/FormField/FormFieldElementBothDateTime'

export default {
  name: 'FormFieldElement',

  components: {
    FormFieldElementInput,
    FormFieldElementSelect,
    FormFieldElementSwitch,
    FormFieldElementBothDateTime
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  computed: {
    isInput () {
      return this.field.el.type === fieldType.input
    },
    isSelect () {
      return this.field.el.type === fieldType.select
    },
    isSwitch () {
      return this.field.el.type === fieldType.switch
    },
    isDateTime () {
      return this.field.el.type === fieldType.dateTime
    }
  }
}
</script>
