/* eslint-disable camelcase */
import { mapState } from 'vuex'
import { has_permissions, has_permission } from '@/utils/has_permissions'
import auth from '@/store/modules/auth'

export default {

  computed: {
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    })
  },

  methods: {
    hasPermissions (...requestedPermissions) {
      return has_permissions(this.currentUser, ...requestedPermissions)
    },

    hasPermission (requestedPermission) {
      return has_permission(this.currentUser, requestedPermission)
    },

    isSuperadmin () {
      return this.currentUser.is_superadmin
    }
  }
}
