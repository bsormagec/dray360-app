<template>
  <div
    v-if="selectedField"
  >
    <h3 class="h6 d-flex ma-3 primary--text">
      Options
      <v-spacer />
      <v-btn
        text
        small
        :loading="loading"
        :disabled="!formIsDirty"
        @click="resetFieldMaps"
      >
        Reset
      </v-btn>
      <v-btn
        small
        color="primary"
        :loading="loading"
        :disabled="!formIsDirty"
        @click="saveFieldMap"
      >
        Save
      </v-btn>
    </h3>
    <div class="field-mapping-form px-3 pb-3">
      <v-switch
        v-model="formFieldMap.screen_hide"
        :class="{'field-mapping-form-field__changed': hasChanged('screen_hide')}"
        label="Screen Hide"
        dense
        v-bind="fieldChangedAttributes('screen_hide')"
      />
      <v-switch
        v-model="formFieldMap.templateable"
        class="mt-0"
        :class="{'field-mapping-form-field__changed': hasChanged('templateable')}"
        label="Templateable"
        hide-details
        v-bind="fieldChangedAttributes('templateable')"
      />
      <v-switch
        v-model="formFieldMap.use_template_value"
        :class="{'field-mapping-form-field__changed': hasChanged('use_template_value')}"
        label="Use Template Value"
        dense
      />
      <v-text-field
        v-model="formFieldMap.constant_value"
        :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
        label="Constant Value"
        clearable
        v-bind="fieldChangedAttributes('constant_value')"
      />
      <v-text-field
        v-model="formFieldMap.d3canon_table"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_table')}"
        label="D3canon Table"
        clearable
        v-bind="fieldChangedAttributes('d3canon_table')"
      />
      <v-text-field
        v-model="formFieldMap.d3canon_column"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_column')}"
        label="D3canon Column"
        clearable
        v-bind="fieldChangedAttributes('d3canon_column')"
      />
      <v-text-field
        v-model="formFieldMap.screen_name"
        :class="{'field-mapping-form-field__changed': hasChanged('screen_name')}"
        label="Screen Name"
        clearable
        hide-details
        v-bind="fieldChangedAttributes('screen_name')"
      />
      <v-textarea
        v-model="formFieldMap.notes"
        :class="{'field-mapping-form-field__changed': hasChanged('notes')}"
        rows="3"
        label="Notes"
        clearable
        v-bind="fieldChangedAttributes('notes')"
      />
      <v-row>
        <v-checkbox
          v-model="abbySourceFieldFilter.old"
          label="Old Fields"
          class="mt-0 mb-2 mx-2"
          dense
          hide-details
        />
        <v-checkbox
          v-model="abbySourceFieldFilter.new"
          label="New Fields"
          class="mt-0 mb-2 mx-2"
          dense
          hide-details
        />
      </v-row>
      <v-autocomplete
        v-model="formFieldMap.abbyy_source_field"
        :class="{'field-mapping-form-field__changed': hasChanged('abbyy_source_field')}"
        :items="abbySourceField"
        label="Abbyy Source Field"
        clearable
        v-bind="fieldChangedAttributes('abbyy_source_field')"
      />
      <v-divider class="mb-3" />
      <h3 class="h6 pa-0 ma-0 mb-4 text-left primary--text">
        To be implemented
      </h3>
      <v-text-field
        v-model="formFieldMap.abbyy_source_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('abbyy_source_regex')}"
        label="Abbyy Source Regex"
        clearable
        hide-details
        disabled
        v-bind="fieldChangedAttributes('abbyy_source_regex')"
      />
      <v-switch
        v-model="formFieldMap.adminreview_if_missing"
        :class="{'field-mapping-form-field__changed': hasChanged('adminreview_if_missing')}"
        label="Admin Review if Missing"
        disabled
        dense
      />
      <v-text-field
        v-model="formFieldMap.adminreview_validation_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('adminreview_validation_regex')}"
        label="Admin Review Validation Regex"
        clearable
        hide-details
        disabled
        v-bind="fieldChangedAttributes('adminreview_validation_regex')"
      />
      <v-switch
        v-model="formFieldMap.available"
        :class="{'field-mapping-form-field__changed': hasChanged('available')}"
        label="Available"
        filled
        dense
        disabled
      />
      <v-text-field
        v-model="formFieldMap.cargowise_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('cargowise_destination')}"
        label="Cargowise Destination"
        clearable
        disabled
        v-bind="fieldChangedAttributes('cargowise_destination')"
      />
      <v-text-field
        v-model="formFieldMap.compcare_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('compcare_destination')}"
        label="Compcare Destination"
        clearable
        disabled
        v-bind="fieldChangedAttributes('compcare_destination')"
      />
      <v-text-field
        v-model="formFieldMap.post_process_source_field"
        :class="{'field-mapping-form-field__changed': hasChanged('post_process_source_field')}"
        label="Post Process Source Field"
        clearable
        disabled
        v-bind="fieldChangedAttributes('post_process_source_field')"
      />
      <v-text-field
        v-model="formFieldMap.post_process_source_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('post_process_source_regex')}"
        label="Post Process Source Regex"
        clearable
        disabled
        v-bind="fieldChangedAttributes('post_process_source_regex')"
      />
      <v-text-field
        v-model="formFieldMap.profittools_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('profittools_destination')}"
        label="Profittools Destination"
        clearable
        hide-details
        disabled
        v-bind="fieldChangedAttributes('profittools_destination')"
      />
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import fieldMaps from '@/store/modules/field_maps'
import deepDiff from 'deep-diff'
import cloneDeep from 'lodash/cloneDeep'
import { abbySourceFileds } from '@/enums/app_objects_types'

export default {
  name: 'FieldMappingForm',

  props: {
    loading: { type: Boolean, required: false, default: false },
    selectedField: { type: String, required: false, default: null },
  },

  data: () => ({
    formFieldMap: {
      abbyy_source_field: 'order_info.bill_to_address',
      abbyy_source_regex: null,
      adminreview_if_missing: false,
      adminreview_validation_regex: null,
      available: true,
      cargowise_destination: null,
      compcare_destination: null,
      constant_value: null,
      d3canon_name: 'bill_to_address_code',
      d3canon_table: 't_company_address_tms_code',
      d3canon_column: 'company_address_tms_code',
      notes: null,
      post_process_source_field: null,
      post_process_source_regex: null,
      profittools_destination: 'ds_ship_type',
      screen_hide: false,
      screen_name: 'Bill To',
      templateable: true,
      use_template_value: true,
    },
    abbySourceFieldFilter: {
      old: false,
      new: false
    },
    formIsDirty: false,
  }),

  computed: {
    ...mapGetters(fieldMaps.moduleName, ['fieldMapsChanges']),

    ...mapState(fieldMaps.moduleName, {
      fieldMaps: state => state.fieldMaps,
      defaultFieldMaps: state => state.defaultFieldMaps,
    }),

    abbySourceField () {
      return [
        ...abbySourceFileds.preset_fields,
        ...this.abbySourceFieldFilter.old ? abbySourceFileds.old_fields : [],
        ...this.abbySourceFieldFilter.new ? abbySourceFileds.new_fields : []
      ]
    }
  },

  watch: {
    selectedField () {
      this.selectedFieldUpdated()
    },

    formFieldMap: {
      handler: function (newVal) {
        const changes = (deepDiff(this.fieldMaps[this.selectedField], newVal) || [])
          .filter(change => {
            if (
              (change.lhs === null && change.rhs === '') ||
              (change.lhs === '' && change.rhs === null)
            ) {
              return false
            }
            return change.kind !== 'D'
          })
        this.formIsDirty = changes.length > 0
        this.$emit('form-changed', this.formIsDirty)
      },
      deep: true
    }
  },

  beforeMount () {
    this.selectedFieldUpdated()
  },

  methods: {
    selectedFieldUpdated () {
      this.formFieldMap = { ...cloneDeep(this.fieldMaps[this.selectedField]) }
      if (abbySourceFileds.old_fields.indexOf(this.selectedField) > -1) {
        this.abbySourceFieldFilter.old = true
        this.abbySourceFieldFilter.new = false
      } else {
        this.abbySourceFieldFilter.old = false
        this.abbySourceFieldFilter.new = true
      }
    },

    getColor (key) {
      return this.hasChanged(key)
        ? 'orange-changes lighten-5'
        : undefined
    },

    hasChanged (key) {
      return this.fieldMapsChanges.filter(change => {
        return change.path.includes(this.selectedField) &&
          change.path.includes(key)
      }).length !== 0
    },

    fieldChangedAttributes (key) {
      if (this.hasChanged(key)) {
        return {
          filled: true,
          color: 'warning',
          dense: true,
        }
      }
      return {
        dense: false,
      }
    },

    saveFieldMap () {
      this.$emit('save', { field: this.selectedField, fieldMap: this.formFieldMap })
      this.formIsDirty = false
      this.$emit('form-changed', this.formIsDirty)
    },

    resetFieldMaps () {
      this.$emit('reset', this.selectedField)
      this.formFieldMap = { ...cloneDeep(this.defaultFieldMaps[this.selectedField]) }
    }
  }
}
</script>

<style lang="scss" scoped>
.modified-field {
  color: var(--v-success-base) !important;
  caret-color: var(--v-success-base) !important;
  &::v-deep .v-label {
    color: var(--v-orange-changes-lighten5);
  }
}

.field-mapping-form {
  height: 100%;
  max-height: calc(100vh - #{rem(152)});
  overflow-y: auto;
}

.field-mapping-form-field__changed::v-deep {
  .v-input__control > .v-input__slot {
    &::before {
      border-color: var(--v-orange-changes-base);
    }
    background-color: rgba(var(--v-orange-changes-base-rgb), 0.1);
  }
}
</style>
