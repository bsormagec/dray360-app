import { getTenantConfig } from '@/store/api_calls/utils'
import { reqStatus } from '@/enums/req_status'
import vuetify from '@/plugins/vuetify'

export const type = {
  setSnackbar: 'SET_SNACKBAR',
  setTenantConfig: 'SET_TENANT_CONFIG',
  getTenantConfig: 'GET_TENANT_CONFIG'
}
const initialState = {
  snackbar: { show: false, showSpinner: false, message: '' },
  tenantConfig: {}
}

const mutations = {
  [type.setSnackbar] (state, snackbar) {
    state.snackbar = snackbar
  },
  [type.setTenantConfig] (state, tenantConfig) {
    state.tenantConfig = tenantConfig
  }
}

const actions = {
  async [type.setSnackbar] ({ commit }, { show, showSpinner, message }) {
    commit(type.setSnackbar, { show, showSpinner, message })
    return reqStatus.success
  },
  async [type.setTenantConfig] ({ state, commit }, newconfig) {
    commit(type.setTenantConfig, { ...newconfig })

    for (const key in newconfig) {
      if (!key.includes('_color') || newconfig[key] === null) {
        continue
      }
      const colorKey = key.substring(0, key.indexOf('_color'))
      vuetify.framework.theme.themes.light[colorKey] = newconfig[key]
    }
  },
  async [type.getTenantConfig] ({ dispatch }) {
    const [error, response] = await getTenantConfig()

    if (error) return

    await dispatch(type.setTenantConfig, { ...response })
  }
}

export default {
  moduleName: 'UTILS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
