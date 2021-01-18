<template>
  <div>
    <UserTable
      class="general-table"
      table-title="User list"
      :has-search="true"
      :has-column-filters="true"
      :has-bulk-actions="false"
      :bulk-actions="['Delete selected', 'Deactivate account', 'Reset password']"
      :has-action-button="{showButton: false, action: '/'}"
      :has-add-button="{showButton: true, action: '/'}"
      :has-calendar="false"
      @delete-item="deleteUser"
    />
  </div>
</template>

<script>

import UserTable from '@/components/Users/UserTable'
import { mapState, mapActions } from 'vuex'
import userDashboard, { types } from '@/store/modules/users'
import utils, { type } from '@/store/modules/utils'
import { deleteUser } from '@/store/api_calls/users'

export default {
  components: {
    UserTable
  },

  computed: {
    ...mapState(userDashboard.moduleName, {
      users: state => state.users
    })
  },

  beforeMount () {
    this.showSidebar()
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.getUsers]),
    ...mapActions(utils.moduleName, {
      setSidebar: type.setSidebar,
      setConfirmationDialog: type.setConfirmationDialog,
      setSnackbar: type.setSnackbar
    }),

    async showSidebar () {
      await this.setSidebar({ show: true })
    },

    deleteUser (id) {
      this.setConfirmationDialog({
        title: 'Are you sure you want to delete this user?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await deleteUser(id)

          let message = ''
          if (error === undefined) {
            message = 'User deleted'
            location.reload()
          } else {
            message = 'An error has ocurred'
          }

          await this.setSnackbar({ show: true, message })
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.user__list{
  @include media("min") {
      padding: rem(15)
    }}
</style>
