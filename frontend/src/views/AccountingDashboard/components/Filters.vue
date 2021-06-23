<template>
  <v-toolbar
    color="white"
    elevation="1"
    tile
    height="auto"
  >
    <v-container
      fluid
      class="pa-0 py-2"
    >
      <v-row
        align="center"
        dense
      >
        <v-col
          class="d-md-none d-lg-none d-xl-none"
          cols="12"
          md="auto"
        >
          <SidebarNavigationButton class="mb-2" />
        </v-col>
        <v-col
          cols="12"
          sm="3"
          md="2"
          class="mb-3 mb-md-0"
        >
          <v-select
            v-model="filters.companyId"
            :items="filteredCompanies"
            item-value="id"
            item-text="name"
            name="company_id"
            label="Company"
            hide-details
            multiple
            clearable
            placeholder="All Companies"
            :menu-props="{
              transition: 'scale-transition',
              offsetY: true,
              nudgeBottom: 12,
            }"
          >
            <template v-slot:selection="{ item, index }">
              <span
                v-if="index === 0"
              >{{ item.name }}</span>
              <span
                v-if="index === 1"
                class="grey--text text-caption ml-1"
              >
                {{ `(+ ${filters.companyId.length - 1} others)` }}
              </span>
            </template>
          </v-select>
        </v-col>
        <v-col
          cols="12"
          sm="3"
          md="2"
          class="mb-2 mb-md-0"
        >
          <DateRange v-model="filters.dateRange" />
        </v-col>
        <v-spacer />
        <v-col cols="auto">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                small
                color="primary"
                v-bind="attrs"
                v-on="on"
                @click="resetFilters"
              >
                <v-icon dense>
                  mdi-reload
                </v-icon>
              </v-btn>
            </template>
            <span class="text-caption">Reset Filters</span>
          </v-tooltip>
        </v-col>
        <v-col cols="auto">
          <OptionList
            :value="selectedHeaders"
            :options="computedColumns"
            icon="mdi-view-column"
            title="Show and hide columns"
            @input="newVal => $emit('colChange', newVal)"
          />
        </v-col>
        <v-col cols="auto">
          <OptionList
            :value="onlyBillable"
            :options="billableOptions"
            icon="mdi-filter-variant"
            @input="newVal => $emit('billableChange', newVal)"
          />
        </v-col>
        <v-col cols="auto">
          <v-btn
            color="primary"
            small
            :href="fileURL"
          >
            export
            <v-icon
              right
              dark
            >
              mdi-download
            </v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-toolbar>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import DateRange from './DateRange'
import OptionList from './OptionList'
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton'

import { metrics } from '@/enums/app_objects_types'
import { metricsLabels } from '../enums/metrics_labels'
import toParams from '@/utils/to_params'

export default {
  name: 'Filters',

  components: {
    DateRange,
    OptionList,
    SidebarNavigationButton
  },

  mixins: [permissions, allCompanies],

  props: {
    initialFilters: {
      type: Object,
      required: true,
      default: () => ({
        dateRange: [],
        companyId: [],
      })
    },
    selectedHeaders: {
      type: Array,
      required: true,
      default: () => ([])
    },
    onlyBillable: {
      type: Array,
      required: true,
      default: () => ([])
    }
  },

  data: (vm) => ({
    filters: {
      dateRange: vm.initialFilters.dateRange,
      companyId: vm.initialFilters.companyId,
    },
    billableOptions: [
      { name: 'Show only billable metrics', value: true },
    ],
  }),

  computed: {
    computedColumns () {
      const labels = metricsLabels
      delete labels.company_name
      return Object.keys(metricsLabels)
        .map(key => ({ name: metricsLabels[key].name, value: key }))
    },

    fileURL () {
      const { dateRange, companyId } = this.filters
      const params = toParams({
        metric: metrics.companyDaily,
        'filter[company_id]': companyId ? companyId.join(',') : null,
        start_date: dateRange[0],
        end_date: dateRange[1],
        to_export: this.selectedHeaders ? ['company_name'].concat(this.selectedHeaders).join(',') : null
      })
      return `${process.env.VUE_APP_APP_URL}/api/metrics-export?${params}`
    },

    filteredCompanies () {
      return this.companies.filter(
        ({ name }) => !name.toLowerCase().includes('onboarding') && !name.toLowerCase().includes('demo')
      )
    }
  },

  watch: {
    filters: {
      handler (newFilters) {
        this.$emit('change', newFilters)
      },
      deep: true,
    },

  },

  async beforeMount () {
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies(true)
    }
  },

  methods: {
    resetFilters () {
      this.$emit('resetFilters')
      this.filters = this.initialFilters
    }
  }
}
</script>

<style lang="scss" scoped>
.v-select.v-text-field::v-deep {
  & .v-select__selections span {
    &:first-child {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    &:last-of-type {
      width: fit-content;
    }
    & + input {
      display: none;
    }
  }
}
</style>
