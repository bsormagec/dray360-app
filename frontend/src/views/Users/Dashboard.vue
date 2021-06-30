<template>
  <div class="pa-5">
    <UserTable
      table-title="User list"
      :has-search="true"
      :has-column-filters="true"
      :has-bulk-actions="false"
      :bulk-actions="['Delete selected', 'Deactivate account', 'Reset password']"
      :has-action-button="{showButton: false, action: '/'}"
      :has-add-button="{showButton: true, action: '/'}"
      :has-calendar="false"
      :virtual-back-button="isMobile"
      @delete-item="deleteUser"
      @go-back="goToOrdersList"
    />
  </div>
</template>

<script>

import UserTable from '@/components/Users/UserTable'
import { mapState, mapActions } from 'vuex'
import userDashboard, { types } from '@/store/modules/users'
import utils, { actionTypes } from '@/store/modules/utils'
import { deleteUser } from '@/store/api_calls/users'

export default {
  components: {
    UserTable
  },

  computed: {
    ...mapState(userDashboard.moduleName, {
      users: state => state.users
    }),
    isMobile () {
      return this.$vuetify.breakpoint.mobile
    },
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.getUsers]),
    ...mapActions(utils.moduleName, {
      setConfirmationDialog: actionTypes.setConfirmationDialog,
    }),
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),
    goToOrdersList () {
      this.redirectBack ? this.$router.back() : this.$router.push('/inbox')
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

          await this.setSnackbar({ message })
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
