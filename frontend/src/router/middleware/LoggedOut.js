import auth from '@/store/modules/auth'

export default async function LoggedOut ({ next, store }) {
  if (store.state[auth.moduleName].frontendLogout) {
    return next()
  }

  try {
    await store.dispatch('AUTH/getCurrentUser')
    // If loggedin, then return to dashboard
    if (store.getters[`${auth.moduleName}/loggedIn`]) {
      return next('/inbox')
    }
  } catch (e) {
    console.log('error ', e)
    // Ignore unauthorized request, redirect to login page below.
    return next()
  }

  return next()
}
