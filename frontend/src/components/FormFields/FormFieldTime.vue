<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="value"
      @accept="handleAccept"
      @cancel="handleCancel"
    >
      <div class="form-field-element-date">
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
              :value="currentValue"
              :label="label"
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
        this.$emit('change', this.currentValue)
      }
    },
    handleAccept () {
      this.$emit('change', this.currentValue)
    },
    handleCancel () {
      this.currentValue = this.value
    }
  }
}
</script>
