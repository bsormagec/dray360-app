<template>
  <div
    :class="`sidebar ${isMobile && 'mobile'}`"
  >
    <div
      v-if="!isMobile"
      class="sidebar__logo"
    />

    <div :class="`sidebar__body ${isMobile && 'mobile'}`">
      <v-btn
        color="primary"
        outlined
        width="11.5rem"
        @click="goToOrdersList()"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
        Order List
      </v-btn>

      <DetailsSidebarNavigation v-if="!isMobile" />

      <v-btn
        :color="saveBtnStyles"
        :outlined="!isEditing && !isMobile"
        :style="{ marginBottom: '1rem' }"
        test-id="toggle-btn"
        width="11.5rem"
        @click="toggleIsEditing"
      >
        {{ isEditing ? 'Save' : 'Edit Order' }}
      </v-btn>

      <v-btn
        v-if="!isMobile && hasAllPermissions('tms-submit')"
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
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import hasAllPermissions from '@/mixins/permissions'
import { formModule } from '@/views/Details/inner_store/index'
import DetailsSidebarNavigation from '@/views/Details/DetailsSidebarNavigation'
import { mapActions } from '@/utils/vuex_mappings'

export default {
  name: 'DetailsSidebar',

  components: {
    DetailsSidebarNavigation
  },

  mixins: [isMobile, hasAllPermissions],

  computed: {
    isEditing () {
      return formModule.state.isEditing
    },

    saveBtnStyles () {
      if (this.isMobile) return 'secondary'
      if (this.isEditing) return 'success'
      return 'primary'
    }
  },

  destroyed () {
    localStorage.removeItem('prevListUrl')
  },

  methods: {
    toggleIsEditing: formModule.methods.toggleIsEditing,

    ...mapActions('AUTH', ['logout']),

    async logoutBtn () {
      this.logoutError = false
      await this.logout()
      this.$router.push('/login')
    },

    goToOrdersList () {
      const prevListUrl = localStorage.getItem('prevListUrl')

      if (prevListUrl) return this.$router.push(prevListUrl)
      this.$router.push('/')
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

  &.mobile {
    height: 7rem;
    bottom: 0;
    width: 100vw;
    padding: unset;
    box-shadow: 0rem -0.4rem 1rem -0.8rem rgba(0,0,0,0.75);
  }
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

  &.mobile {
    height: 100%;
    width: 100%;
    margin: unset;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;

    button {
      margin: unset!important;
    }
  }
}

.sidebar__footer {
  width: 11.1rem;
  height: 4.5rem;
  min-height: 4.5rem;
  margin-top: 6rem;
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
  display: flex;
  flex-direction: column-reverse;
  .logout__btn{
    margin: 5rem auto;
  }
}
</style>
