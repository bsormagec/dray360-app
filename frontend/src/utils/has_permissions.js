/* eslint-disable camelcase */
export const has_permissions = (currentUser, ...requestedPermissions) => {
  let acceptedPerms = 0
  if (currentUser === undefined) { return false }
  currentUser.permissions.forEach(perm => {
    requestedPermissions.forEach(reqPerm => {
      if (perm.name === reqPerm) {
        acceptedPerms++
      }
    })
  })

  if (acceptedPerms === requestedPermissions.length) {
    return true
  } else {
    return false
  }
}

export const has_permission = (currentUser, requestedPermission) => {
  let result = false
  if (currentUser === undefined) { return false }
  currentUser.permissions.forEach(perm => {
    if (perm.name === requestedPermission) {
      result = true
    }
  })
  return result
}

export default {
  has_permission,
  has_permissions
}
