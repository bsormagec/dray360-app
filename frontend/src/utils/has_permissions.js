/* eslint-disable camelcase */
export const has_permissions = (currentUser, ...requestedPermissions) => {
  let acceptedPerms = 0
  currentUser.permissions.forEach(perm => {
    requestedPermissions.forEach(reqPerm => {
      if (perm.name === reqPerm) {
        acceptedPerms++
      }
    })
  })
  console.log('requestedpermissions.length: ', requestedPermissions.length)
  if (acceptedPerms === requestedPermissions.length) {
    return true
  } else {
    return false
  }
}

export const has_permission = (currentUser, requestedPermission) => {
  let result = false
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
