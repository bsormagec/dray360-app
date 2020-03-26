import axios from '@/store/api_calls/axios_config'

const initialState = {
  currentUser: null,
  currentUserLoading: false
}

const mutations = {
  auth_success (state) {
    state.loggedIn = true
  },
  logout (state) {
    state.loggedIn = false
  },
  currentUser: (state, user) => (state.currentUser = user),
  currentUserLoading: (state, isPending) => (state.currentUserLoading = !!isPending)
}

const actions = {
  async login ({ commit }, authData) {
    await axios.get('/sanctum/csrf-cookie')
    await axios.post('/api/login', authData)
    commit('auth_success')
  },
  async getCurrentUser ({ commit, state }, force = false) {
    if (state.currentUser && !force) {
      return
    }

    try {
      commit('currentUserLoading', true)
      const user = await (await axios.get('api/user')).data
      commit('currentUser', { user })
    } finally {
      commit('currentUserLoading', false)
    }
  }
}

export default {
  moduleName: 'AUTH',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
