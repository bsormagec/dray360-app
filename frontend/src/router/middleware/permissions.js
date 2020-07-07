// export default function permission (...permissions) {
//   return async function permission ({ next, store }) {
//     if (!store.state.currentUser?.canAny(permissions)) {
//       return next(new Error('You do not have permission to view this page.'))
//     }
//     next()
//   }
// }

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
