<template>
  <div class="form-field-element-input-select">
    <FormFieldElementInput
      :field="field"
      :style="{ flexGrow: '1' }"
      @change="changeInput"
    />
    <FormFieldElementSelect
      :field="field"
      :style="{ width: '7rem' }"
      :initialized="true"
      @change="changeSelect"
    />
  </div>
</template>

<script>
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
import FormFieldElementSelect from '@/components/FormField/FormFieldElementSelect'

export default {
  name: 'FormFieldElementInputSelect',

  components: {
    FormFieldElementInput,
    FormFieldElementSelect
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    inputValue: undefined,
    selectValue: undefined
  }),

  methods: {
    changeInput (e) {
      this.inputValue = e
      this.emitChange()
    },
    changeSelect (e) {
      this.selectValue = e
      this.emitChange()
    },
    emitChange () {
      if (!this.inputValue || !this.selectValue) return
      this.$emit('change', `${this.inputValue} ${this.selectValue}`)
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-input-select {
  display: flex;
}
</style>
