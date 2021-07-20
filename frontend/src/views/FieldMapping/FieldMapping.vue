<template>
  <v-sheet class="field-mapping__container">
    <FieldMappingFilters
      :loading="loading"
      @fetching="loading = true"
      @done-fetching="clearSelection"
    />
    <v-container
      fluid
      class="pa-0"
    >
      <v-row no-gutters>
        <v-col cols="5">
          <h2 class="h6 d-flex ma-3 primary--text">
            Order AI Fields
            <v-btn
              v-if="isDefaultFieldMap"
              class="ml-auto"
              color="primary"
              text
              small
              :loading="loading"
              @click="addNewFieldMap"
            >
              Add
            </v-btn>
          </h2>
          <FieldMappingList
            :selected-field="selectedField"
            :loading="loading"
            @change="fieldMapSelected"
          />
        </v-col>
        <v-col cols="7">
          <FieldMappingForm
            :selected-field="selectedField"
            :loading="loading"
            @reset="resetFieldMaps"
            @save="saveFieldMap"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-sheet>
</template>

<script>
import FieldMappingFilters from './FieldMappingFilters'
import FieldMappingList from './FieldMappingList'
import FieldMappingForm from './FieldMappingForm'
import permissions from '@/mixins/permissions'

import { mapActions, mapState } from 'vuex'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import fieldMaps, { types as fieldMapsTypes } from '@/store/modules/field_maps'

import cloneDeep from 'lodash/cloneDeep'

export default {
  name: 'FieldMapping',

  components: {
    FieldMappingFilters,
    FieldMappingList,
    FieldMappingForm,
  },

  mixins: [permissions],

  data: () => ({
    loading: false,
    selectedField: null,
    emptyFormFieldMap: {
      abbyy_source_field: null,
      abbyy_source_regex: null,
      adminreview_if_missing: false,
      adminreview_validation_regex: null,
      available: false,
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
      templateable: false,
      use_template_value: false,
    },
  }),

  computed: {
    ...mapState(fieldMaps.moduleName, {
      fieldMaps: state => state.fieldMaps,
      filters: state => state.filters,
    }),

    isDefaultFieldMap () {
      return !this.filters?.companyId && !this.filters?.variantId && !this.filters?.tmsProviderId
    }
  },

  async beforeMount () {
    this.loading = true
    await this.getFieldMaps({})
    this.loading = false
  },

  methods: {
    ...mapActions(utils.moduleName, [
      utilsActionTypes.setSnackbar,
      utilsActionTypes.setConfirmationDialog,
    ]),

    ...mapActions(fieldMaps.moduleName, {
      getFieldMaps: fieldMapsTypes.GET_FIELD_MAPS,
      setFieldMap: fieldMapsTypes.SET_FIELD_MAP,
      resetFieldMap: fieldMapsTypes.RESET_FIELD_MAP,
      saveFieldMaps: fieldMapsTypes.SAVE_FIELD_MAPS,
    }),

    clearSelection () {
      this.loading = false
      this.selectedField = null
    },

    fieldMapSelected (newValue) {
      this.selectedField = newValue
    },

    resetFieldMaps (field) {
      this.loading = true
      this.resetFieldMap({ field })
      this.saveFieldMapsChanges()
      this.loading = false
    },

    saveFieldMap ({ field, fieldMap }) {
      this.loading = true
      this.setFieldMap({ field, fieldMap })
      this.saveFieldMapsChanges()
      this.loading = false
    },

    async saveFieldMapsChanges () {
      const [error] = await this.saveFieldMaps()

      if (error !== undefined) {
        this.setSnackbar({ message: 'There was an error saving the field mapping' })
        return
      }

      this.setSnackbar({ message: 'Field mapping saved' })
    },

    addNewFieldMap () {
      this.setConfirmationDialog({
        open: true,
        title: 'Add new field map',
        text: 'Please specify the D3 canon name',
        hasInputValue: true,
        confirmText: 'Add',
        cancelText: 'Cancel',
        onConfirm: d3CanonName => {
          const fieldMap = cloneDeep({ ...this.emptyFormFieldMap, d3canon_name: d3CanonName })
          this.saveFieldMap({ field: d3CanonName, fieldMap })
        },
        onCancel: () => {}
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.field-mapping__container::v-deep {
  height: calc(100vh - #{rem(40)});
}
</style>
