<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="displayValue"
      :managed-by-template="managedByTemplate"
      :readonly="readonly"
      @accept="handleAccept"
      @accept-all="() => handleAccept(true)"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-switch
          :value="currentValue"
          color="primary"
          inset
          dense
          flat
          hide-details="true"
          :input-value="currentValue"
          :true-value="true"
          :false-value="false"
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
    editMode: { required: true, type: Boolean },
    managedByTemplate: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
  },

  data: (vm) => ({
    currentValue: vm.value
  }),

  computed: {
    displayValue () {
      return this.value === true || this.value === 1 ? 'Yes' : 'No'
    }
  },

  watch: {
    value () {
      this.currentValue = this.value
    }
  },

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

<style lang="scss" scoped>
.v-input--switch::v-deep {
  .v-input__slot {
    background-color: transparent;
    width: auto;
  }
}
</style>
