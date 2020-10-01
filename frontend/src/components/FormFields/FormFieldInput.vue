<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="value"
      @accept="handleAccept"
    >
      <div class="form-field-element-input">
        <v-text-field
          :label="label"
          :placeholder="placeholder"
          :type="type"
          outlined
          dense
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
.form-field-element-input {
  width: 100%;
  fieldset {
    legend {
      font-size: 1.2rem !important;
    }

    transition: border-color 200ms ease-in-out !important;
    border: 0.1rem solid map-get($colors , grey-10) !important;
  }
  .v-input--is-focused fieldset {
    border-color: var(--v-primary-base) !important;
  }
  .v-input__slot, .v-input__control {
    border-top-left-radius: 0.2rem !important;
    border-top-right-radius: 0.2rem !important;
    border-bottom-left-radius: 0.2rem !important;
    border-bottom-right-radius: 0.2rem !important;
  }
  .v-label {
    font-size: 1.2rem !important;
    text-transform: capitalize;
    padding-right: 0.2rem !important;
    background: white !important;
  }
  input {
    font-size: 1.4rem !important;
    &::placeholder {

      text-transform: capitalize;
    }
  }
  .v-label--active {
    transform: translateY(-18px) scale(1) !important;
  }
}
</style>
