export const hasRole = (user, role) => {
  const roles = Array.isArray(role) ? role : [role]

  if (!user) {
    return false
  }
  const matchedRoles = user.roles.filter(role => {
    return roles.includes(role.name)
  })

  return matchedRoles.length > 0
}
