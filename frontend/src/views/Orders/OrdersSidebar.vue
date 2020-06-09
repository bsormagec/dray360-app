<template>
  <div
    :class="{sidebar: true, desktop: !isMobile}"
  >
    <div
      v-if="!isMobile"
      class="sidebar__logo"
    />

    <div
      v-if="!isMobile"
      class="sidebar__footer"
    >
      <v-btn
        color="primary"
        outlined
        width="11.5rem"
        class="logout__btn"
        @click="logoutBtn"
      >
        Logout
      </v-btn>
    </div>

    <transition name="slide-fade">
      <div
        v-if="isOpen && isMobile"
        class="sidebar__background"
      >
        <div class="sidebar__close">
          <v-icon @click="toggleMobileSidebar">
            mdi-close
          </v-icon>
        </div>

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
            create order
          </v-btn>
        </div>
        <div class="sidebar__footer" />
      </div>
    </transition>

    <transition name="fade">
      <div
        v-if="isOpen && isMobile"
        class="sidebar__backdrop"
        @click="toggleMobileSidebar"
      />
    </transition>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import { tabs } from '@/views/Orders/inner_enums'
import { mapActions } from '@/utils/vuex_mappings'

export default {
  name: 'Sidebar',

  mixins: [isMobile],

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
    },
    isOpen: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    tabs
  }),
  methods: {
    ...mapActions('AUTH', ['logout']),
    async logoutBtn () {
      this.logoutError = false
      await this.logout()
      this.$router.push('/login')
    }
  }
}
</script>

<style lang="scss" scoped>
$cushing-logo: url("../../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../../assets/images/ordermaster_logo.svg");

.sidebar.desktop {
  overflow-y: auto;
  z-index: 1;
  display: none;
  position: fixed;
  width: map-get($sizes, sidebar-desktop-width);
  height: 100%;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 4rem;
  padding-bottom: 3rem;

  @media screen and (min-width: map-get($breakpoints, med)) {
    display: flex;
  }
}

.sidebar__background {
  top: 0;
  z-index: 2;
  position: fixed;
  width: 52vw;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 6.1rem;
  padding-bottom: 3rem;
}

.sidebar__backdrop {
  top: 0;
  z-index: 1;
  position: fixed;
  height: 100vh;
  width: 100vw;
  flex-grow: 1;
  background: rgba(0, 0, 0, 0.4)
}

.sidebar__close {
  position: absolute;
  top: 2.4rem;
  left: 1.5rem;
}

.sidebar__logo {
  width: 14rem;
  height: 4.3rem;
  background-image: $cushing-logo;
  background-size: contain;
  background-position: center center;
}

.sidebar__footer {
  width: 11.1rem;
  height: 4.5rem;
  margin-top: auto;
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
  display: flex;
  flex-direction: column-reverse;
  .logout__btn{
    margin: 7rem auto;
  }
}

.sidebar__mobile-options {
  display: flex;
  flex-direction: column;
  margin-top: 3.8rem;
  margin-bottom: auto;
}
</style>
