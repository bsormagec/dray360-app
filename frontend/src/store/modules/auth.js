import { getCsrfCookie, postLogin, getUser } from '@/store/api_calls/auth'

const initialState = {
  currentUser: undefined,
  currentUserLoading: false
}

const mutations = {
  auth_success: (state) => (state.loggedIn = true),
  logout: (state) => (state.loggedIn = false),
  currentUser: (state, user) => (state.currentUser = user),
  currentUserLoading: (state, isPending) => (state.currentUserLoading = !!isPending)
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
  }
}

export default {
  moduleName: 'AUTH',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
