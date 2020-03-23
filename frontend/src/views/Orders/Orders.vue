<template>
  <div class="orders">
    <Sidebar />

    <OrdersList
      :items="list"
      :links="links"
      :meta="meta"
    />

    <div class="orders__create">
      create
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import orders, { types } from '@/store/modules/orders'

import Sidebar from '@/components/Sidebar'
import OrdersList from '@/views/Orders/OrdersList'
import { listFormat } from '@/views/Orders/inner_utils'

export default {
  name: 'Orders',

  components: {
    Sidebar,
    OrdersList
  },

  computed: {
    ...mapState(orders.moduleName, {
      list: state => listFormat(state.list),
      links: state => state.links,
      meta: state => state.meta
    })
  },

  async mounted () {
    await this.fetchOrdersList()
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrders]),

    async fetchOrdersList () {
      const status = await this[types.getOrders]()

      if (status === reqStatus.success) return console.log('success')
      console.log('error')
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
