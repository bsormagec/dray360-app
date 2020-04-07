<template>
  <div
    class="orders"
  >
    <Sidebar
      v-if="shouldShowSidebar"
      class="orders__sidebar"
      :active-mobile-tab="activeMobileTab"
      :change-mobile-tab="changeMobileTab"
      :toggle-mobile-sidebar="toggleMobileSidebar"
    />

    <OrdersList v-if="meta().last_page && shouldShowTab(tabs.list)" />

    <OrdersCreate v-if="shouldShowTab(tabs.create)" />
  </div>
</template>

<script>
import isMobile from '@/utils/is_mobile'
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import orders, { types } from '@/store/modules/orders'

import Sidebar from '@/components/Sidebar'
import OrdersList from '@/views/Orders/OrdersList'
import OrdersCreate from '@/views/Orders/OrdersCreate'
import { listFormat } from '@/views/Orders/inner_utils'
import { tabs } from '@/views/Orders/inner_enums'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'Orders',

  components: {
    Sidebar,
    OrdersList,
    OrdersCreate
  },

  data: () => ({
    ...mapState(orders.moduleName, {
      list: state => listFormat(state.list),
      links: state => state.links,
      meta: state => state.meta
    }),
    activeMobileTab: tabs.list,
    mobileSidebarOpen: false,
    tabs
  }),

  computed: {
    shouldShowSidebar () {
      if (!isMobile()) return true
      return this.mobileSidebarOpen
    }
  },

  async mounted () {
    await this.fetchOrdersList()
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrders]),

    changeMobileTab (tab) {
      this.activeMobileTab = tab
    },

    toggleMobileSidebar () {
      this.mobileSidebarOpen = !this.mobileSidebarOpen
    },

    shouldShowTab (tab) {
      if (!isMobile()) return true
      return this.activeMobileTab === tab
    },

    async fetchOrdersList (n) {
      const status = await this[types.getOrders](n)

      if (status === reqStatus.success) return console.log('success')
      console.log('error')
    }
  },

  provide () {
    const { list, links, meta, fetchOrdersList, toggleMobileSidebar } = this

    return {
      [providerStateName]: {
        list,
        links,
        meta
      },
      [providerMethodsName]: {
        fetchOrdersList,
        toggleMobileSidebar
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.orders {
  display: flex;
  height: 100%;

  @media screen and (max-width: 1200px) {
    flex-wrap: wrap;
  }
}
</style>
