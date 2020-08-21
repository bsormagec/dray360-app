<template>
  <div
    class="orders"
  >
    <OrdersSidebar
      class="orders__sidebar"
      :active-mobile-tab="activeMobileTab"
      :change-mobile-tab="changeMobileTab"
      :toggle-mobile-sidebar="toggleMobileSidebar"
      :is-open="mobileSidebarOpen"
    />

    <div
      v-if="shouldShowTab(tabs.list)"
      :style="{ width: '100%', minWidth: '65%', display: 'flex' }"
    >
      <!-- <OrdersList
        :active-page="activePage"
        :loaded="loaded"
        :filter-query="filterQuery"
        :selected-items="statusQuery"
      /> -->
      <GeneralTable
        class="general-table"
        table-title="Order list"
        :customheaders="headers"
        :active-page="activePage"
        :custom-items="list()"
        :has-search="true"
        :has-column-filters="true"
        :has-bulk-actions="false"
        :has-action-button="{showButton: false, action: '/'}"
        injections="Orders"
        :has-add-button="{showButton: false, action: '/'}"
        v-on:searchToParent="onChildSearchUpdate"
      />
    </div>

    <OrdersCreate
      v-if="shouldShowTab(tabs.create) && hasPermission('orders-create')"
      :toggle-mobile-sidebar="toggleMobileSidebar"
    />
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import hasPermission from '@/mixins/permissions'
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import orders, { types } from '@/store/modules/orders'

import OrdersSidebar from '@/views/Orders/OrdersSidebar'
// import OrdersList from '@/views/Orders/OrdersList'
import OrdersCreate from '@/views/Orders/OrdersCreate'
import { listFormat } from '@/views/Orders/inner_utils'
import { tabs } from '@/views/Orders/inner_enums'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'

import GeneralTable from '@/components/General/GeneralTable'

export default {
  name: 'Orders',

  components: {
    OrdersSidebar,
    // OrdersList,
    OrdersCreate,
    GeneralTable
  },

  mixins: [isMobile, hasPermission],

  data: function () {
    return {
      ...mapState(orders.moduleName, {
        list: state => listFormat(state.list, (id) => this.goToOrder(id)),
        links: state => state.links,
        meta: state => state.meta
      }),
      loaded: false,
      activeMobileTab: tabs.list,
      mobileSidebarOpen: false,
      activePage: 0,
      tabs,
      statusFilter: '',
      searchFilter: {},
      filterQuery: '',
      dateQuery: '',
      statusQuery: ['intake-accepted',
        'intake-exception',
        'intake-started',
        'ocr-completed',
        'ocr-post-processing-complete',
        'ocr-post-processing-error',
        'ocr-waiting',
        'process-ocr-output-file-complete',
        'process-ocr-output-file-error',
        'upload-requested',
        'sending-to-wint',
        'success-sending-to-wint',
        'shipment-not-created-by-wint',
        'shipment-created-by-wint',
        'success-sending-to-wint'],
      headers: [
        {
          text: 'Id',
          sortable: false,
          value: 'display_id',
          width: '6rem'
        },
        {
          text: 'Status',
          value: 'latest_ocr_request_status.display_status',
          width: '12rem'
        },
        { text: 'Bill to', value: 'order.bill_to_address_raw_text', width: '30rem' },
        { text: 'Date', value: 'created_at', width: '6rem' },
        { text: 'Shipment Direction', value: 'order.shipment_direction', width: '8.5rem' },
        { text: 'Shipment Designation', value: 'order.shipment_designation', width: '8.5rem' },
        { text: 'Eq. Type', value: 'order.equipment_type', width: '8.5rem' },
        { text: 'Actions', value: 'action', width: '8.5rem' }
      ],
      menuitems: [
        {
          text: 'Dashboard',
          path: '/user/Dashboard'
        },
        {
          text: 'Manage Users'
        },
        {
          text: 'My Profile'
        },
        {
          text: 'Logout'
        }
      ]
    }
  },

  computed: {
    shouldShowSidebar () {
      if (!this.isMobile) return true
      return this.mobileSidebarOpen
    }
  },

  beforeMount () {
    if (this.searchFilter.length > 0) {
      this.statusQuery = location.search.split('=')[2]

      if (this.statusQuery !== undefined) {
        if (this.statusQuery.includes('&')) {
          this.statusQuery = this.statusQuery.split('&')[0]
        }
      }
      this.statusQuery = this.statusQuery.split(',')
    }
  },

  mounted () {
    this.fetchOrdersList()
    this.loaded = true
    console.log('this.list(): ', this.list())
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrders]),

    goToOrder (id) {
      localStorage.setItem('prevListUrl', this.$route.fullPath)
      this.$router.push(`/order/${id}`)
    },

    changeMobileTab (tab) {
      this.activeMobileTab = tab
    },

    toggleMobileSidebar () {
      this.mobileSidebarOpen = !this.mobileSidebarOpen
    },

    shouldShowTab (tab) {
      if (!this.isMobile) return true
      return this.activeMobileTab === tab
    },

    onChildSearchUpdate (value) {
      console.log('onChildSearchUpdate value: ', value)
      this.filterQuery = value
    },

    // async fetchOrdersList (filters = { page: new URLSearchParams(window.location.search).get('page') }) {
    fetchOrdersList (filters = { page: new URLSearchParams(window.location.search).get('page') }) {
      this.filterQuery = this.returnSearchQuery(this.searchFilter['filter[query]'])
      this.activePage = parseInt(this.returnPage(filters.page))
      this.dateQuery = this.returnDateQuery(this.searchFilter['filter[created_between]'])
      if (this.statusFilter.length !== 0) {
        this.statusQuery = this.statusFilterToStatusQuery(this.statusFilter)
      }
      // this.handleLocationUrl(this.activePage, this.filterQuery, this.statusQuery, this.dateQuery)
      // const status = await this[types.getOrders]({
      const status = this[types.getOrders]({
        'filter[query]': this.filterQuery,
        page: this.activePage,
        'filter[status]': this.statusQuery,
        'filter[created_between]': this.dateQuery
      })

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },

    returnPage (n) {
      const page = n || location.search.split('=')[4] || 1
      return page
    },

    returnSearchQuery (filterQuery) {
      let searchQuery = filterQuery || location.search.split('=')[3] || ''
      if (searchQuery.includes('&')) {
        searchQuery = searchQuery.split('&')[0]
      }
      return searchQuery
    },

    statusFilterToStatusQuery (filter) {
      filter = filter.split('=')[1]
      filter = filter.split(',')
      return filter
    },

    returnStatusQuery (filterQuery) {
      let statusQuery = filterQuery || location.search.split('=')[2] || ''
      if (statusQuery.includes('&')) {
        statusQuery = statusQuery.split('&')[0]
      }
      return statusQuery
    },

    returnDateQuery (query) {
      let dateQuery = query || location.search.split('=')[1] || ''
      if (dateQuery.includes('&')) {
        dateQuery = dateQuery.split('&')[0]
      }
      return dateQuery
    },

    handleLocationUrl (page, filterQuery, statusQuery, dateQuery) {
      const search = `?filter[created_between]=${dateQuery}&filter[status]=${statusQuery}&filter[query]=${filterQuery}&page=${page}`

      if (location.search !== search) {
        this.$router.replace(search)
      }
    },

    setStatusFilter (statuses) {
      if (statuses.length === 0) {
        this.statusFilter = ''
      } else {
        this.statusFilter = `filter[status]=${statuses.join(',')}`
      }
    },

    setSearchFilter (filters) {
      this.searchFilter = filters
    }
  },

  provide () {
    const {
      activePage,
      list,
      links,
      meta,
      fetchOrdersList,
      toggleMobileSidebar,
      setStatusFilter,
      setSearchFilter
    } = this

    return {
      [providerStateName]: {
        activePage: () => activePage,
        list,
        links,
        meta
      },
      [providerMethodsName]: {
        fetchOrdersList,
        toggleMobileSidebar,
        setStatusFilter,
        setSearchFilter
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.orders {
  display: flex;
  height: 100%;
}
.general-table {
  padding: 2rem 1rem;
  display: flex;
  flex-direction: column;
  flex-grow: unset;
  width: 100%;

  @media screen and (min-width: map-get($breakpoints, med)) {
    padding: 5.2rem 3.2rem;
    padding-left: map-get($sizes, sidebar-desktop-width) + 3.2rem;
  }
}
</style>
