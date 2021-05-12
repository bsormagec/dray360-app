<template>
  <!-- <v-container> -->
  <v-row
    no-gutters

    class="field-mapping__container"
  >
    <v-col
      cols="12"
      md="2"
      class="field-mapping__filters"
    >
      <div class="primary pa-2 d-flex align-center justify-center">
        <SidebarNavigationButton />
        <h1 class="subtitle-2 text-center text-uppercase white--text">
          mapping admin panel
        </h1>
      </div>
      <div class="pa-5">
        <FieldMappingFilters
          :loading="loading"
          @fetching="loading = true"
          @done-fetching="clearSelection"
        />
      </div>
    </v-col>
    <v-col
      cols="12"
      md="3"
      class="field-mapping__fields px-5"
    >
      <div class="mt-4 mb-3 d-flex justify-space-between">
        <h3 class="h6 pa-0 ma-0 text-left primary--text">
          Order AI Fields
        </h3>
        <v-btn
          v-if="isDefaultFieldMap"
          color="primary"
          :loading="loading"
          @click="addNewFieldMap"
        >
          Add
        </v-btn>
      </div>
      <div>
        <FieldMappingList
          :selected-field="selectedField"
          :loading="loading"
          @change="fieldMapSelected"
        />
      </div>
    </v-col>
    <v-col
      cols="12"
      md="7"
      class="field-mapping__form px-5"
    >
      <FieldMappingForm
        :selected-field="selectedField"
        :loading="loading"
        @reset="resetFieldMaps"
        @save="saveFieldMap"
      />
    </v-col>
  </v-row>
  <!-- </v-container> -->
</template>

<script>
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton'
import FieldMappingFilters from './FieldMappingFilters'
import FieldMappingList from './FieldMappingList'
import FieldMappingForm from './FieldMappingForm'

import permissions from '@/mixins/permissions'
import isMobile from '@/mixins/is_mobile'

import { mapActions, mapState } from 'vuex'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import fieldMaps, { types as fieldMapsTypes } from '@/store/modules/field_maps'

import cloneDeep from 'lodash/cloneDeep'

export default {
  name: 'FieldMapping',

  components: {
    SidebarNavigationButton,
    FieldMappingFilters,
    FieldMappingList,
    FieldMappingForm,
  },

  mixins: [permissions, isMobile],

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
      d3canon_column: null,
      d3canon_name: null,
      d3canon_table_column: null,
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

  watch: {
    isMobile: function (newVal, oldVal) {
      if (newVal) {
        this.setSidebar({ show: false })
        return
      }
      this.setSidebar({ show: true })
    }
  },

  async beforeMount () {
    if (!this.isMobile) {
      this.setSidebar({ show: true })
    } else {
      this.setSidebar({ show: false })
    }

    this.loading = true
    await this.getFieldMaps({})
    this.loading = false
  },

  methods: {
    ...mapActions(utils.moduleName, [
      utilsActionTypes.setSidebar,
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
.field-mapping__container {
  height: 100vh;
  overflow: hidden;
  .field-mapping__fields, .field-mapping__form {
    height: 100vh;
    overflow-y: auto;
  }
}

.field-mapping__fields, .field-mapping__filters {
  border-right: rem(1) solid rgba(var(--v-slate-gray-base-rgb), 15%);
}

.field-mapping__item {
  min-height: rem(60);
  box-shadow: 0 3px 1px -2px rgb(0 0 0 / 5%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
}
</style>
