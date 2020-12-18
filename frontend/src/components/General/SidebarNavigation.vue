<template>
  <div>
    <v-app-bar-nav-icon
      v-if="isMobile && currentUser !== undefined"
      class="hamburger__icon"
      @click="toogleSidebar"
    />
    <div v-if="showSidebar">
      <v-navigation-drawer
        :value="showSidebar"
        class="sidebar__nav"
        absolute
        left
        :temporary="isMobile"
        :permanent="showSidebar && !isMobile"
        @input="onChangeSidebar"
      >
        <v-btn
          v-if="isMobile"
          class="mx-2 my-1"
          fab
          dark
          small
          color="primary"
          @click.stop="toogleSidebar"
        >
          <v-icon dark>
            mdi-close
          </v-icon>
        </v-btn>
        <img
          v-if="!tenantConfig.logo1"
          class="logo__dry"
          src="@/assets/images/dry360_logo.svg"
          alt=""
        >
        <img
          v-else
          class="logo__dry"
          :src="tenantConfig.logo1"
          alt=""
        >
        <img
          v-if="tenantConfig.logo2"
          class="logo__dry"
          :src="tenantConfig.logo2"
          alt=""
        >

        <div class="menu">
          <v-list>
            <v-list-group
              v-if="isSuperadmin()"
              no-action
              sub-group
            >
              <template v-slot:activator>
                <v-list-item-content>
                  <v-list-item-title>Admin</v-list-item-title>
                </v-list-item-content>
              </template>

              <v-list-item
                v-for="(el, i) in admins"
                :key="i"
                :href="el.path"
                target="_blank"
                :input-value="false"
                link
                dense
              >
                <v-list-item-title
                  class="admin__menu"
                  v-text="el.name"
                />
              </v-list-item>
            </v-list-group>
            <v-list-item-group v-model="group">
              <v-list-item
                v-for="(item, i) in menuItems"
                :key="i"
                :to="item.path"
                dense
              >
                <v-list-item-icon v-if="item.icon">
                  <v-icon v-text="item.icon" />
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title
                    v-if="item.name === 'logout' "
                    :key="i"
                    @click="logoutBtn"
                    v-text="item.name"
                  />
                  <v-list-item-title
                    v-else
                    :key="i"
                    @click="onChangeSidebar"
                    v-text="item.name"
                  />
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </div>
        <div
          v-if="currentUser !== undefined && currentUser.is_superadmin"
          class="company__name"
        >
          <h3>{{ currentUser.company.name }}</h3>
        </div>
        <img
          src="@/assets/images/LogoDryPoweredBy.svg"
          class="logo__dry_bottom"
          alt=""
        >
      </v-navigation-drawer>
    </div>
  </div>
</template>

<script>
import auth from '@/store/modules/auth'
import { mapState, mapActions } from 'vuex'
import hasPermission from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import isMobile from '@/mixins/is_mobile'

export default {

  mixins: [hasPermission, isMobile],

  data () {
    return {
      group: null,
      model: 1,
      admins: [
        { name: 'Nova', path: '/nova' },
        { name: 'Horizon', path: '/horizon' },
        { name: 'Telescope', path: '/telescope' },
        { name: 'Roles and permissions', path: '/authorization' },
        { name: 'Sentry', path: 'https://sentry.io/organizations/draymaster/issues/?project=5285677' },
        { name: 'Rules Editor', path: '/rules-editor' },
        { name: 'Usage Stats', path: '#' },
        { name: 'RefsCustoms Mapping', path: '/companies/refs-custom-mapping' }
      ]
    }
  },
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig,
      showSidebar: state => state.sidebar.show,
      menuItems () {
        return [
          { name: 'dashboard', path: '/dashboard', hasPermission: this.hasPermission('orders-view') },
          { name: 'manage users', path: '/user/dashboard', hasPermission: this.hasPermission('users-view') },
          { name: 'my profile', path: '/user/edit-profile', hasPermission: true },
          { name: 'logout', path: '#', hasPermission: true }
        ].filter((item) => item.hasPermission)
      }
    })
  },
  async mounted () {
    await this[type.getTenantConfig]()
  },
  methods: {
    ...mapActions('AUTH', ['logout']),
    ...mapActions(utils.moduleName, [type.getTenantConfig, type.setSidebar]),
    async logoutBtn () {
      await this.logout()
      this.$router.push('/login')
    },
    toogleSidebar () {
      this[type.setSidebar]({ show: !this.showSidebar })
    },
    onChangeSidebar (value) {
      this[type.setSidebar]({ show: value })
    }
  }

}
</script>

<style lang="scss">
$sidebarbackground: url("../../assets/images/menuBackground.png");
.theme--light.v-navigation-drawer:not(.v-navigation-drawer--floating) .v-navigation-drawer__border{
    background: linear-gradient(90deg, rgba(0, 60, 113, 0.1) 0%, rgba(0, 60, 113, 0.05) 31.77%, rgba(0, 60, 113, 0) 100%);
    box-shadow: inset -1px 0px 0px rgba(0, 60, 113, 0.03);
    transform: rotate(180deg);
  }
  .hamburger__icon{
    color: white !important;
    background: #003C71;
    top: rem(3);
    left: rem(3);
    z-index: 5;
  }
.sidebar__nav{
  background: linear-gradient(90deg, rgba(0, 60, 113, 0) 97.95%, rgba(0, 60, 113, 0.1) 100%), linear-gradient(0deg, rgba(0, 60, 113, 0.05), rgba(0, 60, 113, 0.05)), #FFFFFF;
  box-shadow: inset rem(-1) 0px 0px rgba(0, 60, 113, 0.03);
  display: flex;
  flex-direction: column;
  align-content: center;
  align-self: center;
  background-image: $sidebarbackground;
  background-size: cover;
  max-width: rem(196);
  position: fixed;
  .v-navigation-drawer__border{
    width: 5px;
  }

  .logo__dry{
    width: rem(140);
    margin: rem(15) auto 0 auto;
    display: block;
  }
  .logo__dry_bottom{
    @include center;bottom: rem(30);
  }
  .menu{
    .v-list-item:not(:last-child){
      border-bottom: rem(2) solid map-get($colors, gray );
    }
    .v-list-group__items a{
      border-bottom: unset !important;
    .v-list-item__title.admin__menu {
      text-transform: capitalize;
      font-size: rem(13);
      font-weight: 500;
      line-height: (13 / 16);
      letter-spacing: rem(.75);
    }
    .v-list-group--sub-group{
      .v-list-group__header{
        text-transform: uppercase;
      }
    }
    }
    .v-list-item__title{
      color: var(--v-primary-base) !important;
      font-size: rem(12);
      text-transform: uppercase;
      font-weight: 500;
      text-align: center;
      letter-spacing: rem(.75);
      padding: rem(15) 0;
      @include media('med'){
        text-align: right;
        color: var(--v-primary-base) !important;
        }
    }
    & > div{
      background: transparent !important;
    }
    .v-list-group{
      border-bottom: rem(2) solid map-get($colors, gray );
      .v-list-group__items > .v-list-item {
         padding-left: 0px;
      }
    }

    .v-list-item--active, .v-item--active, .v-list-group--active{
      box-shadow: 0px -1px 0px 0px #003C71 15% inset;
      background: linear-gradient(90deg, rgba(0, 60, 113, 0) 97.95%, rgba(0, 60, 113, 0.1) 100%), linear-gradient(0deg, rgba(0, 60, 113, 0.05), rgba(0, 60, 113, 0.05)), #FFFFFF !important;

      &::before{
        background-color: unset !important;
      }
    }
  }
  .company__name{
    position: fixed;
    top: 80vh;
    @include center;

    h3{
      color: map-get($colors, grey-4 );
      text-align: center;
      font-size: rem(14);
      font-weight: 700;
    }
  }
}
</style>
