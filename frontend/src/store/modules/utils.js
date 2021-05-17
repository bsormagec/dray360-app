import { getTenantConfig } from '@/store/api_calls/utils'
import vuetify from '@/plugins/vuetify'

export const mutationTypes = {
  setSnackbar: 'SET_SNACKBAR',
  setTenantConfig: 'SET_TENANT_CONFIG',
  setConfirmationDialog: 'SET_CONFIRMATION_DIALOG',
  setSidebar: 'SET_SIDEBAR'
}

export const actionTypes = {
  setSnackbar: 'setSnackbar',
  setConfirmationDialog: 'setConfirmationDialog',
  cancelConfirmationDialog: 'cancelConfirmationDialog',
  acceptConfirmationDialog: 'acceptConfirmationDialog',
  getTenantConfig: 'getTenantConfig',
  setTenantConfig: 'setTenantConfig',
  setSidebar: 'setSidebar',
}

const initialState = {
  snackbar: { message: '', multiline: false, timeout: 5000 },
  tenantConfig: {},
  confirmationDialog: getBaseConfirmationDialog(),
  sidebar: { show: false }
}

const mutations = {
  [mutationTypes.setSnackbar] (state, snackbar) {
    state.snackbar = snackbar
  },

  [mutationTypes.setTenantConfig] (state, tenantConfig) {
    state.tenantConfig = tenantConfig
  },

  [mutationTypes.setConfirmationDialog] (state, confirmationDialog) {
    state.confirmationDialog = { ...confirmationDialog }
  },

  [mutationTypes.setSidebar] (state, sidebar) {
    state.sidebar = sidebar
  }

}

const actions = {
  [actionTypes.setSnackbar] ({ commit }, { message = '', multiline = false, timeout = 5000 }) {
    commit(mutationTypes.setSnackbar, { message, multiline, timeout })
  },

  async [actionTypes.setTenantConfig] ({ state, commit }, newconfig) {
    commit(mutationTypes.setTenantConfig, { ...newconfig })

    for (const key in newconfig) {
      if (!key.includes('_color') || newconfig[key] === null) {
        continue
      }
      const colorKey = key.substring(0, key.indexOf('_color'))
      vuetify.framework.theme.themes.light[colorKey] = newconfig[key]
    }
  },

  async [actionTypes.getTenantConfig] ({ dispatch }) {
    const [error, response] = await getTenantConfig()

    if (error) return

    await dispatch(actionTypes.setTenantConfig, { ...response })
  },

  [actionTypes.setConfirmationDialog] ({ commit }, {
    title = '',
    text = '',
    hasInputValue = '',
    noWrap = false,
    confirmText = 'Accept',
    cancelText = 'Cancel',
    onConfirm,
    onCancel
  }) {
    commit(mutationTypes.setConfirmationDialog, {
      open: true,
      title,
      text,
      hasInputValue,
      confirmText,
      cancelText,
      onConfirm,
      onCancel,
      noWrap,
    })
  },

  [actionTypes.acceptConfirmationDialog] ({ commit, state }, value) {
    if (state.confirmationDialog.onConfirm) {
      state.confirmationDialog.onConfirm(value)
    }

    commit(mutationTypes.setConfirmationDialog, getBaseConfirmationDialog())
  },

  [actionTypes.cancelConfirmationDialog] ({ commit, state }) {
    if (state.confirmationDialog.onCancel) {
      state.confirmationDialog.onCancel()
    }

    commit(mutationTypes.setConfirmationDialog, getBaseConfirmationDialog())
  },

  [actionTypes.setSidebar] ({ commit }, { show }) {
    commit(mutationTypes.setSidebar, { show })
  }
}

function getBaseConfirmationDialog () {
  return {
    open: false,
    title: '',
    text: '',
    hasInputValue: true,
    noWrap: false,
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
