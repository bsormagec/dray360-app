import { mapState } from '@/utils/vuex_mappings'
import auth from '@/store/modules/auth'

export default {
  data: () => ({
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    })
  }),

  methods: {
    hasAllPermissions (...requestedPermissions) {
      console.log('mixins has all permissions')
      let acceptedPerms = 0
      this.currentUser().user.permissions.forEach(perm => {
        console.log('user permission: ', perm)
        requestedPermissions.forEach(reqPerm => {
          console.log('requested permission: ', reqPerm)
          if (perm.name === reqPerm) {
            console.log('match')
            acceptedPerms++
          }
        })
      })
      console.log('requestedpermissions.length: ', requestedPermissions.length)
      if (acceptedPerms === requestedPermissions.length) {
        console.log('size match')
        return true
      } else {
        return false
      }
    },

    hasOnePermission (...requestedPermissions) {
      let result = false
      this.currentUser().user.permissions.forEach(perm => {
        requestedPermissions.forEach(reqPerm => {
          if (perm.name === reqPerm) {
            console.log('match')
            result = true
          }
        })
      })
      return result
    }
  }
}
