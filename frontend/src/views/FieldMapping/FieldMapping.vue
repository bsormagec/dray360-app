<template>
  <v-sheet class="field-mapping__container">
    <FieldMappingFilters
      :loading="loading"
      :form-changed="formChanged"
      :custom-mapping="customMapping"
      @fetching="loading = true"
      @done-fetching="clearSelection"
      @cancel-edit="cancelEdit"
      @set-custom-mapping="setCustomMapping"
    />
    <v-container
      v-if="isDefaultFieldMap && customMapping"
      fluid
      fill-height
    >
      <v-row
        align="center"
        justify="center"
        fill-height
      >
        <v-col
          cols="6"
          align-content="center"
        >
          <img
            class="d-block mx-auto mb-3"
            src="@/assets/images/application_downtime.png"
            alt="No filter selected"
          >
          <h1 class="h6 primary--text text-center mb-3">
            Configuration Page not selected
          </h1>
          <p class="text-center">
            Select the custom configuration parameters at the top of the page before making changes to the fields.
          </p>
        </v-col>
      </v-row>
    </v-container>
    <v-container
      v-else
      fluid
      class="pa-0"
    >
      <v-row no-gutters>
        <v-col cols="5">
          <h2 class="h6 d-flex ma-3 primary--text">
            Order AI Fields
            <v-btn
              v-if="canAddFieldMaps"
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
          <v-card
            elevation="0"
            tile
            flat
            class="ma-3"
          >
            <v-text-field
              v-model="searchQuery"
              hide-details
              dense
              clearable
              prepend-icon="mdi-magnify"
              placeholder="Search by field name"
            />
            <v-divider class="my-2" />
          </v-card>
          <FieldMappingList
            :selected-field="selectedField"
            :loading="loading"
            :form-changed="formChanged"
            :query="searchQuery"
            @change="fieldMapSelected"
          />
        </v-col>
        <v-col cols="7">
          <template
            v-if="!selectedField"
          >
            <div
              class="mx-auto placeholder-container"
            >
              <img
                class="d-block mx-auto mb-3"
                src="@/assets/images/application_downtime.png"
                alt="No filter selected"
              >
              <h1 class="h6 primary--text text-center mb-3">
                No field selected
              </h1>
              <p class="text-center">
                To start making edits to this mapping configuration, find the field you wish to customize in the list of all Order AI fields on the left and click on that field to select it.
              </p>
            </div>
          </template>
          <FieldMappingForm
            v-else
            :view-only="!canAddFieldMaps"
            :selected-field="selectedField"
            :loading="loading"
            @reset="resetFieldMaps"
            @save="saveFieldMap"
            @form-changed="setFormChanged"
            @delete="handleFieldMapDeletion"
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
import fieldMaps, { actionTypes as fieldMapsActionTypes } from '@/store/modules/field_maps'

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
      readonly_roles: [],
    },
    formChanged: false,
    customMapping: false,
    searchQuery: ''
  }),

  computed: {
    ...mapState(fieldMaps.moduleName, {
      fieldMaps: state => state.fieldMaps,
      filters: state => state.filters,
    }),

    isDefaultFieldMap () {
      return !this.filters?.companyId && !this.filters?.variantId && !this.filters?.tmsProviderId
    },

    canAddFieldMaps () {
      const { companyId = null, variantId = null, tmsProviderId = null } = this.filters

      if (!companyId && !variantId && !tmsProviderId) {
        return this.hasPermission('field-maps-create')
      } else if (variantId && companyId) {
        return this.hasPermission('company-field-maps-create')
      } else if (companyId) {
        return this.hasPermission('company-field-maps-create')
      } else if (variantId) {
        return this.hasPermission('variant-field-maps-create')
      } else if (tmsProviderId) {
        return this.hasPermission('tms-field-maps-create')
      }

      return false
    }
  },

  async beforeMount () {
    this.loading = true
    await this.getFieldMaps({})
    this.loading = false

    window.addEventListener('beforeunload', this.preventNav)
  },

  beforeDestroy () {
    window.removeEventListener('beforeunload', this.preventNav)
  },

  beforeRouteLeave (to, from, next) {
    if (!this.formChanged) {
      next()
      return
    }
    this.setConfirmationDialog({
      title: 'Unsaved changes detected',
      text: 'Are you sure you want to leave this page without saving? Your changes will be lost.',
      onConfirm: () => {
        next()
      },
      onCancel: () => {}
    })
  },

  methods: {
    ...mapActions(utils.moduleName, [
      utilsActionTypes.setSnackbar,
      utilsActionTypes.setConfirmationDialog,
    ]),

    ...mapActions(fieldMaps.moduleName, [
      fieldMapsActionTypes.getFieldMaps,
      fieldMapsActionTypes.setFieldMap,
      fieldMapsActionTypes.deleteFieldMap,
      fieldMapsActionTypes.resetFieldMap,
      fieldMapsActionTypes.saveFieldMaps,
      fieldMapsActionTypes.updateFieldMapsNames,
    ]),

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

    cancelEdit () {
      this.clearSelection()
      this.formChanged = false
    },

    handleFieldMapDeletion ({ field }) {
      const fieldMapDeletion = async () => {
        this.selectedField = null
        this.loading = true
        this.deleteFieldMap({ field })
        await this.saveFieldMapsChanges()
        this.loading = false
      }
      if (!this.isDefaultFieldMap) {
        this.setConfirmationDialog({
          noWrap: true,
          title: 'Are you sure you want to delete this field?',
          text: `The field <code>'${field}'</code> will be permanently deleted from the system.`,
          onConfirm: () => {
            fieldMapDeletion()
          },
          onCancel: () => {}
        })
        return
      }

      this.setConfirmationDialog({
        noWrap: true,
        title: 'Are you sure you want to delete a system-wide field?',
        text: `The system-wide field <code>'${field}'</code> will be permanently deleted from the system. this will affect every order in the system that uses the deleted field.`,
        onConfirm: () => {
          fieldMapDeletion()
        },
        onCancel: () => {}
      })
    },

    saveFieldMap ({ field, fieldMap, newFieldMap = false }) {
      const saveFieldMap = async () => {
        this.loading = true
        this.setFieldMap({ field, fieldMap })
        await this.saveFieldMapsChanges()
        this.loading = false
      }

      if (!this.isDefaultFieldMap || newFieldMap) {
        saveFieldMap()
        return
      }

      this.setConfirmationDialog({
        title: 'System-wide defaults fields will change',
        text: 'Are you sure you want to change the system-wide defaults? This will affect every order in the system that uses the modified fields.',
        onConfirm: () => {
          saveFieldMap()
        },
        onCancel: () => {}
      })
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
        text: 'Please specify the new property name (usually the same as the d3canon name)',
        hasInputValue: true,
        confirmText: 'Add',
        cancelText: 'Cancel',
        onConfirm: d3CanonName => {
          const fieldMap = cloneDeep({ ...this.emptyFormFieldMap, d3canon_name: d3CanonName })
          if (this.customMapping) fieldMap.d3canon_name = null
          this.saveFieldMap({ field: d3CanonName, fieldMap, newFieldMap: true })
          this.updateFieldMapsNames()
        },
        onCancel: () => {}
      })
    },

    setFormChanged (newValue) {
      this.formChanged = newValue
    },

    setCustomMapping (newValue) {
      this.customMapping = newValue
    },

    preventNav (event) {
      if (!this.formChanged) return
      event.preventDefault()
      event.returnValue = ''
    }
  }
}
</script>

<style lang="scss" scoped>
.field-mapping__container::v-deep {
  height: calc(100vh - #{rem(40)});
}

.placeholder-container {
  max-width: 70%;
  height: calc(100vh - #{rem(40)});
  display: flex;
  flex-direction: column;
  justify-content: center;
}
</style>
