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
        <v-textarea
          outlined
          auto-grow
          :placeholder="placeholder"
          :value="value"
          hide-details="true"
          flat
          dense
          solo
          @input="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'

export default {
  name: 'FormFieldTextArea',

  components: {
    FormFieldPresentation
  },

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
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

<style lang="scss" scoped>
.v-textarea::v-deep {
  .v-input__slot {
    height: auto;
    .v-text-field__slot {
      margin-right: 0;
    }
  }
}
.form-field::v-deep {
  .form-field__group,
  .form-field__group + .action-btns {
    align-items: flex-start;
  }
}
</style>
