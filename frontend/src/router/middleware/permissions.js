/* eslint-disable camelcase */
import { has_permissions } from '@/utils/has_permissions'

export default function permission (...requestedPermissions) {
  return async function permission ({ next, store }) {
    const canAccess = has_permissions(store.state.AUTH.currentUser.user, ...requestedPermissions)
    if (canAccess) {
      return next()
    }
    return next('/')
  }
}
