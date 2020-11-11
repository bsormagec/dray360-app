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
        width="115px"
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
            :style="{marginBottom: '10px'}"
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
        <v-btn
          color="primary"
          outlined
          width="115px"
          class="logout__btn"
          style="margin-top: 40px;"
          @click="logoutBtn"
        >
          Logout
        </v-btn>
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
import { mapActions } from 'vuex'

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
      const status = await this.logout()
      if (status) {
        this.$router.push('/login')
      }
    }
  }
}
</script>

<style lang="scss" scoped>
// $cushing-logo: url("../../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../../assets/images/ordermaster_logo.svg");

.sidebar.desktop {
  overflow-y: auto;
  z-index: 1;
  display: none;
  position: fixed;
  width: rem(map-get($sizes, sidebar-desktop-width));
  height: 100%;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: rem(40);
  padding-bottom: rem(30);

  @include media("med") {
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
  padding-top: rem(61);
  padding-bottom: rem(30);
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
  top: rem(24);
  left: rem(15);
}

.sidebar__logo {
  width: rem(140);
  height: rem(43);
  // background-image: $cushing-logo;
  background-size: contain;
  background-position: center center;
}

.sidebar__footer {
  width: rem(111);
  height: rem(45);
  margin-top: rem(40);
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
  display: flex;
  flex-direction: column-reverse;

  @include media("med") {
    margin-top: auto;
  }

  .logout__btn{
    margin: rem(70) auto;
  }
}

.sidebar__mobile-options {
  display: flex;
  flex-direction: column;
  margin-top: rem(38);
  margin-bottom: auto;
}
</style>
