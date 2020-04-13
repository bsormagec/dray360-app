<template>
  <div
    class="sidebar"
  >
    <div class="sidebar__logo" />

    <div class="sidebar__body">
      <v-btn
        color="primary"
        outlined
        @click="() => {}"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
        Order List
      </v-btn>

      <DetailsSidebarNavigation />

      <v-btn
        color="primary"
        outlined
        :style="{ marginBottom: '1rem' }"
        @click="() => {}"
      >
        Edit Order
      </v-btn>

      <v-btn
        color="primary"
        outlined
        @click="() => {}"
      >
        Send to   TMS
      </v-btn>
    </div>

    <div class="sidebar__footer" />
  </div>
</template>

<script>
import DetailsSidebarNavigation from '@/views/Details/DetailsSidebarNavigation'

export default {
  name: 'DetailsSidebar',

  components: {
    DetailsSidebarNavigation
  }
}
</script>

<style lang="scss" scoped>
$cushing-logo: url("../../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../../assets/images/ordermaster_logo.svg");

.sidebar {
  z-index: 1;
  display: flex;
  position: fixed;
  left: 0;
  width: map-get($sizes, sidebar-desktop-width);
  height: 100%;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.sidebar__logo {
  width: 72%;
  max-width: 14rem;
  height: 4.3rem;
  background-image: $cushing-logo;
  background-size: contain;
  background-position: center center;
}

.sidebar__footer {
  width: 57%;
  max-width: 11.1rem;
  height: 4.5rem;
  margin-top: auto;
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
}

.sidebar__body {
  margin-top: 6rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
