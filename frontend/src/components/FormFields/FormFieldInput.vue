<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="value"
      @accept="handleAccept"
      @accept-all="() => handleAccept(true)"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-text-field
          :placeholder="placeholder"
          :type="type"
          outlined
          dense
          solo
          flat
          hide-details="true"
          :value="value"
          @input="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'

export default {
  name: 'FormFieldInput',

  components: {
    FormFieldPresentation
  },

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
    type: { required: false, type: String, default: 'text' },
    editMode: { required: true, type: Boolean },
    placeholder: { required: false, type: String, default: '' }
  },

  data: (vm) => ({
    currentValue: vm.value
  }),

  methods: {
    handleChange (e) {
      this.currentValue = e
      if (this.editMode && this.references) {
        this.handleAccept()
      }
    },
    handleAccept (saveAll = false) {
      this.$emit('change', { value: this.currentValue, saveAll })
    }
  }
}
</script>
