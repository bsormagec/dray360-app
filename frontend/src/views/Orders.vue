<template>
  <div class="orders">
    <Sidebar />

    <div class="orders__center">
      center
    </div>

    <div class="orders__right">
      right
    </div>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar'

export default {
  name: 'Orders',

  components: {
    Sidebar
  }
}
</script>

<style lang="scss" scoped>

.orders {
  display: flex;
  height: 100%;
}

.orders__center {
  padding: 5.2rem 7.5rem;
  padding-bottom: 3rem;
  flex-grow: 1;
}

.orders__right {
  width: 27%;
  padding: 5.2rem 3.6rem;
  padding-bottom: 3rem;
  box-shadow: map-get($properties, inset-shadow-left);
  border-left: 0.1rem solid map-get($colors, grey-2);
}
</style>
