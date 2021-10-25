<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="value"
      :managed-by-template="managedByTemplate"
      :readonly="readonly"
      :admin-notes="adminNotes"
      @accept="handleAccept"
      @accept-all="() => handleAccept(true)"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-text-field
          v-mask="'##:##'"
          :placeholder="placeholder"
          type="text"
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
import orderForm, { actionTypes as orderFormActionTypes } from '@/store/modules/order-form'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import FormFieldPresentation from './FormFieldPresentation'
import { mask } from 'vue-the-mask'

export default {
  name: 'FormFieldTimeMask',

  directives: { mask },

  components: {
    FormFieldPresentation,
  },

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
    editMode: { required: true, type: Boolean },
    placeholder: { required: false, type: String, default: '' },
    managedByTemplate: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
    adminNotes: { type: String, required: false, default: '' },
  },

  data: (vm) => ({
    currentValue: vm.value
  }),

  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setSnackbar]),

    ...mapActions(orderForm.moduleName, [orderFormActionTypes.stopFieldEdit]),

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
      if (this.currentValue !== null && this.currentValue !== '' && !this.isValidTime(this.currentValue)) {
        this.setSnackbar({ message: 'Please enter a valid date' })
        return
      }
      this.$emit('change', { value: this.currentValue, saveAll })
    },

    isValidTime (timeString) {
      const [hour, minute] = timeString.split(':')

      return (hour !== undefined && minute !== undefined) &&
        parseInt(hour, 10) < 24 &&
        parseInt(minute, 10) < 60
    }
  }
}
</script>
