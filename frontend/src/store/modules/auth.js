import { getCsrfCookie, postLogin, getUser, postLogout, postLeaveImpersonation } from '@/store/api_calls/auth'
import { reqStatus } from '@/enums/req_status'
import get from 'lodash/get'
import { type as utilsTypes } from './utils'

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

    if (error) {
      return { ...(error.response.data), status: reqStatus.error }
    }
    commit('auth_success')
    return { status: reqStatus.success }
  },
  async getCurrentUser ({ commit, state, dispatch }, force = false) {
    const shouldntProceed = state.currentUser && !force
    if (shouldntProceed) return

    commit('currentUserLoading', true)
    const [error, user] = await getUser()

    if (!error) {
      commit('currentUser', { user })
      await dispatch(`UTILS/${utilsTypes.setTenantConfig}`, { ...(user.configuration) }, { root: true })
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
