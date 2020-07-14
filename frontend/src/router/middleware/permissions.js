// import hasAllPermissions from '@/mixins/permissions'

export default function permission (requestedPermission) {
  let canAccess = false
  return async function permission ({ next, store }) {
    store.state.AUTH.currentUser.user.permissions.forEach(perm => {
      if (perm.name === requestedPermission) {
        canAccess = true
      }
    })
    if (canAccess) {
      console.log('given permission')
      return next()
    } else {
      return next('/')
    }
  }
}

// export default function permission (requestedPermission) {
//   return async function permission ({ next, store }) {
//     console.log('middleware permissions')
//     if (hasAllPermissions(requestedPermission)) {
//       console.log('given permission')
//       return next()
//     } else {
//       return next('/')
//     }
//   }
// }
