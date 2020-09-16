import { reqStatus } from '@/enums/req_status'
import { changePassword } from '@/store/api_calls/profile'

export const types = {
  changePassword: 'CHANGE_PASSWORD'
}

const initialState = {
  users: []
}

const mutations = {

}

const actions = {

  async [types.changePassword] ({ commit }, oldPassword, password, passwordConfirmation) {
    const [error] = await changePassword(oldPassword, password, passwordConfirmation)

    if (error) {
      return error.response.data
    }
    return reqStatus.success
  }
}

export default {
  moduleName: 'PROFILE',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
