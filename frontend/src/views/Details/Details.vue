<template>
  <div
    class="details"
  >
    <DetailsSidebar />
  </div>
</template>

<script>
import DetailsSidebar from '@/views/Details/DetailsSidebar'

export default {
  name: 'Details',

  components: {
    DetailsSidebar
  }
}
</script>

<style lang="scss" scoped>
.details {
  width: 100%;
  height: 100%;
  display: flex;
}
</style>
