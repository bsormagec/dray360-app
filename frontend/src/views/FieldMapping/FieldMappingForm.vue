<template>
  <div
    v-if="selectedField"
    class="d-flex flex-column"
  >
    <div class="field-mapping-title">
      <h3 class="h6 pa-0 ma-0 text-left primary--text">
        Options
      </h3>
    </div>
    <div class="field-mapping-form">
      <v-switch
        v-model="formFieldMap.templateable"
        :class="{'field-mapping-form-field__changed': hasChanged('templateable')}"
        label="Templateable"
        hide-details
      />
      <v-switch
        v-model="formFieldMap.use_template_value"
        :class="{'field-mapping-form-field__changed': hasChanged('use_template_value')}"
        label="Use Template Value"
      />
      <v-text-field
        v-model="formFieldMap.constant_value"
        :class="{'field-mapping-form-field__changed': hasChanged('constant_value')}"
        label="Constant Value"
        clearable
        outlined
        dense
      />
      <v-text-field
        v-model="formFieldMap.d3canon_table"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_table')}"
        label="D3canon Table"
        clearable
        outlined
        dense
      />
      <v-text-field
        v-model="formFieldMap.d3canon_column"
        :class="{'field-mapping-form-field__changed': hasChanged('d3canon_column')}"
        label="D3canon Column"
        clearable
        outlined
        dense
      />
      <v-textarea
        v-model="formFieldMap.notes"
        :class="{'field-mapping-form-field__changed': hasChanged('notes')}"
        rows="3"
        label="Notes"
        clearable
        outlined
        dense
      />
      <v-text-field
        v-model="formFieldMap.abbyy_source_field"
        :class="{'field-mapping-form-field__changed': hasChanged('abbyy_source_field')}"
        label="Abbyy Source Field"
        clearable
        outlined
        dense
      />
      <v-divider class="mb-4" />
      <h3 class="h6 pa-0 ma-0 mb-4 text-left primary--text">
        To be implemented
      </h3>
      <v-text-field
        v-model="formFieldMap.abbyy_source_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('abbyy_source_regex')}"
        label="Abbyy Source Regex"
        clearable
        outlined
        dense
        hide-details
        disabled
      />
      <v-switch
        v-model="formFieldMap.adminreview_if_missing"
        :class="{'field-mapping-form-field__changed': hasChanged('adminreview_if_missing')}"
        label="Admin Review if Missing"
        disabled
      />
      <v-text-field
        v-model="formFieldMap.adminreview_validation_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('adminreview_validation_regex')}"
        label="Admin Review Validation Regex"
        clearable
        outlined
        dense
        hide-details
        disabled
      />
      <v-switch
        v-model="formFieldMap.available"
        :class="{'field-mapping-form-field__changed': hasChanged('available')}"
        label="Available"
        filled
        disabled
      />
      <v-text-field
        v-model="formFieldMap.cargowise_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('cargowise_destination')}"
        label="Cargowise Destination"
        clearable
        outlined
        dense
        disabled
      />
      <v-text-field
        v-model="formFieldMap.compcare_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('compcare_destination')}"
        label="Compcare Destination"
        clearable
        outlined
        dense
        disabled
      />
      <v-text-field
        v-model="formFieldMap.post_process_source_field"
        :class="{'field-mapping-form-field__changed': hasChanged('post_process_source_field')}"
        label="Post Process Source Field"
        clearable
        outlined
        dense
        disabled
      />
      <v-text-field
        v-model="formFieldMap.post_process_source_regex"
        :class="{'field-mapping-form-field__changed': hasChanged('post_process_source_regex')}"
        label="Post Process Source Regex"
        clearable
        outlined
        dense
        disabled
      />
      <v-text-field
        v-model="formFieldMap.profittools_destination"
        :class="{'field-mapping-form-field__changed': hasChanged('profittools_destination')}"
        label="Profittools Destination"
        clearable
        outlined
        dense
        hide-details
        disabled
      />
      <v-switch
        v-model="formFieldMap.screen_hide"
        :class="{'field-mapping-form-field__changed': hasChanged('screen_hide')}"
        label="Screen Hide"
        disabled
      />
      <v-text-field
        v-model="formFieldMap.screen_name"
        :class="{'field-mapping-form-field__changed': hasChanged('screen_name')}"
        label="Screen Name"
        clearable
        outlined
        dense
        hide-details
        disabled
      />
    </div>
    <div class="field-mapping-buttons">
      <v-btn
        color="primary"
        outlined
        :loading="loading"
        @click="$emit('reset', selectedField)"
      >
        Reset
      </v-btn>
      <v-btn
        color="primary"
        :loading="loading"
        @click="$emit('save', {field: selectedField, fieldMap: formFieldMap })"
      >
        Save
      </v-btn>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import fieldMaps from '@/store/modules/field_maps'

import cloneDeep from 'lodash/cloneDeep'

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
  }),

  computed: {
    ...mapGetters(fieldMaps.moduleName, ['fieldMapsChanges']),

    ...mapState(fieldMaps.moduleName, {
      fieldMaps: state => state.fieldMaps,
    }),
  },

  watch: {
    selectedField () {
      this.formFieldMap = { ...cloneDeep(this.fieldMaps[this.selectedField]) }
    }
  },

  methods: {
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
  }
}
</script>
<style lang="scss" scoped>
.modified-field {
  color: var(--v-success-base) !important;
  caret-color: var(--v-success-base) !important;
  &::v-deep .v-label{
    color: var(--v-orange-changes-lighten5);
  }
}

.field-mapping-title {
  margin-top: rem(16);
  margin-bottom: rem(10);
}

.field-mapping-form {
  max-height: calc(100vh - 8rem);
  overflow-y: auto;
  padding-top: rem(4);
}

.field-mapping-buttons {
  display: flex;
  margin-top: rem(16);
  justify-content: space-between;
}

.field-mapping-form-field__changed::v-deep .v-input__slot {
  background-color: rgba(var(--v-orange-changes-base-rgb), 0.1);
}
</style>
