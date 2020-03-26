export default async function auth ({ next, store }) {
  if (store.state.AUTH.currentUser) {
    return next()
  }
  try {
    await store.dispatch('AUTH/getCurrentUser')
  } catch (e) {
    // Ignore unauthorized request, redirect to login page below.
    if (e.response?.status !== 401) {
      // throw e
      console.log(e)
    }
  }

  if (!store.state.AUTH.currentUser) {
    // TODO: Redirect to intended URL after logging in.
    return next('/login')
  }

  next()
}
