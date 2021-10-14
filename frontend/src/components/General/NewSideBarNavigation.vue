<template>
  <v-navigation-drawer
    class="app__menu-drawer"
    app
    floating
    color="primary"
    dark
    :value="showSidebar"
    :mini-variant="!isMobile"
    mini-variant-width="60"
    :expand-on-hover="!isMobile"
    width="290"
    @input="onChange"
    @transitionend="setShowSubMenu(false)"
  >
    <template v-slot:prepend>
      <v-container
        fluid
        pa-0
        d-flex
      >
        <v-icon class="envase-logo">
          $vuetify.icons.envase
        </v-icon>
        <img
          class="envase-text"
          src="../../assets/images/envaseTextLogo.png"
          alt="Envase Order AI"
        >
      </v-container>
    </template>

    <v-list
      class="app__menu"
      dark
    >
      <v-list-item-group :class="{open: subMenuOpen}">
        <v-list-group
          v-if="canViewOtherCompanies()"
          prepend-icon="mdi-account-circle"
          no-action
          color="white"
          :value="subMenuOpen && menusOpen.admin"
          @click.prevent.stop="setShowSubMenu('admin', true)"
        >
          <template v-slot:activator>
            <v-list-item-title>
              Admin
            </v-list-item-title>
          </template>

          <v-list-item @click="setShowSubMenu('admin', false)">
            <v-list-item-icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Admin</v-list-item-title>
          </v-list-item>

          <v-list-item
            v-for="(item, i) in adminMenuItems"
            :key="i"
            link
            :to="item.path"
            :href="item.href"
            :target="item.target || '_blank'"
          >
            <v-list-item-title>{{ item.name }}</v-list-item-title>
          </v-list-item>
        </v-list-group>

        <v-list-group
          v-if="canViewOtherCompanies()"
          prepend-icon="mdi-view-dashboard"
          no-action
          color="white"
          :value="subMenuOpen && menusOpen.reporting"
          @click.prevent.stop="setShowSubMenu('reporting', true)"
        >
          <template v-slot:activator>
            <v-list-item-title>
              Reporting
            </v-list-item-title>
          </template>

          <v-list-item @click="setShowSubMenu('reporting', false)">
            <v-list-item-icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Reporting</v-list-item-title>
          </v-list-item>

          <v-list-item
            v-for="(item, i) in reportingMenuItems"
            :key="i"
            link
            :to="item.path"
            :href="item.href"
            :target="item.target || '_blank'"
          >
            <v-list-item-title>{{ item.name }}</v-list-item-title>
          </v-list-item>
        </v-list-group>

        <v-list-item
          v-for="(item, i) in menuItems"
          :key="i"
          link
          :to="item.path"
          :href="item.href"
          v-on="item.events || {}"
        >
          <v-list-item-icon>
            <v-progress-circular
              v-if="item.hasLoading && loading"
              :size="25"
              indeterminate
              color="primary"
            />
            <v-icon v-else>
              {{ item.icon }}
            </v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ item.name }}
          </v-list-item-title>
        </v-list-item>
      </v-list-item-group>
    </v-list>

    <template v-slot:append>
      <v-icon class="d360-icon">
        $vuetify.icons.poweredBy360
      </v-icon>
    </template>
  </v-navigation-drawer>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'
import auth from '@/store/modules/auth'
import hasPermission from '@/mixins/permissions'
import { reqStatus } from '@/enums/req_status'
import events from '@/enums/events'

export default {
  name: 'NewSideBarNavigation',

  mixins: [hasPermission],

  data: () => ({
    subMenuOpen: false,
    menusOpen: {
      admin: false,
      reporting: false,
    },
    loading: false,
  }),

  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    ...mapState(utils.moduleName, {
      showSidebar: state => state.sidebar.show
    }),

    isMobile () {
      return this.$vuetify.breakpoint.mobile
    },

    adminMenuItems () {
      return [
        { name: 'Nova', href: '/nova', hasPermission: this.hasPermission('nova-view') },
        { name: 'Horizon', href: '/horizon', hasPermission: this.hasPermission('nova-view') },
        { name: 'Websockets', href: '/laravel-websockets', hasPermission: this.hasPermission('nova-view') },
        { name: 'Roles and permissions', href: '/authorization', hasPermission: this.hasPermission('nova-view') },
        { name: 'Sentry', href: 'https://sentry.io/organizations/draymaster/issues/?project=5285677', hasPermission: this.hasPermission('nova-view') },
        { name: 'Rules Editor', path: '/rules-editor', target: '_self', hasPermission: this.hasPermission('rules-editor-view') },
        { name: 'RefsCustoms Mapping', href: '/companies/refs-custom-mapping', hasPermission: this.hasPermission('nova-view') },
        { name: 'Field Mapping Portal', path: '/field-mapping', target: '_self', hasPermission: this.hasPermission('field-maps-view') },
      ].filter((item) => item.hasPermission)
    },

    reportingMenuItems () {
      return [
        { name: 'Accounting Dashboard', path: '/accounting-dashboard', target: '_self', hasPermission: this.hasPermission('metrics-view') },
        { name: 'Audit Logs', path: '/audit-logs', target: '_self', hasPermission: this.hasPermission('audit-logs-view') },
        { name: 'Comments Logs', path: '/comments-logs', target: '_self', hasPermission: this.hasPermission('orders-view') },
      ]
    },

    menuItems () {
      return [
        { name: 'inbox', path: '/inbox', icon: 'mdi-inbox', hasPermission: this.hasPermission('orders-view') },
        { name: 'upload images', icon: 'mdi-upload', events: { click: this.openUploadImages }, hasPermission: this.hasPermission('pt-images-create') },
        { name: 'search', path: '/search', icon: 'mdi-magnify', hasPermission: this.hasPermission('orders-view') },
        { name: 'manage users', path: '/user/dashboard', icon: 'mdi-account-multiple', hasPermission: this.hasPermission('users-view') },
        { name: 'my profile', path: '/user/edit-profile', icon: 'mdi-account-box-outline', hasPermission: true },
        { name: 'logout', icon: 'mdi-logout', events: { click: this.logoutHandler }, hasPermission: true, hasLoading: true }
      ].filter((item) => item.hasPermission)
    }
  },

  watch: {
    showSidebar (newVal) {
      if (!newVal) {
        this.subMenuOpen = false
      }
    },
  },

  beforeMount () {
    if (!this.isMobile) {
      this.setSidebar({ show: true })
    }
  },

  methods: {
    ...mapActions('AUTH', ['logout']),
    ...mapActions(utils.moduleName, [actionTypes.setSidebar]),

    onChange (value) {
      this.setSidebar({ show: value })
    },

    async logoutHandler () {
      this.loading = true
      const result = await this.logout()
      this.loading = false
      if (result === reqStatus.success) {
        return this.$router.push('/login').catch(() => {})
      }
    },

    openUploadImages () {
      this.$root.$emit(events.openPtFileUploadDialog)
    },

    setShowSubMenu (prop, value) {
      for (const key in this.menusOpen) {
        this.menusOpen[key] = false
      }
      this.menusOpen[prop] = value
      this.subMenuOpen = value
    },
  },
}
</script>

<style lang="scss" scoped>
$sidebarbackground: url("../../assets/images/sidebarbackground.png");

.app__menu-drawer {
    background-image: $sidebarbackground;
    background-position: left bottom;
    background-repeat: no-repeat;
    background-size: 290px auto;
    background-blend-mode: color-burn;

    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 62px;
        // border-right: 1px solid rgba(255, 255, 255, .2);
    }
}

.v-list.app__menu::v-deep {
  padding: 0;

  .v-list-group {
    .v-list-group__items {
      position: absolute;
      left: rem(290);
      top: 0;
      width: rem(290);

      .v-list-item {
        &:first-child {
          &::before {
            background-color: var(--v-primary-darken1);
            opacity: 1;
          }

          .v-list-item__icon {
            background-color: var(--v-primary-lighten1);
          }

          .v-icon {
            opacity: 1;
          }

          .v-list-item__title {
            margin-left: rem(35);
            background-color: var(--primary-darken1);
          }
        }

        .v-list-item__title {
          margin-left: rem(96);
        }
      }
    }

    // .v-list-item {
    //   .v-list-item__icon.v-list-group__header__prepend-icon {
    //     border-top: 1px solid rgba(255, 255, 255, .2);
    //     border-bottom: none;
    //   }

    //   .v-list-item__icon.v-list-group__header__append-icon {
    //     border: none;
    //   }
    // }
  }

  .v-item-group {
    &.open {
      transform: translateX(-100%);
    }
    .v-list-group .v-list-group__header {
      .v-list-group__header__append-icon {
        .v-icon {
          transform: rotate(-90deg);
        }
      }
    }
  }

  .v-list-item {
    padding: 0;
    justify-content: initial;

    &::before {
      border-radius: 0;
    }

    // &:last-child {
    //   .v-list-item__icon {
    //     border-bottom: 1px solid rgba(255, 255, 255, .2);
    //   }
    // }

    &__icon {
      min-width: auto;
      margin: 0;
      // border-top: 1px solid rgba(255, 255, 255, .2);
      z-index: 2;

      .v-icon {
        margin: rem(18);
        opacity: 0.4;
      }

      &.v-list-group__header__append-icon {
        border: none;
      }
    }

    &__title {
      margin-left: rem(35);
      font-weight: 400;
      font-size: rem(12);
      letter-spacing: rem(1);
      line-height: (32 / 12);
      text-transform: uppercase;
      z-index: 1;
    }

    &.v-list-item--active {
      &::before {
        background-color: var(--v-primary-darken1);
        opacity: 1;
      }
      .v-list-item__icon {
        background-color: var(--v-primary-lighten1);
      }
      .v-icon {
        opacity: 1;
      }
    }
  }
}

.v-icon.envase-logo::v-deep {
  margin: rem(14) rem(16) rem(31);
  .custom-svg-icon {
    width: rem(28);
    height: rem(34);
  }
}

.envase-text {
  width: rem(95);
  height: rem(44);
  margin: rem(14) 0 0 rem(34);
  object-fit: contain;
}

.v-icon.d360-icon::v-deep {
  position: absolute;
  left: 10px;
  bottom: 10px;
  transform-origin: bottom left;
  transform: rotate(-90deg) translateY(100%);
  opacity: 0.6;
  .custom-svg-icon {
    width: rem(103);
    height: rem(38);
  }
}
</style>
