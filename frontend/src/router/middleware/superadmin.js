export default async function auth ({ next, store }) {
  if (store.state.AUTH.currentUser) {
    return next()
  }
  try {
    await store.dispatch('AUTH/getCurrentUser')
    if (store.state.AUTH.currentUser.user.is_superadmin) {
      console.log('is superadmin true')
      return next()
    }
  } catch (e) {
    // Ignore unauthorized request, redirect to login page below.
    if (e.response?.status !== 401) {
      // throw e
      console.log(e)
    }
  }

  if (!store.state.AUTH.currentUser.user.is_superadmin) {
    console.log('not superadmin')
    // Redirect to intended URL after logging in.
    const url = window.location.pathname
    const path = (window.location.search).replace('/', '')
    const intendedUrl = url + path
    try {
      await store.dispatch('AUTH/setIntendedUrl', { intendedUrl })
    } catch (e) {
      // Ignore unauthorized request, redirect to login page below.
      console.log(e)
    }

    return next('/')
  }

  next()
}
