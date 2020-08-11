import { reqStatus } from '@/enums/req_status'
export const type = {
  setSnackbar: 'SET_SNACKBAR'
}
const initialState = {
  snackbar: { show: false, showSpinner: false, message: '' }
}

const mutations = {
  [type.setSnackbar] (state, snackbar) {
    state.snackbar = snackbar
  }
}

const actions = {
  async [type.setSnackbar] ({ commit }, { show, showSpinner, message }) {
    commit(type.setSnackbar, { show, showSpinner, message })
    return reqStatus.success
  }

}

export default {
  moduleName: 'UTILS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
