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
            :header-props="{ sortIcon: 'mdi-chevron-down' }"
            :headers="displayCols"
            :items="items"
            :options.sync="options"
            :loading="loading"
            :server-items-length="meta.total"
            hide-default-footer
            dense
          >
            <template v-slot:top>
              <Filters
                :headers="headers"
                :initial-filters="{
                  property: '',
                  companyId: [],
                  dateRange: defaultDateRange(),
                  userId: [],
                  variantId: [],
                }"
                :selected-headers="selectedHeaders"
                @change="filtersChanged"
                @colChange="colChanged"
                @reset-filters="resetFilters"
              />
            </template>
            <template v-slot:item="{ item }">
              <tr>
                <td
                  v-if="showCell('property_name')"
                  class="text-start"
                >
                  {{ item.property_name }}
                </td>
                <td
                  v-if="showCell('old_value')"
                  class="wrap-overflow old"
                >
                  <pre>{{ item.old_value !== 'null' ? item.old_value : '' }}</pre>
                </td>
                <td
                  v-if="showCell('new_value')"
                  class="wrap-overflow new"
                >
                  <pre>{{ item.new_value !== 'null' ? item.new_value : '' }}</pre>
                </td>
                <td
                  v-if="showCell('company_name')"
                  class="text-start"
                >
                  {{ item.company_name }}
                </td>
                <td
                  v-if="showCell('request_id')"
                  class="text-start"
                >
                  <router-link :to="`/inbox?selected=${item.request_id}&displayHidden=true`">
                    {{ item.request_id }}
                  </router-link>
                </td>
                <td
                  v-if="showCell('order_id')"
                  class="text-start"
                >
                  <router-link :to="`/order/${item.order_id}`">
                    {{ item.order_id }}
                  </router-link>
                </td>
                <td
                  v-if="showCell('order_date')"
                  class="text-start"
                >
                  {{ item.order_date }}
                </td>
                <td
                  v-if="showCell('verifier')"
                  class="text-start"
                >
                  {{ item.verifier }}
                </td>
                <td
                  v-if="showCell('variant_id')"
                  class="text-start"
                >
                  {{ item.variant_id }}
                </td>
                <td
                  v-if="showCell('edit_date')"
                  class="text-start"
                >
                  {{ item.edit_date }}
                </td>
                <td
                  v-if="showCell('user.name')"
                  class="text-start"
                >
                  {{ item.user.name }}
                </td>
              </tr>
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
import { mapActions } from 'vuex'
import { getOrderPropertiesAuditLogs } from '@/store/api_calls/utils'
import utils, { actionTypes } from '@/store/modules/utils'
import { formatDate } from '@/utils/dates'
import format from 'date-fns/format'
import subDays from 'date-fns/subDays'

import { localStorageTypes } from '@/enums/app_objects_types'

import Filters from './components/Filters'
import Pagination from '@/components/OrderTable/components/Pagination'

export default {
  name: 'PropertyLogs',

  components: {
    Filters,
    Pagination,
  },

  data: (vm) => ({
    headers: [
      { text: 'Property', value: 'property_name', sortable: false },
      { text: 'Old', value: 'old_value' },
      { text: 'New', value: 'new_value' },
      { text: 'Company', value: 'company_name' },
      { text: 'Request Id', value: 'request_id' },
      { text: 'Order Id', value: 'order_id' },
      { text: 'order_date', value: 'order_date' },
      { text: 'Verifier', value: 'verifier', sortable: false },
      { text: 'Variant Id', value: 'variant_id' },
      // { text: 'variant_name', value: 'variant_name' },
      // { text: 'audit_id', value: 'audit_id' },
      { text: 'Edit Date', value: 'edit_date' },
      { text: 'Editor', value: 'user.name', sortable: false },
    ],
    filters: {
      property: '',
      companyId: [],
      dateRange: vm.defaultDateRange(),
      userId: [],
      variantId: [],
    },
    selectedHeaders: [],
    options: {},
    items: [],
    loading: false,
    sort: '',
    page: 1,
    itemsPerPage: ['10', '25', '50', '100'],
    meta: {},
    links: {},
    userOptions: {
      itemsPerPageSelected: '10'
    }
  }),

  computed: {
    displayCols () {
      return this.headers.filter(o => this.selectedHeaders.indexOf(o.value) > -1)
    }
  },

  watch: {
    userOptions: {
      handler () {
        localStorage.setItem(localStorageTypes.propertyLogsOptions, JSON.stringify(this.userOptions))
      },
      deep: true
    },

    options: {
      handler () {
        const sortCol = this.options.sortBy.join()
        // eslint-disable-next-line eqeqeq
        const sortDesc = this.options.sortDesc.join() == 'true'
        this.sort = `${sortDesc ? '-' : ''}${sortCol !== '' ? sortCol : ''}`
        if (sortCol === '') return
        this.fetchPropertyLogs()
      },
      deep: true
    }
  },

  created () {
    this.selectedHeaders = this.headers.map(s => s.value)
    const userOptions = localStorage.getItem(localStorageTypes.propertyLogsOptions)
    if (userOptions) {
      const options = JSON.parse(userOptions)
      this.userOptions = { ...this.userOptions, ...options }
    } else {
      const options = JSON.stringify(this.userOptions)
      localStorage.setItem(localStorageTypes.propertyLogsOptions, options)
    }
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),

    formatDate,

    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
      this.page = 1
      this.fetchPropertyLogs()
    },

    colChanged (newCols) {
      this.selectedHeaders = newCols
    },

    showCell (columnName) {
      return this.displayCols.filter(col => col.value === columnName).length > 0
    },

    resetFilters () {
      this.filters = {
        property: '',
        companyId: [],
        dateRange: this.defaultDateRange(),
        userId: [],
        variantId: [],
      }
      this.page = 1
      this.selectedHeaders = this.headers.map(s => s.value)
      this.fetchPropertyLogs()
    },

    defaultDateRange () {
      const date = new Date()
      return [
        format(subDays(date, 6), 'yyyy-MM-dd'),
        format(date, 'yyyy-MM-dd')
      ]
    },

    async fetchPropertyLogs () {
      this.items = []
      const {
        property,
        companyId,
        dateRange,
        userId,
        variantId
      } = this.filters
      this.loading = true

      if (!property || property === '') {
        this.loading = false
        this.items = []
        return
      }

      const [error, data] = await getOrderPropertiesAuditLogs({
        'filter[company_id]': companyId ? companyId.join(',') : null,
        'filter[user_id]': userId ? userId.join(',') : null,
        'filter[variant_id]': variantId ? variantId.join(',') : null,
        start_date: dateRange[0],
        end_date: dateRange[1],
        property,
        page: this.page,
        sort: this.sort,
        per_page: this.userOptions.itemsPerPageSelected
      })
      this.loading = false
      if (error !== undefined) {
        this.setSnackbar({ message: 'There was an error please try again.' })
        return
      }
      const { data: items, links, meta } = data
      this.items = items
      this.links = links
      this.meta = meta
    },

    onPageChange (newPage) {
      this.page = newPage
      this.fetchPropertyLogs()
    },

    onItemsPerPageChange (newValue) {
      this.userOptions.itemsPerPageSelected = newValue
      this.fetchPropertyLogs()
    }
  }
}
</script>

<style lang="scss" scoped>
.wrapper {
  height: calc(100vh - 40px);
  overflow-y: auto;
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

.wrap-overflow {
  max-width: rem(160);

  &.old {
    background-color: var(--v-error-lighten4);
  }
  &.new {
    background-color: var(--v-success-lighten4);
  }

  pre {
    white-space: normal;
    line-break: anywhere;
    font-size: rem(12);
  }
}
</style>
