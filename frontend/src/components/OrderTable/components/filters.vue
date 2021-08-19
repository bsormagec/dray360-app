<template>
  <div>
    <v-dialog
      v-model="open"
      max-width="420px"
      class="filters px-4"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          text
          v-bind="attrs"
          class="filters-trigger"
          small
          v-on="on"
        >
          <v-icon left>
            mdi-magnify
          </v-icon>
          Filters
          <v-icon
            right
            color="#41B6E6"
          >
            mdi-plus-circle
          </v-icon>
        </v-btn>
        <ul
          class="active-filters"
        >
          <li
            v-for="(filter, index) in displayFilters"
            :key="index"
            class="pt-1"
          >
            <Chip
              v-if="hasValidValue(filter.value)"
              small
              closeable
              handle-close
              :color="getFilterColor(filter)"
              :represents="filter"
              @closed="removeFilter"
            >
              {{ getChipText(filter) }}
            </Chip>
          </li>
        </ul>
      </template>

      <v-card class="pb-5">
        <v-card-title class="d-flex justify-space-between pb-4 px-5">
          <h6>Apply Filters</h6>
          <v-btn
            icon
            @click.prevent="closeDialog"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider />
        <v-card-text class="px-5">
          <v-container class="filters">
            <v-row>
              <v-col cols="4 d-flex align-center">
                <label
                  for="search"
                  class="filter-label"
                >
                  Search Terms
                </label>
              </v-col>
              <v-col cols="8">
                <v-text-field
                  v-model="filters.search"
                  name="search"
                  hide-details
                  prepend-icon="mdi-magnify"
                  outlined
                  dense
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="4 d-flex align-center">
                <label class="filter-label">
                  Date Range
                </label>
              </v-col>
              <v-col cols="8">
                <DateRange
                  v-model="filters.dateRange"
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="4 d-flex align-center">
                <label
                  for="update_status"
                  hide-details
                  class="filter-label"
                >
                  Status
                </label>
              </v-col>
              <v-col cols="8">
                <v-autocomplete
                  v-model="filters.status"
                  :items="statuses"
                  outlined
                  hide-details
                  multiple
                  name="update_status"
                  prepend-icon="mdi-check-circle-outline"
                  dense
                  class="status-selector"
                >
                  <template v-slot:selection="{ item, index }">
                    <v-chip v-if="index === 0">
                      <span>{{ item.text }}</span>
                    </v-chip>
                    <span
                      v-if="index === 1"
                      class="grey--text caption"
                    >
                      (+{{ filters.status.length - 1 }} others)
                    </span>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
            <v-row v-if="hasPermission('system-status-filter')">
              <v-col cols="4 d-flex align-center">
                <label
                  for="system_status"
                  hide-details
                  class="filter-label"
                >
                  System Status
                </label>
              </v-col>
              <v-col cols="8">
                <v-autocomplete
                  v-model="filters.system_status"
                  :items="system_statuses"
                  outlined
                  hide-details
                  multiple
                  name="system_status"
                  prepend-icon="mdi-check-circle-outline"
                  dense
                  class="status-selector"
                >
                  <template v-slot:selection="{ item, index }">
                    <v-chip v-if="index === 0">
                      <span>{{ item.text }}</span>
                    </v-chip>
                    <span
                      v-if="index === 1"
                      class="grey--text caption"
                    >
                      (+{{ filters.system_status.length - 1 }} others)
                    </span>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
            <v-row v-if="canViewOtherCompanies()">
              <v-col cols="4 d-flex align-center">
                <label
                  for="company_id"
                  hide-details
                  class="filter-label"
                >
                  Company
                </label>
              </v-col>
              <v-col cols="8">
                <v-autocomplete
                  v-model="filters.company_id"
                  :items="companies"
                  item-value="id"
                  item-text="name"
                  outlined
                  hide-details
                  multiple
                  name="company_id"
                  prepend-icon="mdi-office-building-outline"
                  dense
                  class="status-selector"
                >
                  <template v-slot:selection="{ item, index }">
                    <v-chip v-if="index === 0">
                      <span>{{ item.name }}</span>
                    </v-chip>
                    <span
                      v-if="index === 1"
                      class="grey--text caption"
                    >
                      (+{{ filters.company_id.length - 1 }} others)
                    </span>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
            <v-row v-if="hiddenItemsFilter">
              <v-col cols="4 d-flex align-center">
                <label
                  for="company_id"
                  hide-details
                  class="filter-label"
                >{{ hiddenItemsText }}</label>
              </v-col>
              <v-col cols="8">
                <v-switch
                  v-model="filters.displayHidden"
                  color="primary"
                  dense
                  inset
                  flat
                  hide-details="true"
                  :prepend-icon="!filters.displayHidden ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                  :true-value="true"
                  :false-value="false"
                />
              </v-col>
            </v-row>
            <!-- <v-row v-if="currentUser !== undefined && currentUser.is_superadmin">

                FUTURE STATE:

                <v-col cols="4 d-flex align-center">
                <label
                  for="update_type"
                  class="filter-label"
                >
                  Update Type
                </label>
              </v-col>
               <v-col cols="8">
                <v-select
                  v-model="filters.updateType"
                  outlined
                  hide-details
                  name="update_type"
                  prepend-icon="mdi-restore"
                  dense
                />
              </v-col>
            </v-row> -->
          </v-container>
        </v-card-text>
        <v-card-actions class="mr-4">
          <v-spacer />
          <v-btn
            color="blue darken-1"
            text
            @click="closeDialog"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            dark
            @click="closeDialogAndSetFilters"
          >
            Apply
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import DateRange from '@/components/DateRange'
import Chip from '@/components/Chip'
import auth from '@/store/modules/auth'
import { mapState } from 'vuex'

import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { statuses, displayStatuses } from '@/enums/app_objects_types'

export default {
  name: 'Filters',
  components: {
    DateRange,
    Chip
  },
  mixins: [permissions, allCompanies],
  props: {
    search: {
      type: String,
      required: false,
      default: ''
    },
    dateRange: {
      type: Array,
      required: false,
      default: () => []
    },
    status: {
      type: Array,
      required: false,
      default: () => []
    },
    systemStatus: {
      type: Array,
      required: false,
      default: () => []
    },
    companyId: {
      type: Array,
      required: false,
      default: () => []
    },
    updateType: {
      type: String,
      required: false,
      default: ''
    },
    displayHidden: {
      type: Boolean,
      required: false,
      default: false
    },
    hiddenItemsFilter: {
      type: Boolean,
      required: false,
      default: false
    },
    hiddenItemsText: {
      type: String,
      required: false,
      default: 'Show Hidden Items'
    },
    hiddenItemsLabel: {
      type: String,
      required: false,
      default: 'Hidden Items Shown'
    }
  },
  data () {
    return {
      open: false,
      statuses: [
        { text: displayStatuses.processing, value: displayStatuses.processing },
        { text: displayStatuses.exception, value: displayStatuses.exception },
        { text: displayStatuses.rejected, value: displayStatuses.rejected },
        { text: displayStatuses.intake, value: displayStatuses.intake },
        { text: displayStatuses.processed, value: displayStatuses.processed },
        { text: displayStatuses.autoSubmitted, value: displayStatuses.autoSubmitted },
        { text: displayStatuses.needsReview, value: displayStatuses.needsReview },
        { text: displayStatuses.sendingToTms, value: displayStatuses.sendingToTms },
        { text: displayStatuses.markDone, value: displayStatuses.markDone },
        { text: displayStatuses.markUndone, value: displayStatuses.markUndone },
        { text: displayStatuses.sentToTms, value: displayStatuses.sentToTms },
        { text: displayStatuses.acceptedByTms, value: displayStatuses.acceptedByTms },
        { text: displayStatuses.tmsWarning, value: displayStatuses.tmsWarning },
        { text: displayStatuses.tmsError, value: displayStatuses.tmsError },
        { text: displayStatuses.uploadingImage, value: displayStatuses.uploadingImage },
        { text: displayStatuses.imageUploadFailed, value: displayStatuses.imageUploadFailed },
        { text: displayStatuses.imageUploaded, value: displayStatuses.imageUploaded },
      ],
      system_statuses: [
        { text: statuses.intakeAccepted, value: statuses.intakeAccepted },
        { text: statuses.intakeAcceptedDatafile, value: statuses.intakeAcceptedDatafile },
        { text: statuses.intakeException, value: statuses.intakeException },
        { text: statuses.intakeRejected, value: statuses.intakeRejected },
        { text: statuses.intakeStarted, value: statuses.intakeStarted },
        { text: statuses.ocrCompleted, value: statuses.ocrCompleted },
        { text: statuses.ocrPostProcessingReview, value: statuses.ocrPostProcessingReview },
        { text: statuses.ocrPostProcessingComplete, value: statuses.ocrPostProcessingComplete },
        { text: statuses.ocrPostProcessingAutosubmitted, value: statuses.ocrPostProcessingAutosubmitted },
        { text: statuses.ocrPostProcessingError, value: statuses.ocrPostProcessingError },
        { text: statuses.processOcrOutputFileReview, value: statuses.processOcrOutputFileReview },
        { text: statuses.ocrWaiting, value: statuses.ocrWaiting },
        { text: statuses.ocrTimedout, value: statuses.ocrTimedout },
        { text: statuses.replicatedFromExistingOrder, value: statuses.replicatedFromExistingOrder },
        { text: statuses.processOcrOutputFileComplete, value: statuses.processOcrOutputFileComplete },
        { text: statuses.processOcrOutputFileError, value: statuses.processOcrOutputFileError },
        { text: statuses.uploadRequested, value: statuses.uploadRequested },

        { text: statuses.sendingToWint, value: statuses.sendingToWint },
        { text: statuses.autoSendingToWint, value: statuses.autoSendingToWint },
        { text: statuses.failureSendingToWint, value: statuses.failureSendingToWint },
        { text: statuses.successSendingToWint, value: statuses.successSendingToWint },
        { text: statuses.shipmentCreatedByWint, value: statuses.shipmentCreatedByWint },
        { text: statuses.shipmentNotCreatedByWint, value: statuses.shipmentNotCreatedByWint },
        { text: statuses.updatingToWint, value: statuses.updatingToWint },
        { text: statuses.failureUpdatingToWint, value: statuses.failureUpdatingToWint },
        { text: statuses.successUpdatingToWint, value: statuses.successUpdatingToWint },
        { text: statuses.shipmentUpdatedByWint, value: statuses.shipmentUpdatedByWint },
        { text: statuses.shipmentNotUpdatedByWint, value: statuses.shipmentNotUpdatedByWint },

        { text: statuses.sendingToChainio, value: statuses.sendingToChainio },
        { text: statuses.autoSendingToChainio, value: statuses.autoSendingToChainio },
        { text: statuses.failureSendingToChainio, value: statuses.failureSendingToChainio },
        { text: statuses.successSendingToChainio, value: statuses.successSendingToChainio },
        { text: statuses.shipmentCreatedByChainio, value: statuses.shipmentCreatedByChainio },
        { text: statuses.shipmentNotCreatedByChainio, value: statuses.shipmentNotCreatedByChainio },

        { text: statuses.sendingToCompcare, value: statuses.sendingToCompcare },
        { text: statuses.autoSendingToCompcare, value: statuses.autoSendingToCompcare },
        { text: statuses.failureSendingToCompcare, value: statuses.failureSendingToCompcare },
        { text: statuses.successSendingToCompcare, value: statuses.successSendingToCompcare },
        { text: statuses.shipmentCreatedByCompcare, value: statuses.shipmentCreatedByCompcare },
        { text: statuses.shipmentNotCreatedByCompcare, value: statuses.shipmentNotCreatedByCompcare },

        { text: statuses.updatesPriorOrder, value: statuses.updatesPriorOrder },
        { text: statuses.requestMarkedDone, value: statuses.requestMarkedDone },
        { text: statuses.requestMarkedUndone, value: statuses.requestMarkedUndone },
        { text: statuses.updatedBySubsequentOrder, value: statuses.updatedBySubsequentOrder },
        { text: statuses.successImageuplodingToBlackfl, value: statuses.successImageuplodingToBlackfl },
        { text: statuses.failureImageuplodingToBlackfl, value: statuses.failureImageuplodingToBlackfl },
        { text: statuses.untriedImageuplodingToBlackfl, value: statuses.untriedImageuplodingToBlackfl },

        { text: statuses.uploadImageRequested, value: statuses.uploadImageRequested },
        { text: statuses.uploadImageFailed, value: statuses.uploadImageFailed },
        { text: statuses.uploadImageSucceeded, value: statuses.uploadImageSucceeded },
      ],
      // these are the models for the form fields
      filters: {
        search: this.search,
        dateRange: this.dateRange,
        status: this.status,
        system_status: this.systemStatus,
        company_id: this.companyId,
        updateType: this.updateType,
        displayHidden: this.displayHidden
      },
      // these are the filters that get rendered as chips and emitted to the parent
      activeFilters: [],
      labels: {
        search: 'Search',
        dateRange: 'Date Range',
        status: 'Status',
        system_status: 'System Status',
        company_id: 'Company',
        updateType: 'Update Type',
        displayHidden: this.hiddenItemsLabel
      },
      chipColors: {
        search: '#41B6E6',
        dateRange: '#FDAA63',
        displayHidden: '#8293A0',
        status: '#77C19A',
        system_status: '#77C19A',
        company_id: '#41B6E6',
        updateType: '#41B6E6',
        default: '#41B6E6'
      }
    }
  },
  computed: {
    hasActiveFilters () {
      // if any filter value has a length greater than zero return true
      return this.activeFilters.some(element => this.hasValidValue(element.value))
      // return this.activeFilters.some(element => element.value.length > 0)
    },
    // necessary because of rendering multiple chips for different statuses
    displayFilters () {
      const dFilters = []
      this.activeFilters.forEach(filter => {
        if (filter.type === 'status') {
          filter.value.forEach(statusFilter => {
            dFilters.push({ type: 'status', value: statusFilter })
          })
        } else if (filter.type === 'system_status') {
          filter.value.forEach(statusFilter => {
            dFilters.push({ type: 'system_status', value: statusFilter })
          })
        } else if (filter.type === 'company_id') {
          filter.value.forEach(companyId => {
            dFilters.push({ type: 'company_id', value: this.findCompanyById(companyId).name })
          })
        } else {
          dFilters.push(filter)
        }
      })

      return dFilters.filter(element => this.hasValidValue(element.value))
    },
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser })
  },

  created () {
    this.statuses = this.statuses.sort((a, b) => a.text > b.text ? 1 : -1)
    this.system_statuses = this.system_statuses.sort((a, b) => a.text > b.text ? 1 : -1)
    this.setActiveFilters()
  },

  async beforeMount () {
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies()
    }
  },

  methods: {
    hasValidValue (value) {
      if (typeof value === 'boolean') return value
      return !!value.length
    },
    // closes filters modal
    closeDialog () {
      this.open = false
    },
    // closes filters modal and applies filters
    closeDialogAndSetFilters () {
      this.open = false
      this.setActiveFilters()
      this.onFiltersChange()
    },
    getFilterColor (filter) {
      // eslint-disable-next-line no-prototype-builtins
      return this.chipColors.hasOwnProperty(filter.type) ? this.chipColors[filter.type] : this.chipColors.default
    },
    setActiveFilters () {
      this.activeFilters = Object.keys(this.filters).map(k => ({ type: k, value: this.filters[k] })).filter(element => this.hasValidValue(element.value))
    },
    findCompanyById (id) {
      return this.companies.filter(company => company.id === id)[0] || { name: '' }
    },
    removeFilter (filter) {
      // remove from model
      if (filter.type === 'status' || filter.type === 'system_status') {
        this.filters[filter.type] = this.filters[filter.type].filter(element => element !== filter.value)
      } else if (filter.type === 'company_id') {
        this.filters[filter.type] = this.filters[filter.type].filter(element => this.findCompanyById(element).name !== filter.value)
      } else if (Array.isArray(filter.value)) {
        this.filters[filter.type] = []
      } else {
        this.filters[filter.type] = ''
      }

      this.setActiveFilters()
      this.onFiltersChange()
    },
    // used for explicitly getting the filters out of the component
    getActiveFilters () {
      return this.activeFilters
    },
    // emit filter change event to parent
    onFiltersChange () {
      this.$emit('change', this.activeFilters)
    },
    // text formatting for filter chips
    getChipText (filter) {
      let labelValue
      if (Array.isArray(filter.value)) {
        labelValue = filter.value.join(' - ')
      } else if (typeof filter.value === 'boolean') {
        return this.labels[filter.type]
      } else {
        labelValue = filter.value
      }

      return `${this.labels[filter.type]}: ${labelValue}`
    },

    // reset filters to prop values
    reset () {
      Object.keys(this.filters).forEach(filterKey => {
        this.filters[filterKey] = this[filterKey]
      })
      this.setActiveFilters()

      this.$emit('change', this.activeFilters)
    },

    setFiltersFromState (stateFilters) {
      this.filters = { ...stateFilters }
      this.setActiveFilters()
    },
    // set all filters to blank or empty array
    clearFilters () {
      Object.keys(this.filters).forEach(key => {
        if (Array.isArray(this.filters[key])) {
          this.filters[key] = []
        } else {
          this.filters[key] = ''
        }
      })
      this.setActiveFilters()
      this.onFiltersChange()
    },
  }
}
</script>

<style lang="scss" scoped>
  .filters {
    max-width: rem(400);
    margin: auto;
  }
  .filters-trigger::v-deep .v-btn__content .v-icon--left, .v-btn__content .v-icon--right {
    font-size: 18px;
  }
  .filters-trigger::v-deep .v-btn__content .v-icon--left {
    margin-right:0.7rem;
  }
  .filters-trigger::v-deep .v-btn__content .v-icon--right {
    margin-left:0.7rem;
  }
  .filter-label {
    font-size: rem(13);
    line-height: rem(20);
    font-weight: 700;
  }
  .filter-header {
    position: relative;
  }
  .active-filters {
    display: inline-block;
    list-style: none;
    padding:0;
    margin: 0 0 0 rem(5);
    li {
      margin: 0 rem(5);
      display: inline-block;
    }
  }
  .status-selector::v-deep .v-chip {
    margin: rem(5);
  }
</style>
