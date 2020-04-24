<template>
  <div
    class="sidebar"
  >
    <div
      v-if="!isMobile"
      class="sidebar__logo"
    />

    <div class="sidebar__body">
      <v-btn
        color="primary"
        outlined
        width="11.5rem"
        @click="() => {}"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
        Order List
      </v-btn>

      <DetailsSidebarNavigation />

      <v-btn
        :color="isEditing ? 'success' : 'primary'"
        :outlined="!isEditing"
        :style="{ marginBottom: '1rem' }"
        width="11.5rem"
        @click="toggleIsEditing"
      >
        {{ isEditing ? 'Save' : 'Edit Order' }}
      </v-btn>

      <v-btn
        color="primary"
        outlined
        width="11.5rem"
        @click="() => {}"
      >
        Send to   TMS
      </v-btn>
    </div>

    <div
      v-if="!isMobile"
      class="sidebar__footer"
    />
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import { detailsState, detailsMethods } from '@/views/Details/inner_store'
import DetailsSidebarNavigation from '@/views/Details/DetailsSidebarNavigation'

export default {
  name: 'DetailsSidebar',

  components: {
    DetailsSidebarNavigation
  },

  mixins: [isMobile],

  computed: {
    isEditing () {
      return detailsState.isEditing
    }
  },

  methods: {
    toggleIsEditing () {
      detailsMethods.toggleIsEditing()
    }
  }
}
</script>

<style lang="scss" scoped>
$cushing-logo: url("../../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../../assets/images/ordermaster_logo.svg");

.sidebar {
  overflow-y: auto;
  z-index: 1;
  display: flex;
  position: fixed;
  left: 0;
  width: map-get($sizes, sidebar-desktop-width);
  height: 100vh;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.sidebar__logo {
  width: 14rem;
  min-height: 4.3rem;
  height: 4.3rem;
  background-image: $cushing-logo;
  background-size: 14rem 4.3rem;
  background-position: center center;
}

.sidebar__body {
  margin-top: 6rem;
  margin-bottom: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.sidebar__footer {
  width: 11.1rem;
  height: 4.5rem;
  min-height: 4.5rem;
  margin-top: 6rem;
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
}
</style>
