<template>
  <div class="wrapper">
    <v-container
      fluid
      pa-0
    >
      <div class="row no-gutters">
        <div class="col-12 audits__list">
          <SidebarNavigationButton />
          <Filters
            :initial-filters="filters"
            @change="filtersChanged"
          />
          <v-row>
            <v-col cols="1 d-flex align-center">
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
          <div class="table-root">
            <v-data-table
              :headers="[
                {
                  text: 'ID', value: 'id',
                },
                { text: 'Request ID', value: 'request_id' },
                { text: 'Company', value: 'company.name' },
                { text: 'Variant Name', value: 'variant_name' },
                { text: 'First Updated', value: 'created_at' },
                { text: 'Last Updated', value: 'updated_at' },
                { text: 'Changes Count', value: 'changes_count' },
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
              <template v-slot:[`item.changes_count`]="{ item }">
                {{ item.audits.length }}
              </template>
              <template v-slot:[`item.created_at`]="{ item }">
                {{ formatDate(item.created_at, { timeZone: true, withSeconds: true }) }}
              </template>
              <template v-slot:[`item.updated_at`]="{ item }">
                {{ formatDate(item.updated_at, { timeZone: true, withSeconds: true }) }}
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
                  @pageIndexChange="onPageChange"
                />
              </template>
            </v-data-table>
          </div>
        </div>
      </div>
    </v-container>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import permissions from '@/mixins/permissions'
import { flatMapAudits } from '@/utils/flatmap_audits'
import { formatDate } from '@/utils/dates'

import { mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'
import { getAuditLogsDashboard } from '@/store/api_calls/utils'

import Filters from './Filters'
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton'
import AuditLogsTable from '@/components/AuditLogsTable'
import Pagination from '@/components/OrderTable/components/Pagination'

export default {
  name: 'AuditsLog',

  components: {
    Filters,
    Pagination,
    AuditLogsTable,
    SidebarNavigationButton,
  },

  mixins: [isMobile, permissions],

  data: () => ({
    filters: {
      timeRange: null,
      dateRange: [],
      companyId: null,
      variantName: null,
      userId: null,
    },
    orders: [],
    page: 1,
    meta: {},
    links: {},
    options: {
      sortBy: ['id'],
      sortDesc: [true],
    },
    sort: '-id',
    loading: false,
  }),

  watch: {
    isMobile: function (newVal, oldVal) {
      if (newVal) {
        this.setSidebar({ show: false })
      } else {
        this.setSidebar({ show: true })
      }
    },
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
  },

  async beforeMount () {
    if (!this.isMobile) {
      return this.setSidebar({ show: true })
    }

    return this.setSidebar({ show: false })
  },

  methods: {
    formatDate,

    ...mapActions(utils.moduleName, [actionTypes.setSnackbar, actionTypes.setSidebar]),

    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
      this.page = 1
    },
    async searchAudits () {
      const { timeRange, dateRange, userId, companyId, variantName } = this.filters

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
        'filter[variant_name]': variantName ? variantName.join(',') : null,
        page: this.page,
        sort: this.sort,
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

        return { ...order.model, audits }
      })
    },
    onPageChange (newPage) {
      this.page = newPage
      this.searchAudits()
    }
  }
}
</script>

<style lang="scss" scoped>
.audits__list {
  height: 100vh;
  overflow-y: auto;
  padding: rem(14) rem(28) 0 rem(28);
}
</style>
