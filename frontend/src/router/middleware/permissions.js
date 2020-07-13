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
