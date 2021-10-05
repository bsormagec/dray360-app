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
            :header-props="{ sortIcon: 'mdi-chevron-up' }"
            :headers="displayCols"
            :items="userFeedbacks"
            :options.sync="options"
            :loading="loading"
            :server-items-length="meta.total"
            hide-default-footer
          >
            <template v-slot:top>
              <Filters
                :initial-filters="filters"
                :selected-headers="selectedHeaders"
                @change="filtersChanged"
                @colChange="colChanged"
                @resetFilters="resetFilters"
              />
            </template>
            <template v-slot:[`item.commentable.request_id`]="{ item }">
              <router-link
                v-if="item.commentable.deleted_at === null"
                :to="`/inbox?selected=${item.commentable.request_id}`"
              >
                {{ item.commentable.request_id }}
              </router-link>
              <span
                v-else
                class="grey--text text-decoration-none"
                :title="item.commentable.deleted_at !== null && `Deleted at: ${formatDate(item.commentable.deleted_at, { timezone: false, withSeconds: true })}`"
              >
                {{ item.commentable.request_id }}
              </span>
            </template>
            <template v-slot:[`item.commentable.id`]="{ item }">
              <span v-if="item.commentable_type === 'App\\Models\\Order'">
                <router-link
                  v-if="item.commentable.deleted_at === null"
                  :to="`/order/${item.commentable.id}`"
                >
                  {{ item.commentable.id }}
                </router-link>
                <span
                  v-else
                  class="grey--text text-decoration-none"
                  :title="item.commentable.deleted_at !== null && `Deleted at: ${formatDate(item.commentable.deleted_at, { timezone: false, withSeconds: true })}`"
                >
                  {{ item.commentable.id }}
                </span>
              </span>
            </template>
            <template v-slot:[`item.created_at`]="{ item }">
              {{ formatDate(item.created_at, { timeZone: false, withSeconds: true }) }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
              {{ formatDate(item.updated_at, { timeZone: false, withSeconds: true }) }}
            </template>
            <template v-slot:[`item.comment`]="{ item }">
              <StringCollapse :string="item.comment" />
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
import { getFeedback } from '@/store/api_calls/feedbacks'
import utils, { actionTypes } from '@/store/modules/utils'
import { formatDate } from '@/utils/dates'
import format from 'date-fns/format'
import subDays from 'date-fns/subDays'

import { localStorageTypes } from '@/enums/app_objects_types'
import { commentsLabels } from './enums/comments_labels'

import Filters from './components/Filters'
import StringCollapse from './components/StringCollapse'
import Pagination from '@/components/OrderTable/components/Pagination'

export default {
  name: 'OrderCommentsDashboard',

  components: {
    Filters,
    Pagination,
    StringCollapse
  },

  data: () => ({
    filters: {
      companyId: [],
      dateRange: [],
      userId: [],
      roleId: [],
      comment: ''
    },
    selectedHeaders: [],
    options: {
      sortBy: ['updated_at'],
      sortDesc: [false]
    },
    userFeedbacks: [],
    loading: false,
    sort: 'updated_at',
    page: 1,
    itemsPerPage: ['10', '25', '50', '100'],
    meta: {},
    links: {},
    userOptions: {
      itemsPerPageSelected: '10'
    }
  }),

  computed: {
    tableHeaders () {
      return commentsLabels
    },

    displayCols () {
      return this.tableHeaders.filter(o => this.selectedHeaders.indexOf(o.value) > -1)
    }
  },

  watch: {
    userOptions: {
      handler () {
        localStorage.setItem(localStorageTypes.commentsLogsOptions, JSON.stringify(this.userOptions))
      },
      deep: true
    },

    options: {
      handler () {
        const sortCol = () => {
          const name = this.options.sortBy.join()
          if (name === 'commentable.id') return 'order_id'
          if (name === 'company_name') return 'company'
          if (name === 'user.name') return 'user'
          return name
        }
        // eslint-disable-next-line eqeqeq
        const sortDesc = this.options.sortDesc.join() == 'true'
        this.sort = `${sortDesc ? '-' : ''}${sortCol() !== '' ? sortCol() : ''}`
        if (sortCol() === '') return
        this.searchUserFeedbacks()
      },
      deep: true
    }
  },

  beforeMount () {
    this.filters.dateRange = this.getDefaultDateRange()
  },

  created () {
    this.selectedHeaders = this.tableHeaders.map(s => s.value)
    const userOptions = localStorage.getItem(localStorageTypes.commentsLogsOptions)
    if (userOptions) {
      const options = JSON.parse(userOptions)
      this.userOptions = { ...this.userOptions, ...options }
    } else {
      const options = JSON.stringify(this.userOptions)
      localStorage.setItem(localStorageTypes.commentsLogsOptions, options)
    }
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),

    formatDate,

    filtersChanged (newFilters) {
      this.filters = { ...newFilters }
      this.searchUserFeedbacks()
    },

    colChanged (newCols) {
      this.selectedHeaders = newCols
    },

    resetFilters () {
      this.filters.dateRange = this.getDefaultDateRange()
      this.filters.companyId = []
      this.filters.comment = ''
      this.selectedHeaders = this.tableHeaders.map(s => s.value)
      this.searchUserFeedbacks()
    },

    getDefaultDateRange () {
      const date = new Date()
      return [
        format(subDays(date, 6), 'yyyy-MM-dd'),
        format(date, 'yyyy-MM-dd')
      ]
    },

    async searchUserFeedbacks () {
      this.userFeedbacks = []
      const {
        companyId,
        userId,
        dateRange,
        roleId,
        comment
      } = this.filters
      this.loading = true
      const [error, data] = await getFeedback({
        'filter[company_id]': companyId ? companyId.join(',') : null,
        'filter[user_id]': userId ? userId.join(',') : null,
        'filter[start_date]': dateRange[0],
        'filter[end_date]': dateRange[1],
        'filter[role]': roleId ? roleId.join(',') : null,
        'filter[comment]': comment !== '' ? comment : null,
        page: this.page,
        sort: this.sort,
        per_page: this.userOptions.itemsPerPageSelected
      })
      this.loading = false
      if (error !== undefined) {
        this.setSnackbar({ message: 'There was an error please try again.' })
        return
      }
      const { data: userFeedbacks, links, meta } = data
      this.userFeedbacks = userFeedbacks
      this.links = links
      this.meta = meta
    },

    onPageChange (newPage) {
      this.page = newPage
      this.searchUserFeedbacks()
    },

    onItemsPerPageChange (newValue) {
      this.userOptions.itemsPerPageSelected = newValue
      this.searchUserFeedbacks()
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
</style>
