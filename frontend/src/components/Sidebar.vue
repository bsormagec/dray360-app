<template>
  <div class="sidebar">
    <div class="sidebar__background">
      <div class="sidebar__logo" />

      <div class="sidebar__mobile-options">
        <v-btn
          color="primary"
          :outlined="activeMobileTab !== tabs.list"
          :style="{marginBottom: '1rem'}"
          @click="changeMobileTab(tabs.list)"
        >
          orders list
        </v-btn>
        <v-btn
          color="primary"
          :outlined="activeMobileTab !== tabs.create"
          @click="changeMobileTab(tabs.create)"
        >
          create order by upload
        </v-btn>
      </div>

      <div class="sidebar__footer" />
    </div>

    <div
      class="sidebar__backdrop"
      @click="toggleMobileSidebar"
    />
  </div>
</template>

<script>
import { tabs } from '@/views/Orders/inner_enums'

export default {
  name: 'Sidebar',

  props: {
    activeMobileTab: {
      type: String,
      required: true
    },
    changeMobileTab: {
      type: Function,
      required: true
    },
    toggleMobileSidebar: {
      type: Function,
      required: true
    }
  },

  data: () => ({
    tabs
  })
}
</script>

<style lang="scss" scoped>
$sidebar-width: 13.5%;
$cushing-logo: url("../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../assets/images/ordermaster_logo.svg");

.sidebar {
  display: flex;
  position: fixed;
  z-index: 100;
  height: 100vh;
  width: 100%;
}

.sidebar__background {
  width: 52%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.sidebar__backdrop {
  flex-grow: 1;
  background: rgba(0, 0, 0, 0.4)
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

.sidebar__mobile-options {
  display: flex;
  flex-direction: column;
  margin-top: 3.8rem;
  margin-bottom: auto;
}
</style>
