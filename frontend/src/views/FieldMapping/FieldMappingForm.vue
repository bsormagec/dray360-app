<template>
  <div
    v-if="selectedField"
  >
    <h3 class="h6 d-flex ma-3 primary--text">
      Mapping Options for "{{ selectedField }}"
      <v-spacer />
      <v-btn
        v-if="!viewOnly"
        text
        small
        :loading="loading"
        :disabled="!formIsDirty"
        @click="resetFieldMaps"
      >
        Reset
      </v-btn>
      <v-btn
        v-if="!viewOnly"
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
      <v-container class="pa-0">
        <v-row no-gutters>
          <v-col
            cols="auto"
            class="mr-4"
          >
            <v-switch
              v-model="formFieldMap.screen_hide"
              :class="{'field-mapping-form-field__changed': hasChanged('screen_hide')}"
              :disabled="viewOnly"
              label="Screen Hide"
              dense
              hide-details
              v-bind="fieldChangedAttributes('screen_hide')"
            />
          </v-col>
          <v-col>
            <v-text-field
              v-model="formFieldMap.screen_name"
              :class="{'field-mapping-form-field__changed': hasChanged('screen_name')}"
              :disabled="viewOnly"
              label="Screen Name"
              clearable
              v-bind="fieldChangedAttributes('screen_name')"
              hide-details
            />
          </v-col>
        </v-row>
      </v-container>
      <v-container class="pa-0">
        <v-row no-gutters>
          <v-col
            cols="auto"
            class="mr-4"
          >
            <v-switch
              v-model="formFieldMap.use_constant_as_default_only"
              :class="{'field-mapping-form-field__changed': hasChanged('use_constant_as_default_only')}"
              :disabled="viewOnly"
              label="Default only"
              dense
              hide-details
              v-bind="fieldChangedAttributes('use_constant_as_default_only')"
            />
          </v-col>
          <v-col>
            <v-select
              v-if="fieldNameMatch(/event\d+_type/g, true)"
              v-model="formFieldMap.constant_value"
              :items="customFieldInput.eventTypes"
              :disabled="viewOnly"
              item-text="name"
              item-value="value"
              :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
              label="Constant Value"
              clearable
              v-bind="fieldChangedAttributes('constant_value')"
              @change="value => onCustomInputClear('constant_value', value)"
            />
            <v-select
              v-else-if="fieldNameMatch('shipment_direction')"
              v-model="formFieldMap.constant_value"
              :disabled="viewOnly"
              :items="customFieldInput.shipmentDirection"
              item-text="name"
              item-value="value"
              :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
              label="Constant Value"
              clearable
              v-bind="fieldChangedAttributes('constant_value')"
              @change="value => onCustomInputClear('constant_value', value)"
            />
            <v-select
              v-else-if="fieldNameMatch(/hazmat|hazardous|expedite/g, true)"
              v-model="formFieldMap.constant_value"
              :disabled="viewOnly"
              :items="customFieldInput.booleanFields"
              item-text="name"
              item-value="value"
              :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
              label="Constant Value"
              clearable
              v-bind="fieldChangedAttributes('constant_value')"
              @change="value => onCustomInputClear('constant_value', value)"
            />
            <v-text-field
              v-else
              v-model="formFieldMap.constant_value"
              :disabled="viewOnly"
              :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
              label="Constant Value"
              clearable
              v-bind="fieldChangedAttributes('constant_value')"
            />
          </v-col>
        </v-row>
      </v-container>
      <v-switch
        v-model="formFieldMap.available"
        :class="{'field-mapping-form-field__changed': hasChanged('available')}"
        :disabled="viewOnly"
        label="Available"
        dense
        hide-details
        v-bind="fieldChangedAttributes('available')"
      />
      <v-switch
        v-model="formFieldMap.use_template_value"
        :class="{'field-mapping-form-field__changed': hasChanged('use_template_value')}"
        :disabled="viewOnly"
        label="Use Template Value"
        dense
        v-bind="fieldChangedAttributes('use_template_value')"
      />
      <v-text-field
        v-model="formFieldMap.shipment_direction_filter"
        :class="{'field-mapping-form-field__changed': hasChanged('shipment_direction_filter')}"
        :disabled="viewOnly"
        label="Shipment Direction Filter"
        clearable
        v-bind="fieldChangedAttributes('shipment_direction_filter')"
      />
      <v-text-field
        v-model="formFieldMap.d3canon_name"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_name')}"
        :disabled="viewOnly"
        label="D3canon Name"
        clearable
        v-bind="fieldChangedAttributes('d3canon_name')"
        hide-details
      />
      <!-- <v-row>
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
      </v-row> -->
      <v-autocomplete
        v-model="formFieldMap.abbyy_source_field"
        :class="{'field-mapping-form-field__changed': hasChanged('abbyy_source_field')}"
        :disabled="viewOnly"
        :items="abbySourceField"
        label="Abbyy Source Field"
        clearable
        v-bind="fieldChangedAttributes('abbyy_source_field')"
      />
      <v-text-field
        v-model="formFieldMap.profittools_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('profittools_profittools_destinationdestination')}"
        :disabled="viewOnly"
        label="Profittools Destination"
        clearable
        hide-details
        v-bind="fieldChangedAttributes('profittools_destination')"
      />
      <v-textarea
        v-model="formFieldMap.notes"
        :class="{'field-mapping-form-field__changed': hasChanged('notes')}"
        :disabled="viewOnly"
        rows="3"
        label="Notes"
        clearable
        v-bind="fieldChangedAttributes('notes')"
      />

      <h3 class="h6 pa-0 mb-4 mt-16 text-left primary--text">
        To be implemented...
      </h3>
      <v-text-field
        v-model="formFieldMap.d3canon_table"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_table')}"
        label="D3canon Table"
        clearable
        disabled
        v-bind="fieldChangedAttributes('d3canon_table')"
      />
      <v-text-field
        v-model="formFieldMap.d3canon_column"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_column')}"
        label="D3canon Column"
        clearable
        disabled
        v-bind="fieldChangedAttributes('d3canon_column')"
      />
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
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import fieldMaps from '@/store/modules/field_maps'
import deepDiff from 'deep-diff'
import cloneDeep from 'lodash/cloneDeep'
import { abbySourceFileds } from '@/enums/app_objects_types'
import { eventTypes, shipmentDirection, booleanFields } from '@/enums/field_type'

export default {
  name: 'FieldMappingForm',

  props: {
    viewOnly: { type: Boolean, required: false, default: false },
    loading: { type: Boolean, required: false, default: false },
    selectedField: { type: String, required: false, default: null },
  },

  data: () => ({
    formFieldMap: {
      abbyy_source_field: null,
      abbyy_source_regex: null,
      adminreview_if_missing: false,
      adminreview_validation_regex: null,
      available: true,
      cargowise_destination: null,
      compcare_destination: null,
      constant_value: null,
      d3canon_name: null,
      d3canon_table: null,
      d3canon_column: null,
      notes: null,
      post_process_source_field: null,
      post_process_source_regex: null,
      profittools_destination: null,
      screen_hide: false,
      screen_name: null,
      shipment_direction_filter: null,
      use_template_value: true,
      use_constant_as_default_only: false,
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
    },

    customFieldInput () {
      return {
        eventTypes,
        shipmentDirection,
        booleanFields,
      }
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
    },

    fieldNameMatch (stringToMatch, isRegEx) {
      if (isRegEx) {
        return this.selectedField.match(stringToMatch) !== null
      }
      return this.selectedField === stringToMatch
    },

    onCustomInputClear (field, value) {
      if (value === undefined) {
        this.formFieldMap[field] = null
      }
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
