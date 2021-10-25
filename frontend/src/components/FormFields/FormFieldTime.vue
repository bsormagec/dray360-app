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
      @cancel="handleCancel"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-dialog
          v-model="isOpen"
          :close-on-content-click="false"
          :nudge-right="40"
          transition="scale-transition"
          offset-y
          width="290px"
        >
          <template v-slot:activator="{ on }">
            <v-text-field
              dense
              flat
              outlined
              solo
              hide-details="true"
              :value="currentValue"
              prepend-icon="mdi-calendar"
              readonly
              v-on="on"
            />
          </template>

          <v-time-picker
            :value="currentValue"
            format="24hr"
            @change="handleChange"
          />
        </v-dialog>
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'

export default {
  name: 'FormFieldTime',

  components: {
    FormFieldPresentation,
  },

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String },
    value: { required: true, default: '' },
    type: { required: false, type: String, default: 'text' },
    editMode: { required: true, type: Boolean },
    placeholder: { required: false, type: String, default: '' },
    managedByTemplate: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
    adminNotes: { type: String, required: false, default: '' },
  },

  data: (vm) => ({
    currentValue: vm.value,
    isOpen: false
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  methods: {
    handleChange (e) {
      this.currentValue = e
      this.isOpen = false
      if (this.editMode && this.references) {
        this.handleAccept()
      }
    },

    handleAccept (saveAll = false) {
      this.$emit('change', { value: this.currentValue, saveAll })
    },

    handleCancel () {
      this.currentValue = this.value
    }
  }
}
</script>

<style lang="scss" scoped>
.v-text-field::v-deep {
  align-items: center;
  .v-input__prepend-outer {
    margin: 0 rem(6) 0 0 !important;
  }
}
</style>
