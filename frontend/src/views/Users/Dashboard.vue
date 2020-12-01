<template>
  <div>
    <UserTable
      class="general-table"
      table-title="User list"
      :has-search="true"
      :has-column-filters="true"
      :has-bulk-actions="true"
      :bulk-actions="['Delete selected', 'Deactivate account', 'Reset password']"
      :has-action-button="{showButton: false, action: '/'}"
      :has-add-button="{showButton: true, action: '/'}"
      :has-calendar="false"
      @deleteItem="deleteUser"
    />
  </div>
</template>

<script>

import UserTable from '@/components/Users/UserTable'
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import userDashboard, { types } from '@/store/modules/users'
import utils, { type } from '@/store/modules/utils'
export default {
  components: {
    UserTable
  },

  computed: {
    ...mapState(userDashboard.moduleName, {
      users: state => state.users
    })
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.getUsers, types.deleteUser]),
    ...mapActions(utils.moduleName, [type.setSidebar]),

    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    },

    async deleteUser (id) {
      const status = await this[types.deleteUser](id)

      if (status === reqStatus.success) {
        console.log('delete user success')
      } else {
        console.log('error')
      }

      location.reload()
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
