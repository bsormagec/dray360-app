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
          class="fixed-table"
          :headers="tableHeaders"
          :items="metrics"
          :items-per-page="metrics.length"
          :hide-default-footer="true"
          :loading="loading"
          height="100%"
          loading-text="Loading... Please wait"
        >
          <template v-slot:top>
            <Filters
              :initial-filters="filters"
              :hidden-cols="hiddenCols"
              @change="filtersChanged"
              @colChange="colChanged"
            />
          </template>
          <template
            v-for="item in hasFormula"
            v-slot:[`header.${item}`]="{ header }"
          >
            {{ header.text }}
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
                  <span class="text-body-1">
                    Formula
                  </span>
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

import isMobile from '@/mixins/is_mobile'
import permissions from '@/mixins/permissions'

import { mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'

import Filters from './components/Filters'

import { getAccountingMetrics } from '@/store/api_calls/accounting_metrics'
import { metrics } from '@/enums/app_objects_types'
import { metricsLabels } from './enums/metrics_labels'

export default {
  name: 'AccountingDashboard',

  components: {
    Filters
  },

  mixins: [permissions, isMobile],

  data: () => ({
    filters: {
      dateRange: [],
      companyId: [],
    },
    hiddenCols: [],
    loading: false,
    metrics: [],
  }),

  computed: {
    tableHeaders () {
      return Object.keys(metricsLabels).map(key => ({
        text: metricsLabels[key].name,
        align: 'start',
        sortable: false,
        value: key,
        width: '220px',
        formula: metricsLabels[key]?.formula ?? null
      }))
    },

    areBilables () {
      const arrayToReturn = []
      Object.keys(metricsLabels).map((key) => {
        if (metricsLabels[key]?.bilable) {
          arrayToReturn.push(key)
        }
      })
      return arrayToReturn
    },

    hasFormula () {
      const arrayToReturn = []
      Object.keys(metricsLabels).map((key) => {
        if (metricsLabels[key]?.formula) {
          arrayToReturn.push(key)
        }
      })
      return arrayToReturn
    },

    colsTotal () {
      const obj = cloneDeep(this.metrics)
      const arrayToReturn = []
      Object.keys(metricsLabels).map(key => {
        if (key !== 'company_name') {
          arrayToReturn.push(obj.reduce((t, cur) => t + Number(cur[key]), 0))
        }
      })
      return arrayToReturn
    }
  },

  watch: {
    isMobile: function (newVal, oldVal) {
      if (newVal) {
        this.setSidebar({ show: false })
      } else {
        this.setSidebar({ show: true })
      }
    },

    filters: {
      handler () {
        this.searchMetrics()
      },
      deep: true
    }
  },

  async beforeMount () {
    this.filters.dateRange = this.getDefaultDateRange()

    if (!this.isMobile) {
      return this.setSidebar({ show: true })
    }

    return this.setSidebar({ show: false })
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSidebar]),

    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
    },

    colChanged (newCols) {
      this.hiddenCols = newCols
    },

    getDefaultDateRange () {
      const date = new Date()
      const firstDay = new Date(date.getFullYear(), date.getMonth(), 1)
      const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0)
      return [format(firstDay, 'yyyy-MM-dd'), format(lastDay, 'yyyy-MM-dd')]
    },

    async searchMetrics () {
      const { dateRange, companyId } = this.filters
      this.loading = true
      const [error, data] = await getAccountingMetrics({
        metric: metrics.companyDaily,
        'filter[company_id]': companyId ? companyId.join(',') : null,
        start_date: dateRange[0],
        end_date: dateRange[1]
      })
      this.loading = false
      if (error !== undefined) {
        console.log('error')
        return
      }
      this.metrics = data.data
      return { ...data }
    }
  }
}
</script>

<style lang="scss" scoped>
.audits__list {
  height: 100vh;
  overflow-y: auto;
}

.v-data-table.fixed-table::v-deep {
  height: calc(100% - 64px);
  table > tbody > tr > td:nth-child(1),
  table > thead > tr > th:nth-child(1) {
    position: sticky;
    left: 0;
  }

  table {
    height: 100%;
    background-color: rgba(var(--v-primary-base-rgb), .1);
    thead > tr > th:not(:first-child) {
      &:hover {
        background-color: rgba(white, 0);
        color: white;
        transition: color .3s ease, background-color .3s ease;
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
    padding-top: rem(32);
    padding-bottom: rem(16);
    background-color: rgba(white, .95);
    border-right: thin solid #d6d6d6;
    border-bottom: none !important;
    color: var(--v-dark-base);
    font-weight: 400;
    position: relative;

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
