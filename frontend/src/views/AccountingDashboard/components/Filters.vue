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
            :items="companies"
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
            v-model="cols"
            :options="computedColumns"
            icon="mdi-view-column"
            title="Show and hide columns"
          />
        </v-col>
        <v-col cols="auto">
          <OptionList
            v-model="filters.showOnlyBilable"
            :options="metrics"
            icon="mdi-filter-variant"
          />
        </v-col>
        <v-col cols="auto">
          <v-btn
            color="primary"
            small
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

import { metricsLabels } from '../enums/metrics_labels'

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
        companyId: null,
      })
    },
    hiddenCols: {
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
    cols: vm.hiddenCols,
    metrics: [
      { name: 'Show only bilable metrics' },
    ],
  }),

  computed: {
    computedColumns () {
      const labels = metricsLabels
      delete labels.company_name
      return Object.keys(metricsLabels)
        .map(key => ({ name: metricsLabels[key].name, value: key }))
    }
  },

  watch: {
    filters: {
      handler (newFilters) {
        this.$emit('change', newFilters)
      },
      deep: true,
    },

    cols: function (newColArray) {
      this.$emit('colChange', newColArray)
    }
  },

  async beforeMount () {
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies()
    }
  },
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
