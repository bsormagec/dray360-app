export default async function LoggedOut ({ next, store }) {
  try {
    await store.dispatch('AUTH/getCurrentUser')
    // If loggedin, then return to dashboard
    if (store.state.AUTH.loggedIn) {
      return next('/dashboard')
    }
  } catch (e) {
    console.log('error ', e)
    // Ignore unauthorized request, redirect to login page below.
    return next()
  }
  return next()
}
