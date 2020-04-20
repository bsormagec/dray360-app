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

  <FormFieldElementDate
    v-else-if="isDate"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementTime
    v-else-if="isTime"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementBothDateTime
    v-else-if="isDateTime"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementModalSelect
    v-else-if="isModalSelect"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementRadio
    v-else-if="isRadio"
    :field="field"
    @change="e => $emit('change', e)"
  />
</template>

<script>
import { fieldType } from '@/enums/field_type'
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementSelect from '@/components/FormField/FormFieldElementSelect'
import FormFieldElementSwitch from '@/components/FormField/FormFieldElementSwitch'
import FormFieldElementDate from '@/components/FormField/FormFieldElementDate'
import FormFieldElementTime from '@/components/FormField/FormFieldElementTime'
import FormFieldElementBothDateTime from '@/components/FormField/FormFieldElementBothDateTime'
import FormFieldElementModalSelect from '@/components/FormField/FormFieldElementModalSelect'
import FormFieldElementRadio from '@/components/FormField/FormFieldElementRadio'

export default {
  name: 'FormFieldElement',

  components: {
    FormFieldElementInput,
    FormFieldElementSelect,
    FormFieldElementSwitch,
    FormFieldElementDate,
    FormFieldElementTime,
    FormFieldElementBothDateTime,
    FormFieldElementModalSelect,
    FormFieldElementRadio
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
    isDate () {
      return this.field.el.type === fieldType.date
    },
    isTime () {
      return this.field.el.type === fieldType.time
    },
    isDateTime () {
      return this.field.el.type === fieldType.dateTime
    },
    isModalSelect () {
      return this.field.el.type === fieldType.modalSelect
    },
    isRadio () {
      return this.field.el.type === fieldType.radio
    }
  }
}
</script>
