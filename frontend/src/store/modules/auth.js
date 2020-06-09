import { getCsrfCookie, postLogin, getUser, postLogout } from '@/store/api_calls/auth'

const initialState = {
  loggedIn: false,
  currentUser: undefined,
  currentUserLoading: false,
  intendedUrl: undefined
}

const mutations = {
  auth_success: (state) => (state.loggedIn = true),
  logout: (state) => (state.loggedIn = false),
  currentUser: (state, user) => (state.currentUser = user),
  currentUserLoading: (state, isPending) => (state.currentUserLoading = !!isPending),
  intendedUrl: (state, url) => (state.intendedUrl = url)
}

const actions = {
  async login ({ commit }, authData) {
    await getCsrfCookie()
    const [error] = await postLogin(authData)

    if (!error) commit('auth_success')
  },
  async getCurrentUser ({ commit, state }, force = false) {
    const shouldntProceed = state.currentUser && !force
    if (shouldntProceed) return

    commit('currentUserLoading', true)
    const [error, user] = await getUser()

    if (!error) commit('currentUser', { user })
    commit('currentUserLoading', false)
  },
  async logout ({ commit }) {
    const [error] = await postLogout()
    if (!error) commit('logout')
  },

  async setIntendedUrl (context, { intendedUrl }) {
    context.commit('intendedUrl', intendedUrl)
  }

}

export default {
  moduleName: 'AUTH',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
