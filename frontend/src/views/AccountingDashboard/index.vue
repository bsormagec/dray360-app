<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
      no-gutter
    >
      <v-col
        cols="12"
        class="audits__list pa-0"
      >
        <v-data-table
          :header-props="{ sortIcon: 'mdi-chevron-up'}"
          :headers="displayCols"
          :items-per-page="metrics.length"
          :items="metrics"
          :loading="loading"
          :options.sync="options"
          :server-items-length="metrics.length"
          class="fixed-table"
          calculate-widths
          height="100%"
          hide-default-footer
          loading-text="Loading... Please wait"
        >
          <template v-slot:top>
            <Filters
              :initial-filters="filters"
              :selected-headers="selectedHeaders"
              :only-billable="onlyBillable"
              @change="filtersChanged"
              @colChange="colChanged"
              @billableChange="billableChange"
              @resetFilters="resetFilters"
            />
          </template>
          <template
            v-for="(item, i) in hasFormula"
            v-slot:[`header.${item}`]="{ header }"
          >
            <span :key="i">{{ header.text }}</span>
            <v-menu
              :key="item.value"
              open-on-hover
              top
              offset-y
              offset-x
            >
              <template v-slot:activator="{ on, attrs }">
                <div
                  class="th-button"
                  role="button"
                  v-bind="attrs"
                  v-on="on"
                />
              </template>
              <v-card
                max-width="344"
                outlined
              >
                <v-card-title color="primary">
                  <div>
                    <div class="text-body-1">
                      Formula
                    </div>
                    <div class="text-caption">
                      {{ header.value }}
                    </div>
                  </div>
                  <v-spacer />
                  <v-icon>mdi-puzzle</v-icon>
                </v-card-title>
                <v-card-text>
                  {{ header.formula }}
                </v-card-text>
              </v-card>
            </v-menu>
          </template>
          <template slot="body.append">
            <tr
              class="d-block"
              style="opacity: 0"
            />
            <tr>
              <td>Total</td>
              <td
                v-for="(item, i) in colsTotal"
                :key="i"
              >
                {{ item }}
              </td>
            </tr>
          </template>
        </v-data-table>
      </v-col>
    </v-container>
  </div>
</template>

<script>
import format from 'date-fns/format'
import cloneDeep from 'lodash/cloneDeep'

import permissions from '@/mixins/permissions'

import Filters from './components/Filters'

import { getAccountingMetrics } from '@/store/api_calls/accounting_metrics'
import { metrics } from '@/enums/app_objects_types'
import { metricsLabels } from './enums/metrics_labels'

export default {
  name: 'AccountingDashboard',

  components: {
    Filters
  },

  mixins: [permissions],

  data: () => ({
    filters: {
      dateRange: [],
      companyId: [],
    },
    options: {
      sortBy: ['company_name'],
      sortDesc: [false]
    },
    sort: 'company_name',
    loading: false,
    metrics: [],
    selectedHeaders: [],
    onlyBillable: [],
  }),

  computed: {
    tableHeaders () {
      return Object.keys(metricsLabels).map(key => ({
        text: metricsLabels[key].name,
        align: 'start',
        value: key,
        width: '220px',
        formula: metricsLabels[key]?.formula ?? null,
        billable: metricsLabels[key]?.billable ?? null
      }))
    },

    hasFormula () {
      return Object.keys(metricsLabels).filter((key) => metricsLabels[key]?.formula)
    },

    areBilable () {
      return Object.keys(metricsLabels).filter(key => metricsLabels[key]?.billable)
    },

    colsTotal () {
      const obj = cloneDeep(this.metrics)
      return this.displayCols
        .filter(o => o.value !== 'company_name')
        .map(col => obj.reduce((t, cur) => t + Number(cur[col.value]), 0))
    },

    displayCols () {
      return [
        {
          text: 'Company Name',
          align: 'start',
          value: 'company_name',
          width: '220px',
          formula: null,
          billable: null
        },
        ...this.tableHeaders.filter(o => this.selectedHeaders.indexOf(o.value) > -1)
      ]
    }
  },

  watch: {
    options: {
      handler () {
        const sortCol = this.options.sortBy.join()
        const sortDesc = (this.options.sortDesc.join() === 'true')

        this.sort = `${sortDesc ? '-' : ''}${sortCol !== '' ? sortCol : ''}`

        if (sortCol === '') {
          return
        }

        this.searchMetrics()
      },
      deep: true
    },

    filters: {
      handler () {
        const sortCol = this.options.sortBy.join()
        // eslint-disable-next-line eqeqeq
        const sortDesc = this.options.sortDesc.join() == 'true'

        this.sort = `${sortDesc ? '-' : ''}${sortCol !== '' ? sortCol : ''}`

        if (sortCol === '') {
          return
        }

        this.searchMetrics()
      },
      deep: true
    }
  },

  async beforeMount () {
    this.filters.dateRange = this.getDefaultDateRange()
  },

  created () {
    this.selectedHeaders = this.tableHeaders
      .filter(o => o.value !== 'company_name')
      .map(s => s.value)
  },

  methods: {
    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
    },

    colChanged (newCols) {
      this.selectedHeaders = newCols
      const a = cloneDeep(newCols)
      const b = cloneDeep(this.areBilable)
      if (a.sort().join(',') !== b.sort().join(',')) {
        this.onlyBillable = []
      } else {
        this.onlyBillable = [true]
      }
    },

    billableChange (newVal) {
      this.onlyBillable = newVal
      this.selectedHeaders = this.tableHeaders
        .filter(o => newVal.length ? o.billable : o.value !== 'company_name')
        .map(s => s.value)
    },

    resetFilters () {
      this.filters.dateRange = this.getDefaultDateRange()
      this.filters.companyId = []
      this.onlyBillable = []
      this.selectedHeaders = this.tableHeaders
        .filter(o => o.value !== 'company_name')
        .map(s => s.value)
      this.options.sortBy = ['company_name']
      this.options.sortDesc = [false]
      this.sort = 'company_name'
    },

    getDefaultDateRange () {
      const date = new Date()
      const firstDay = new Date(date.getFullYear(), date.getMonth(), 1)
      const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0)
      return [format(firstDay, 'yyyy-MM-dd'), format(lastDay, 'yyyy-MM-dd')]
    },

    async searchMetrics () {
      this.metrics = []
      const { dateRange, companyId } = this.filters
      this.loading = true
      const [error, data] = await getAccountingMetrics({
        metric: metrics.companyDaily,
        'filter[company_id]': companyId ? companyId.join(',') : null,
        start_date: dateRange[0],
        end_date: dateRange[1],
        sort: this.sort
      })
      this.loading = false
      if (error !== undefined) {
        console.log('error')
        return
      }
      this.metrics = data.data
    }
  }
}
</script>

<style lang="scss" scoped>
.audits__list {
  height: calc(100vh - 40px);
  overflow-y: auto;
}

.v-data-table.fixed-table::v-deep {
  height: calc(100% - 64px);
  table > tbody > tr > td:nth-child(1),
  table > thead > tr > th:nth-child(1) {
    position: sticky;
    left: 0;
  }

  table > thead > tr {
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: var(--v-primary-base);
  }

  table {
    height: 100%;
    background-color: rgba(var(--v-primary-base-rgb), .1);
    thead > tr > th:not(:first-child) {
      &:hover {
        background-color: rgba(white, 0);
        color: white;
        transition: color .3s ease, background-color .3s ease;
        i {
          color: white;
        }
      }
    }
    tbody > tr {
      &:hover {
        background-color: #e9f7fc !important;
      }
      &:last-child, &:last-child > td:first-child {
        z-index: 1;
      }
      &:last-child > td {
        position: sticky;
        background-color: white !important;
        bottom: 0;
      }
    }
  }

  table > tbody > tr > td {
    background-color: rgba(white, .2);
    border-right: thin solid #d6d6d6;
    border-bottom: none !important;
    &:nth-child(1) {
      background-color: #e9f7fc;
    }
  }

  table > thead {
    background-color:var(--v-primary-base);
  }

  table > thead > tr > th {
    height: rem(72);
    background-color: rgba(white, .95);
    border-right: thin solid #d6d6d6;
    border-bottom: none !important;
    color: var(--v-dark-base);
    font-weight: 400;
    position: relative;

    & > span {
      display: inline-flex;
      padding-right: rem(22);
    }

    & > i {
      position: absolute;
      right: rem(17);
      top: 50%;
      transform: translateY(-50%) rotate(0deg);
    }

    &.asc > i {
      transform: translateY(-50%) rotate(180deg);
    }

    &.desc > i {
      transform: translateY(-50%) rotate(0deg);
    }

    &:nth-child(1) {
      background-color: var(--v-accent-lighten4);
      z-index: 1;
    }
  }
}

.th-button {
  position: absolute;
  right: 0;
  bottom: 0;
  height: 24px;
  width: 24px;
  overflow: hidden;
  &::before {
    content: '';
    position: absolute;
    height: 24px;
    width: 24px;
    left: 50%;
    top: 50%;
    background-color: white;
    transform-origin: center;
    transform: rotate(-45deg);
  }
}
</style>
