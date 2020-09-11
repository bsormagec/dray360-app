<template>
  <div class="row">
    <div class="col-2 sidebar__navigation">
      <SidebarNavigation :menu-items="menuitems" />
    </div>
    <div class="user__list col-10">
      <UserTable
        class="general-table"
        table-title="User list"
        :customheaders="headers"
        :active-page="1"
        :custom-items="users()"
        :has-search="true"
        :has-column-filters="true"
        :has-bulk-actions="true"
        :bulk-actions="['Delete selected', 'Deactivate account', 'Reset password']"
        :has-action-button="{showButton: false, action: '/'}"
        injections="Orders"
        :has-add-button="{showButton: true, action: '/'}"
        :has-calendar="false"
        @searchToParent="onChildSearchUpdate"
        @deleteItem="deleteUser"
      />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import UserTable from '@/components/Users/UserTable'
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import userDashboard, { types } from '@/store/modules/users'
export default {
  components: {
    SidebarNavigation,
    UserTable
  },
  data: () => ({
    ...mapState(userDashboard.moduleName, {
      users: state => state.users
    }),

    searchQuery: '',
    headers: [
      { text: 'Name', value: 'name' },
      { text: 'Email', value: 'email' },
      { text: 'Org', value: 'company.name' },
      { text: 'Permission', value: 'roles[0].name' },
      { text: 'Status', value: 'deactivated_at' },
      { text: 'Actions', value: 'actions', sortable: false }
    ],
    menuitems: [
      {
        text: 'Dashboard',
        path: '/user/Dashboard'
      },
      {
        text: 'Manage Users'
      },
      {
        text: 'My Profile'
      },
      {
        text: 'Logout'
      }
    ]
  }),

  async mounted () {
    const vc = this
    await vc.fetchUsers()
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.getUsers, types.deleteUser]),

    async fetchUsers () {
      const status = await this[types.getUsers]()
      console.log('this.users: ', this.users())

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },

    async deleteUser (id) {
      const status = await this[types.deleteUser](id)

      if (status === reqStatus.success) {
        console.log('delete user success')
      } else {
        console.log('error')
      }

      location.reload()
    },

    onChildSearchUpdate (value) {
      this.searchQuery = value
      this.handleLocationUrl()
    },

    handleLocationUrl () {
      const newUrl = `?searchQuery=${this.searchQuery}`

      if (location.search !== newUrl) {
        this.$router.replace(newUrl)
      }
    }
  }
}
</script>
<style lang="scss" scoped>
  .user__list{
    padding: 2rem !important;
  }
  .sidebar__navigation{
    max-width: 20rem !important;
  }
</style>
