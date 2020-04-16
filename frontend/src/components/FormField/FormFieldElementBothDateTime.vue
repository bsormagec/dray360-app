<template>
  <div class="form-field-element-datetime">
    <FormFieldElementDate :field="field" />
    <FormFieldElementTime :field="field" />
  </div>
</template>

<script>
import FormFieldElementDate from '@/components/FormField/FormFieldElementDate'
import FormFieldElementTime from '@/components/FormField/FormFieldElementTime'

export default {
  name: 'FormFieldElementDateTime',

  components: {
    FormFieldElementDate,
    FormFieldElementTime
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
