<template>
  <div class="form-field-element-input-select">
    <FormFieldElementInput
      :field="{ ...field, value: inputValue }"
      :is-editing="isEditing"
      :style="{ flexGrow: '1' }"
      @change="changeInput"
    />
    <FormFieldElementSelect
      :field="{ ...field, value: selectValue }"
      :style="{ width: '70px' }"
      :is-editing="isEditing"
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
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    inputValue: undefined,
    selectValue: undefined
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

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
      this.$emit('change', `${this.inputValue} - ${this.selectValue}`)
    },

    syncValue () {
      if (!this.field.value) return
      const values = this.field.value.split(' - ')
      this.inputValue = values[0]
      this.selectValue = values[1]
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-input-select {
  display: flex;
}
</style>
