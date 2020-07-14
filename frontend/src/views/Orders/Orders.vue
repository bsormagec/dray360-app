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
      <OrdersList
        :active-page="activePage"
        :loaded="loaded"
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
import OrdersList from '@/views/Orders/OrdersList'
import OrdersCreate from '@/views/Orders/OrdersCreate'
import { listFormat } from '@/views/Orders/inner_utils'
import { tabs } from '@/views/Orders/inner_enums'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'Orders',

  components: {
    OrdersSidebar,
    OrdersList,
    OrdersCreate
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
      searchFilter: {}
    }
  },

  computed: {
    shouldShowSidebar () {
      if (!this.isMobile) return true
      return this.mobileSidebarOpen
    }
  },

  async mounted () {
    await this.fetchOrdersList()
    this.loaded = true
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

    async fetchOrdersList (filters = { page: new URLSearchParams(window.location.search).get('page') }) {
      this.activePage = parseInt(this.handleLocationUrl(filters.page))
      const status = await this[types.getOrders]({
        ...this.searchFilter,
        ...filters,
        page: this.activePage,
        query: this.statusFilter
      })

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },

    handleLocationUrl (n) {
      const page = n || location.search.split('=')[1] || 1
      const search = `?page=${page}`

      if (location.search !== search) {
        this.$router.replace(search)
      }

      return page
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
</style>
