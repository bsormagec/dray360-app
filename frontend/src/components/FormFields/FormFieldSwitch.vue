<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="displayValue"
      @accept="handleAccept"
    >
      <div class="form-field-element-switch">
        <v-switch
          :value="value"
          :label="label"
          inset
          @input="handleChange"
          @change="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'
export default {
  name: 'FormFieldSwitch',

  components: {
    FormFieldPresentation
  },

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
    editMode: { required: true, type: Boolean }
  },

  data: (vm) => ({
    currentValue: vm.value
  }),

  computed: {
    displayValue () {
      return this.value === true || this.value === 1 ? 'Yes' : 'No'
    }
  },

  methods: {
    handleChange (e) {
      this.currentValue = e
      if (this.editMode && this.references) {
        this.$emit('change', this.currentValue)
      }
    },
    handleAccept () {
      this.$emit('change', this.currentValue)
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-switch {
  margin-left: 2rem;
  width: 100%;
}
</style>
