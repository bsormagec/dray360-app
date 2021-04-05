import { getCsrfCookie, postLogin, getUser, postLogout, postLeaveImpersonation, postForgotPassword, postPasswordReset } from '@/store/api_calls/auth'
import { reqStatus } from '@/enums/req_status'
import get from 'lodash/get'
import { type as utilsTypes } from './utils'
import requestsList, { types as requestsListTypes } from './requests-list'

const initialState = {
  loggedIn: false,
  currentUser: undefined,
  currentUserLoading: false,
  intendedUrl: undefined
}

const mutations = {
  auth_success: (state) => (state.loggedIn = true),
  logout: (state) => {
    state.loggedIn = false
    state.currentUser = undefined
  },
  currentUser: (state, { user }) => (state.currentUser = user),
  currentUserLoading: (state, isPending) => (state.currentUserLoading = !!isPending),
  intendedUrl: (state, url) => (state.intendedUrl = url)
}

const actions = {
  async login ({ commit, dispatch }, authData) {
    await getCsrfCookie()
    const [error] = await postLogin(authData)

    if (error) {
      return { ...(error.response.data), status: reqStatus.error }
    }
    commit('auth_success')
    await dispatch('getCurrentUser', true)
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
  async logout ({ commit, state, dispatch }) {
    if (get(state.currentUser, 'is_impersonated')) {
      const [error] = await postLeaveImpersonation()

      if (error) return

      window.location = '/nova'
      return
    }

    const [error] = await postLogout()
    if (!error) {
      dispatch(
        `${requestsList.moduleName}/${requestsListTypes.setSupervise}`,
        false,
        { root: true }
      )
      commit('logout')
      return reqStatus.success
    }
  },
  simpleLogout ({ commit }) {
    commit('logout')
  },
  async setIntendedUrl (context, { intendedUrl }) {
    context.commit('intendedUrl', intendedUrl)
  },

  async ForgotPassword ({ commit }, email) {
    const [error] = await postForgotPassword(email)

    if (error) {
      return { ...(error.response.data), status: reqStatus.error }
    }

    return { status: reqStatus.success }
  },

  async PasswordReset ({ commit }, token, email, password, passwordConfirmation) {
    const [error] = await postPasswordReset(token, email, password, passwordConfirmation)

    if (error) {
      return { ...(error.response.data), status: reqStatus.error }
    }

    return { status: reqStatus.success }
  }

}

export default {
  moduleName: 'AUTH',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
