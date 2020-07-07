import { mapState } from '@/utils/vuex_mappings'
import auth from '@/store/modules/auth'

export default {
  data: () => ({
    ...mapState(auth.moduleName, {
      currentUser: state => state.currentUser
    })
  }),

  methods: {
    hasPermission (permission) {
      let result = false
      this.currentUser().user.permissions.forEach(perm => {
        if (perm.name === permission) {
          result = true
        }
      })
      return result
    }
  }
}
