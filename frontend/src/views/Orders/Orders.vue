<template>
  <div
    class="orders"
    data-testid="test-orders"
  >
    <Sidebar />

    <OrdersList v-if="meta().last_page" />

    <div class="orders__create">
      create
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import orders, { types } from '@/store/modules/orders'

import Sidebar from '@/components/Sidebar'
import OrdersList from '@/views/Orders/OrdersList'
import { listFormat } from '@/views/Orders/inner_utils'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'Orders',

  components: {
    Sidebar,
    OrdersList
  },

  data: () => ({
    ...mapState(orders.moduleName, {
      list: state => listFormat(state.list),
      links: state => state.links,
      meta: state => state.meta
    })
  }),

  async mounted () {
    await this.fetchOrdersList()
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrders]),

    async fetchOrdersList (n) {
      const status = await this[types.getOrders](n)

      if (status === reqStatus.success) return console.log('success')
      console.log('error')
    }
  },

  provide () {
    const { list, links, meta, fetchOrdersList } = this

    return {
      [providerStateName]: {
        list,
        links,
        meta
      },
      [providerMethodsName]: {
        fetchOrdersList
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

.orders__list {
  padding: 5.2rem 7.5rem;
  padding-bottom: 3rem;
  flex-grow: 1;
}

.orders__create {
  width: 27%;
  padding: 5.2rem 3.6rem;
  padding-bottom: 3rem;
  box-shadow: map-get($properties, inset-shadow-left);
  border-left: 0.1rem solid map-get($colors, grey-2);
}
</style>
