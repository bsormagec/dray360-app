<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
      no-gutter
    >
      <v-col cols="12">
        <div class="table-root">
          <v-data-table
            :headers="[
              { text: 'ID', value: 'id' },
              { text: 'Request ID', value: 'request_id' },
              { text: 'Company', value: 'company_name' },
              { text: 'Variant ID', value: 'variant_id' },
              { text: 'Verifier', value: 'verifier'},
              { text: 'Reviewers', value: 'admin_reviewers', sortable: false},
              { text: 'Created At', value: 'created_at' },
              { text: 'Updated At', value: 'updated_at' },
              { text: 'Changes', value: 'changes_count' },
              { text: 'Client changes', value: 'client_changes_count' },
              { text: 'Reviewer changes', value: 't_companies_changes_count' },
              { text: '', sortable: false, value: 'data-table-expand' },
            ]"
            item-key="id"
            :items="orders"
            :single-expand="false"
            :options.sync="options"
            :server-items-length="meta.total"
            :header-props="{ sortIcon: 'mdi-chevron-up'}"
            hide-default-footer
            :loading="loading"
            show-expand
          >
            <template v-slot:top>
              <v-row
                class="table__tool-bar"
                align="start"
                justify="space-between"
                dense
              >
                <v-col cols="11">
                  <Filters
                    :initial-filters="filters"
                    @change="filtersChanged"
                  />
                </v-col>
                <v-col cols="auto">
                  <v-btn
                    color="primary"
                    primary
                    :loading="loading"
                    @click="searchAudits"
                  >
                    Search
                  </v-btn>
                </v-col>
              </v-row>
            </template>
            <template v-slot:[`item.id`]="{ item }">
              <router-link :to="`/order/${item.id}`">
                {{ item.id }}
              </router-link>
            </template>
            <template v-slot:[`item.changes_count`]="{ item }">
              {{ item.audits.length }}
            </template>
            <template v-slot:[`item.created_at`]="{ item }">
              {{ formatDate(item.created_at, { timeZone: false, withSeconds: true }) }}
            </template>
            <template v-slot:[`item.admin_reviewers`]="{ item }">
              {{ item.admin_reviewers.join(', ') }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
              {{ formatDate(item.updated_at, { timeZone: false, withSeconds: true }) }}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length">
                <AuditLogsTable
                  :audits="item.audits"
                  height="40vh"
                />
              </td>
            </template>
            <template v-slot:footer>
              <Pagination
                :loading="loading"
                :page-data="meta"
                :links="links"
                :default-items-per-page="itemsPerPage"
                :default-items-per-page-selected="userOptions.itemsPerPageSelected"
                @pageIndexChange="onPageChange"
                @itemsPerPageChange="onItemsPerPageChange"
              />
            </template>
          </v-data-table>
        </div>
      </v-col>
    </v-container>
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import { flatMapAudits } from '@/utils/flatmap_audits'
import { formatDate } from '@/utils/dates'
import uniq from 'lodash/uniq'

import { mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'
import { getAuditLogsDashboard } from '@/store/api_calls/utils'

import Filters from './Filters'
import AuditLogsTable from '@/components/AuditLogsTable'
import Pagination from '@/components/OrderTable/components/Pagination'

import { localStorageTypes } from '@/enums/app_objects_types'

export default {
  name: 'AuditsLog',

  components: {
    Filters,
    Pagination,
    AuditLogsTable,
  },

  mixins: [permissions],

  data: () => ({
    filters: {
      timeRange: 1,
      dateRange: [],
      companyId: null,
      variantId: null,
      userId: null,
    },
    orders: [],
    page: 1,
    itemsPerPage: ['10', '25', '50', '100'],
    meta: {},
    links: {},
    options: {
      sortBy: ['id'],
      sortDesc: [true],
    },
    sort: '-id',
    loading: false,
    userOptions: {
      itemsPerPageSelected: '10'
    }
  }),

  watch: {
    options: {
      handler () {
        const sortCol = this.options.sortBy.join()
        // eslint-disable-next-line eqeqeq
        const sortDesc = this.options.sortDesc.join() == 'true'

        this.sort = `${sortDesc ? '-' : ''}${sortCol !== '' ? sortCol : ''}`

        if ((this.filters.dateRange.length < 2 && !this.filters.timeRange) || sortCol === '') {
          return
        }

        this.searchAudits()
      },
      deep: true
    },

    userOptions: {
      handler () {
        localStorage.setItem(localStorageTypes.auditsLogsDashboardOptions, JSON.stringify(this.userOptions))
      },
      deep: true
    }
  },

  created () {
    const userOptions = localStorage.getItem(localStorageTypes.auditsLogsDashboardOptions)
    if (userOptions) {
      const options = JSON.parse(userOptions)
      this.userOptions = { ...this.userOptions, ...options }
    } else {
      const options = JSON.stringify(this.userOptions)
      localStorage.setItem(localStorageTypes.auditsLogsDashboardOptions, options)
    }
  },

  methods: {
    formatDate,

    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),

    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
      this.page = 1
    },

    async searchAudits () {
      const { timeRange, dateRange, userId, companyId, variantId } = this.filters

      if (!timeRange || (timeRange === -1 && dateRange.length !== 2)) {
        this.setSnackbar({ message: 'Please select a date range.' })
        return
      }
      this.loading = true
      const [error, data] = await getAuditLogsDashboard({
        time_range: timeRange,
        start_date: timeRange === -1 ? dateRange[0] : null,
        end_date: timeRange === -1 ? dateRange[1] : null,
        user_id: userId ? userId.join(',') : null,
        'filter[company_id]': companyId ? companyId.join(',') : null,
        'filter[variant_id]': variantId ? variantId.join(',') : null,
        page: this.page,
        sort: this.sort,
        per_page: this.userOptions.itemsPerPageSelected
      })
      this.loading = false

      if (error !== undefined) {
        this.setSnackbar({ message: 'There was an error please try again.' })
        return
      }

      const { data: orders, links, meta } = data
      this.meta = meta
      this.links = links

      this.orders = orders.map(order => {
        const audits = flatMapAudits(order.order, 'order')
          .concat(
            order.order_line_items.map(item => {
              return flatMapAudits(item.audits, `order line item #${item.id}`)
            }).filter(item => item.length > 0).flat()
          )
          .concat(
            order.order_address_events.map(item => {
              return flatMapAudits(item.audits, `order address event #${item.id}`)
            }).filter(item => item.length > 0).flat()
          )

        return { ...order.model, audits, admin_reviewers: this.getAdminReviewers(order.model) }
      })
    },

    getAdminReviewers (order) {
      const reviewers = order.audits
        .filter(audit => audit.user.t_company_id === 2)
        .map(audit => audit.user.name)
      const related = ['order_line_items', 'order_address_events']
      for (let index = 0; index < related.length; index++) {
        order[related[index]].forEach(related => {
          related.audits
            .filter(audit => audit.user.t_company_id === 2)
            .forEach(audit => reviewers.push(audit.user.name))
        })
      }

      return uniq(reviewers)
    },

    onPageChange (newPage) {
      this.page = newPage
      this.searchAudits()
    },

    onItemsPerPageChange (newValue) {
      this.userOptions.itemsPerPageSelected = newValue
      this.searchAudits()
    }
  }
}
</script>

<style lang="scss" scoped>
.wrapper {
  height: calc(100vh - 40px);
  overflow-y: auto;
}
.audits__list {
  height: 100%;
  padding: rem(14) rem(28) 0 rem(28);
}
.table-root::v-deep {
  .table__tool-bar {
    position: sticky;
    top: 0;
    background-color: #FFFFFF;
    z-index: 1;
  }
  table > thead > tr > th {
    position: relative;
    & > span {
      display: inline-block;
      width: 100%;
      padding-right: rem(18);
    }
    & > .v-icon.v-icon {
      position: absolute;
      top: 50%;
      right: 0;
      transform: translateY(-50%) rotate(0deg);
    }
    &.asc > .v-icon.v-icon {
      transform: translateY(-50%)rotate(180deg);
    }
    &.desc > .v-icon.v-icon {
      transform: translateY(-50%) rotate(0deg);
    }
  }
}
</style>
