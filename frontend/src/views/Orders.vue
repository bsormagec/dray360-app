<template>
  <div class="orders">
    <Sidebar />
    <p>hello</p>
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
</style>
