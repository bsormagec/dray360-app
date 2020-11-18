import { getTenantConfig } from '@/store/api_calls/utils'
import { reqStatus } from '@/enums/req_status'
import vuetify from '@/plugins/vuetify'

export const type = {
  setSnackbar: 'SET_SNACKBAR',
  setConfirmationDialog: 'SET_CONFIRMATION_DIALOG',
  cancelConfirmationDialog: 'CANCEL_CONFIRMATION_DIALOG',
  acceptConfirmationDialog: 'ACCEPT_CONFIRMATION_DIALOG',
  setTenantConfig: 'SET_TENANT_CONFIG',
  getTenantConfig: 'GET_TENANT_CONFIG',
  setSidebar: 'SET_SIDEBAR'
}
const initialState = {
  snackbar: { show: false, showSpinner: false, message: '' },
  tenantConfig: {},
  confirmationDialog: getBaseConfirmationDialog(),
  sidebar: { show: false }
}

const mutations = {
  [type.setSnackbar] (state, snackbar) {
    state.snackbar = snackbar
  },
  [type.setTenantConfig] (state, tenantConfig) {
    state.tenantConfig = tenantConfig
  },
  [type.setConfirmationDialog] (state, confirmationDialog) {
    state.confirmationDialog = { ...confirmationDialog }
  },
  [type.setSidebar] (state, sidebar) {
    state.sidebar = sidebar
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
  },
  [type.setConfirmationDialog] ({ commit }, {
    title = '',
    text = '',
    confirmText = 'Accept',
    cancelText = 'Cancel',
    onConfirm,
    onCancel
  }) {
    commit(type.setConfirmationDialog, {
      open: true,
      title,
      text,
      confirmText,
      cancelText,
      onConfirm,
      onCancel
    })
  },
  [type.acceptConfirmationDialog] ({ commit, state }) {
    if (state.confirmationDialog.onConfirm) {
      state.confirmationDialog.onConfirm()
    }

    commit(type.setConfirmationDialog, getBaseConfirmationDialog())
  },
  [type.cancelConfirmationDialog] ({ commit, state }) {
    if (state.confirmationDialog.onCancel) {
      state.confirmationDialog.onCancel()
    }

    commit(type.setConfirmationDialog, getBaseConfirmationDialog())
  },
  [type.setSidebar] ({ commit }, { show }) {
    commit(type.setSidebar, { show })
  }
}

function getBaseConfirmationDialog () {
  return {
    open: false,
    title: '',
    text: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    onConfirm: () => {},
    onCancel: () => {}
  }
}

export default {
  moduleName: 'UTILS',
  namespaced: true,
  state: initialState,
  mutations,
  actions
}
