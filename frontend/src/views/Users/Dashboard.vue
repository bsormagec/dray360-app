<template>
  <div class="row">
    <div class="col-2 sidebar__navigation">
      <SidebarNavigation :menu-items="menuitems" />
    </div>
    <div class="user__list col-10">
      <GeneralTable
        class="general-table"
        table-title="User list"
        :customheaders="headers"
        :active-page="1"
        :custom-items="items"
        :has-search="true"
        :has-column-filters="true"
        :has-bulk-actions="true"
        :bulk-actions="['Delete selected', 'Deactivate accounts']"
        :has-action-button="{showButton: false, action: '/'}"
        injections="Orders"
        :has-add-button="{showButton: false, action: '/'}"
        v-on:searchToParent="onChildSearchUpdate"
      />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import GeneralTable from '@/components/General/GeneralTable'
export default {
  components: {
    SidebarNavigation,
    GeneralTable
  },
  data () {
    return {
      searchQuery: '',
      headers: [
        {
          text: 'First',
          align: 'start',
          sortable: false,
          value: 'first'
        },
        { text: 'Last', value: 'last' },
        { text: 'Email', value: 'email' },
        { text: 'Position', value: 'position' },
        { text: 'Org', value: 'org' },
        { text: 'Permission', value: 'permissions' },
        { text: 'Status', value: 'status' },
        { text: 'Password', value: 'password' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      items: [
        {
          id: 0,
          first: 'John',
          last: 'Doe',
          email: 'jdoe@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 1,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 2,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 3,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 4,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 5,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 6,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 7,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 8,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        },
        {
          id: 9,
          first: 'Bill',
          last: 'Brasky',
          email: 'bbrasky@email.com',
          position: 'Distpatcher',
          org: 'Operations',
          permissions: 'Editor',
          status: 'Active',
          password: 'Reset'
        }
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
    }
  },
  methods: {
    onChildSearchUpdate (value) {
      this.searchQuery = value
      this.handleLocationUrl(this.searchQuery)
    },

    handleLocationUrl (searchQuery) {
      const search = `?searchQuery=${searchQuery}`

      if (location.search !== search) {
        this.$router.replace(search)
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
