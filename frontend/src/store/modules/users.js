import { reqStatus } from '@/enums/req_status'
import { getUsers, editUser } from '@/store/api_calls/users'

export const types = {
  setUsers: 'SET_USERS',
  getUsers: 'GET_USERS',
  editUser: 'EDIT_USER',
  setRoles: 'SET_ROLES'
}

const initialState = {
  users: []
}

const mutations = {
  [types.setUsers] (state, { usersData }) {
    state.users = usersData
  },

  [types.editUser] (state, { userData, i }) {
    state.users[i] = userData
  },

  [types.setRoles] (state, { rolesData }) {
    state.roles = rolesData
  }
}

const actions = {
  async [types.getUsers] ({ commit }) {
    const [error, data] = await getUsers()

    if (error) return reqStatus.error

    commit(types.setUsers, { usersData: data })
    return reqStatus.success
  },

  async [types.editUser] ({ commit }, user) {
    const userId = user.user_id
    delete user.user_id

    const [error, data] = await editUser(userId, user)

    if (error) return reqStatus.error

    commit(types.editUser, { userData: data.data }, userId)
    return reqStatus.success
  }
}

export default {
  moduleName: 'USER_DASHBOARD',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
