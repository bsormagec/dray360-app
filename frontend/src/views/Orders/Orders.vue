<template>
  <div class="orders">
    <Sidebar />

    <OrdersList />

    <div class="orders__create">
      create
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar'
import OrdersList from '@/views/Orders/OrdersList'

import { getOrders } from '@/store/api_calls/orders.js'

export default {
  name: 'Orders',

  components: {
    Sidebar,
    OrdersList
  },

  async mounted () {
    const res = await getOrders()
    console.log(res)
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
