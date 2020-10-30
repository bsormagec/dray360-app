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
          >
            <Chip
              v-if="filter.value.length > 0"
              small
              closeable
              handle-close
              :color="getFilterColor(filter)"
              :meta="filter"
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
                <DateRangeCalendar
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
                <v-select
                  v-model="filters.status"
                  outlined
                  hide-details
                  multiple
                  chips
                  :items="statuses"
                  name="update_status"
                  prepend-icon="mdi-check-circle-outline"
                  dense
                  class="status-selector"
                />
              </v-col>
            </v-row>

            <v-row v-if="currentUser !== undefined && currentUser.is_superadmin">
              <!--

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
              </v-col> -->
            </v-row>
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
import DateRangeCalendar from './DateRange'
import Chip from '@/components/Chip'
import auth from '@/store/modules/auth'
import { mapState } from 'vuex'

export default {
  name: 'Filters',
  components: {
    DateRangeCalendar,
    Chip
  },
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
    updateType: {
      type: String,
      required: false,
      default: ''
    }
  },
  data () {
    return {
      open: false,
      statuses: [
        { text: 'Processing', value: 'Processing' },
        { text: 'Exception', value: 'Exception' },
        { text: 'Rejected', value: 'Rejected' },
        { text: 'Intake', value: 'Intake' },
        { text: 'Verified', value: 'Verified' },
        { text: 'Sending to TMS', value: 'Sending to TMS' },
        { text: 'Sent to TMS', value: 'Sent to TMS' },
        { text: 'Accepted by TMS', value: 'Accepted by TMS' }
      ],
      // these are the models for the form fields
      filters: {
        search: this.search,
        dateRange: this.dateRange,
        status: this.status,
        updateType: this.updateType
      },
      // these are the filters that get rendered as chips and emitted to the parent
      activeFilters: [],
      labels: {
        search: 'Search',
        dateRange: 'Date Range',
        status: 'Status',
        updateType: 'Update Type'
      },
      chipColors: {
        search: '#41B6E6',
        dateRange: '#FDAA63',
        status: '#77C19A',
        updateType: '#41B6E6',
        default: '#41B6E6'
      }
    }
  },
  computed: {
    hasActiveFilters () {
      // if any filter value has a length greater than zero return true
      return this.activeFilters.some(element => element.value.length > 0)
    },
    // necessary because of rendering multiple chips for different statuses
    displayFilters () {
      const dFilters = []
      this.activeFilters.forEach(filter => {
        if (filter.type === 'status') {
          filter.value.forEach(statusFilter => {
            dFilters.push({ type: 'status', value: statusFilter })
          })
        } else {
          dFilters.push(filter)
        }
      })

      return dFilters.filter(element => !!element.value.length)
    },
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser })
  },

  created () {
    this.setActiveFilters()
  },

  methods: {
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
      return this.chipColors.hasOwnProperty(filter.type) ? this.chipColors[filter.type] : this.chipColors.default
    },
    setActiveFilters () {
      this.activeFilters = Object.keys(this.filters).map(k => ({ type: k, value: this.filters[k] })).filter(element => !!element.value.length)
    },
    removeFilter (filter) {
      // remove from model
      if (filter.type === 'status') {
        this.filters[filter.type] = this.filters[filter.type].filter(element => element !== filter.value)
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
    }
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
