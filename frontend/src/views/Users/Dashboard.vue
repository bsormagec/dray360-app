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
import utils, { type } from '@/store/modules/utils'
import { deleteUser } from '@/store/api_calls/users'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'

export default {
  components: {
    UserTable
  },

  mixins: [isMobile, isMedium],

  computed: {
    ...mapState(userDashboard.moduleName, {
      users: state => state.users
    })
  },

  watch: {
    isMedium: function (newVal, oldVal) {
      if (!newVal) this.setSidebar({ show: true })
    },
    isMobile: function (newVal, oldVal) {
      if (newVal) this.setSidebar({ show: false })
      else this.setSidebar({ show: true })
    }
  },

  beforeMount () {
    if (!this.isMobile) return this.setSidebar({ show: true })
    return this.setSidebar({ show: false })
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.getUsers]),
    ...mapActions(utils.moduleName, {
      setSidebar: type.setSidebar,
      setConfirmationDialog: type.setConfirmationDialog,
      setSnackbar: type.setSnackbar
    }),
    goToOrdersList () {
      this.redirectBack ? this.$router.back() : this.$router.push('/dashboard')
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
</style>
