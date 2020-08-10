import { getCsrfCookie, postLogin, getUser, postLogout, postLeaveImpersonation } from '@/store/api_calls/auth'
import { reqStatus } from '@/enums/req_status'
import get from 'lodash/get'

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

    if (error) return reqStatus.error
    commit('auth_success')
    return reqStatus.success
  },
  async getCurrentUser ({ commit, state }, force = false) {
    const shouldntProceed = state.currentUser && !force
    if (shouldntProceed) return

    commit('currentUserLoading', true)
    const [error, user] = await getUser()

    if (!error) {
      commit('currentUser', { user })
      commit('auth_success')
    }
    commit('currentUserLoading', false)
  },
  async logout ({ commit, state }) {
    if (get(state.currentUser, 'user.is_impersonated')) {
      const [error] = await postLeaveImpersonation()

      if (error) return

      window.location = '/nova'
      return
    }

    const [error] = await postLogout()
    if (!error) {
      commit('logout')
      return reqStatus.success
    }
  },
  simpleLogout ({ commit }) {
    commit('logout')
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
