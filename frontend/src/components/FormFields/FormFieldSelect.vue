<template>
  <div class="form-field">
    <FormFieldPresentation
      :edit-mode="editMode"
      :references="references"
      :label="label"
      :value="displayName"
      @accept="handleAccept"
    >
      <div class="form-field__group">
        <div class="form-field__label">
          {{ label }}
        </div>
        <v-select
          dense
          outlined
          clearable
          flat
          solo
          hide-details="true"
          :items="items"
          :value="value"
          :item-text="itemText"
          :item-value="itemValue"
          @change="handleChange"
        />
      </div>
    </FormFieldPresentation>
  </div>
</template>

<script>
import FormFieldPresentation from './FormFieldPresentation'

export default {
  name: 'FormFieldSelect',

  components: {
    FormFieldPresentation
  },

  props: {
    references: {
      type: String,
      default: null
    },
    label: {
      type: String,
      required: true
    },
    value: {
      required: true,
      default: ''
    },
    items: {
      type: Array,
      required: true
    },
    itemValue: {
      type: String,
      required: false,
      default: 'id'
    },
    itemText: {
      type: String,
      required: false,
      default: 'name'
    },
    editMode: {
      type: Boolean,
      required: true
    },
    displayValue: {
      type: Function,
      required: false,
      default: undefined
    }
  },

  data: (vm) => ({
    currentValue: vm.value
  }),
  computed: {
    displayName: function () {
      return this.displayValue !== undefined ? this.displayValue(this.value) : this.value
    }
  },
  methods: {
    handleChange (e) {
      this.currentValue = e === undefined ? null : e

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
.form-field-element-input-select {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  width: 100%;
  height: 100%;
  .v-input {
    flex-grow: 0;
    width: 50%;
    height: fit-content;
  }
}
</style>
