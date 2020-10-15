<template>
  <div class="form-field-element">
    <FormFieldElementInput
      v-if="isInput"
      :is-editing="isEditing"
      :field="field"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementInputSelect
      v-else-if="isInputSelect"
      :is-editing="isEditing"
      :field="field"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementTextArea
      v-else-if="isTextArea"
      :is-editing="isEditing"
      :field="field"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementSelect
      v-else-if="isSelect"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementSwitch
      v-else-if="isSwitch"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementDate
      v-else-if="isDate"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementTime
      v-else-if="isTime"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementBothDateTime
      v-else-if="isDateTime"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementModalSelect
      v-else-if="isModalSelect"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementRadio
      v-else-if="isRadio"
      :field="field"
      :is-editing="isEditing"
      @change="e => $emit('change', e)"
    />

    <FormFieldElementModalAddress
      v-else-if="isModalAddress"
      :field="field"
      :is-editing="isEditing"
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
  </div>
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
import FormFieldElementModalAddress from '@/components/FormField/FormFieldElementModalAddress'

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
    FormFieldElementRadio,
    FormFieldElementModalAddress
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  computed: {
    isInput () {
      return this.field.el.type === fieldType.input
    },
    isPassword () {
      return this.field.el.type === fieldType.password
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
    },
    isModalAddress () {
      return this.field.el.type === fieldType.modalAddress
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element {
  width: 100%;
}

.element__label {
  display: block;
  font-size: rem(14) !important;
  text-transform: capitalize;
  color: map-get($colors, black-2);
}

.element__info-title {
  display: flex;
  align-items: center;
  font-size: rem(16) !important;
  font-weight: bold;
  line-height: rem(36);
  color: map-get($colors, grey-4);
  margin-bottom: rem(30);
  text-transform: capitalize;
  border-bottom: ren(1) solid map-get($colors, grey-10);

  i {
    margin-left: rem(10);
    width: rem(18);
    height: rem(18);
  }
}
</style>
