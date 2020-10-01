<template>
  <div>
    <v-navigation-drawer
      v-model="drawer"
      permanent
      class="sidebar__nav"
      absolute
      bottom
    >
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
            v-if="currentUser !== undefined && currentUser.is_superadmin"
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
            >
              <v-list-item-title v-text="el.name" />
            </v-list-item>
          </v-list-group>
          <v-list-item-group v-model="group">
            <v-list-item
              v-for="(item, i) in menuItems"
              :key="i"
              :to="item.path"
            >
              <v-list-item-icon>
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
                  v-else-if="item.name === 'manage users' && hasPermission('users-view')"
                  :key="i"
                  v-text="item.name"
                />
                <v-list-item-title
                  v-else
                  :key="i"
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
        <h3>{{ company.name }}</h3>
      </div>
      <img
        src="@/assets/images/LogoDryPoweredBy.svg"
        class="logo__dry_bottom"
        alt=""
      >
    </v-navigation-drawer>
  </div>
</template>
<script>
import auth from '@/store/modules/auth'
import { mapState, mapActions } from '@/utils/vuex_mappings'
import hasPermission from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import companies, { types } from '@/store/modules/companies'

export default {

  mixins: [hasPermission],

  data () {
    return {
      drawer: false,
      group: null,
      model: 1,
      menuItems: [{ name: 'dashboard', path: '/dashboard' },
        { name: 'manage users', path: '/user/dashboard' },
        { name: 'my profile', path: '/user/edit-profile' },
        { name: 'logout', path: '#' }],
      admins: []

    }
  },
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    ...mapState(companies.moduleName, { company: state => state.company }),
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig
    })
  },
  watch: {
    group () {
      this.drawer = false
    }
  },
  async created () {
    this.admins = [{ name: 'Check Horizon', path: '/horizon', role: ['superadmin'] },
      { name: 'Roles and permissions', path: '/authorization', role: ['superadmin'] },
      { name: 'Telescope', path: '/telescope', role: ['superadmin'] },
      { name: 'Nova', path: '/nova', role: ['superadmin'] },
      { name: 'Sentry', path: 'https://sentry.io/organizations/draymaster/issues/?project=5285677', role: ['superadmin'] },
      { name: 'Rules Editor', path: '/rules-editor', role: ['admin'] },
      { name: 'usage stats', path: '' },
      { name: 'RefsCustoms Mapping', path: `/companies/${this.currentUser.t_company_id}/refs-custom-mapping`, role: ['admin'] }
    ]
    await this[type.getTenantConfig]()
    await this[types.getCompany]({ id: this.currentUser.t_company_id })
  },

  methods: {
    ...mapActions('AUTH', ['logout']),
    ...mapActions(utils.moduleName, [type.getTenantConfig]),
    ...mapActions(companies.moduleName, [types.getCompany]),
    async logoutBtn () {
      await this.logout()
      this.$router.push('/login')
    }
  }

}
</script>

<style lang="scss" scoped>

$sidebarbackground: url("../../assets/images/bg_sidebar.png");
    .sidebar__nav{
        background: linear-gradient(90deg, rgba(0, 60, 113, 0.1) 0%, rgba(0, 60, 113, 0.05) 31.77%, rgba(0, 60, 113, 0) 100%);
        box-shadow: inset -1px 0px 0px rgba(0, 60, 113, 0.03);
        display: flex;
        flex-direction: column;
        align-content: center;
        align-self: center;
        background-image: $sidebarbackground;
        background-size: cover;

        .logo__dry{
            width: 20rem;
            height: 4.8rem;
            margin: 2rem auto;
            display: block;

        }
        .logo__dry_bottom{
            @include center;
            bottom: 3rem;

        }
        .menu{
            .v-list-item:not(:last-child){
                border-bottom: 0.2rem solid map-get($colors, gray );
            }
            .v-list-item__title{
                color: var(--v-primary-base) !important;
                text-transform: uppercase;
                font-weight: 500;
                text-align: right;
                letter-spacing: 0.75px;
                padding: 1.5rem 0px;
            }
            & > div{
                background: transparent !important;
            }
        }
        .company__name{
          position: absolute;
          bottom: 15rem;
          @include center;
          h3{
            color: map-get($colors, grey-4 );
            text-align: center;
          }
        }
    }

</style>
