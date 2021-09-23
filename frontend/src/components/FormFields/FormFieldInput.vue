<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="value"
      :managed-by-template="managedByTemplate"
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
          autofocus
          dense
          solo
          flat
          hide-details="true"
          :value="value"
          @keypress.enter.stop="submit"
          @keydown.esc="cancel"
          @input="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import orderForm, { actionTypes } from '@/store/modules/order-form'
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
    placeholder: { required: false, type: String, default: '' },
    managedByTemplate: { type: Boolean, required: false, default: false },
  },

  data: (vm) => ({
    currentValue: vm.value
  }),

  methods: {
    ...mapActions(orderForm.moduleName, [actionTypes.stopFieldEdit]),
    handleChange (e) {
      this.currentValue = e
      if (this.editMode && this.references) {
        this.handleAccept()
      }
    },
    submit () {
      this.stopFieldEdit({ path: this.references })
      this.handleAccept()
    },
    cancel () {
      this.stopFieldEdit({ path: this.references })
      this.currentValue = this.value
    },
    handleAccept (saveAll = false) {
      this.$emit('change', { value: this.currentValue, saveAll })
    }
  }
}
</script>
