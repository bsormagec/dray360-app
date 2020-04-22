<template>
  <FormFieldElementInput
    v-if="isInput"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementInputSelect
    v-else-if="isInputSelect"
    :field="field"
    @change="e => $emit('change', e)"
  />

  <FormFieldElementTextArea
    v-else-if="isTextArea"
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

  <span
    v-else-if="isInfoTitle"
    class="element__info-title"
  >
    {{ field.name }}
    <v-icon color="primary">mdi-information</v-icon>
  </span>

  <span
    v-else-if="isLabel"
    class="element__label"
  >{{ field.name }}</span>
</template>

<script>
import { fieldType } from '@/enums/field_type'
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementInputSelect from '@/components/FormField/FormFieldElementInputSelect'
import FormFieldElementTextArea from '@/components/FormField/FormFieldElementTextArea'
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
    FormFieldElementInputSelect,
    FormFieldElementTextArea,
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
    isInputSelect () {
      return this.field.el.type === fieldType.inputSelect
    },
    isTextArea () {
      return this.field.el.type === fieldType.textArea
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
    },
    isInfoTitle () {
      return this.field.el.type === fieldType.infoTitle
    },
    isLabel () {
      return this.field.el.type === fieldType.label
    }
  }
}
</script>

<style lang="scss" scoped>
.element__label {
  display: block;
  font-size: 1.4rem !important;
  text-transform: capitalize;
  color: map-get($colors, black-2);
}

.element__info-title {
  display: flex;
  align-items: center;
  font-size: 1.6rem !important;
  font-weight: bold;
  line-height: 3.6rem;
  color: map-get($colors , grey-4);
  margin-bottom: 3rem;
  text-transform: capitalize;
  border-bottom: 0.1rem solid map-get($colors , grey-10);

  i {
    margin-left: 1rem;
    width: 1.8rem;
    height: 1.8rem;
  }
}
</style>
