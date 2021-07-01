<template>
  <v-container
    v-if="!has404"
    fluid
  >
    <v-row no-gutters>
      <v-col md="4">
        <v-text-field
          v-model="name"
          data-cy="name-input"
          label="Name"
          placeholder="Name"
          type="input"
          outlined
          dense
          autocomplete="off"
        />
        <v-text-field
          v-model="email"
          data-cy="email-input"
          label="Email"
          placeholder="Email"
          type="input"
          outlined
          dense
          autocomplete="off"
        />
        <v-text-field
          v-if="add"
          v-model="password"
          data-cy="password-input"
          label="Password"
          placeholder="Password"
          type="password"
          outlined
          dense
          autocomplete="off"
        />
        <v-select
          v-if="hasPermission('roles-update')"
          v-model="role_selected"
          data-cy="roles-selector"
          label="User Role"
          item-text="display_name"
          item-value="id"
          :items="roles"
          outlined
          dense
          persistent-hint
        />
        <v-select
          v-if="canViewOtherCompanies()"
          v-model="companyId"
          data-cy="roles-selector"
          label="Company"
          item-text="name"
          item-value="id"
          :items="companies"
          outlined
          dense
          persistent-hint
        />
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col md="4">
        <v-row no-gutters>
          <v-col
            v-if="edit"
            md="6"
          >
            <v-btn
              color="error"
              class="mr-4 mb-2"
              outlined
              @click="deleteUser"
            >
              Delete
            </v-btn>
            <v-btn
              :loading="loading"
              outlined
              color="primary"
              class="mb-2"
              @click="toggleUserStatus"
            >
              {{ userIsActive ? 'Deactivate' : 'Activate' }}
            </v-btn>
          </v-col>
          <v-col
            :cols="edit? '6' : '12'"
            class="d-flex justify-end"
          >
            <v-btn
              color="primary"
              class="mr-4"
              text
              @click="$router.push('/user/dashboard')"
            >
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              data-cy="save-button"
              :loading="loading"
              @click="() => { this.edit ? this.editUser() : this.addUser() }"
            >
              {{ this.edit ? 'Save' : 'Add' }}
            </v-btn>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
  <ContainerNotFound
    v-else
    container-type="USER"
  />
</template>
<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'
import { getUser, getRoles, changeUserStatus, editUser, deleteUser, addUser } from '@/store/api_calls/users'
import ContainerNotFound from '@/views/ContainerNotFound'

import get from 'lodash/get'

export default {
  name: 'UserForm',

  components: {
    ContainerNotFound,
  },

  mixins: [permissions, allCompanies],

  props: {
    edit: {
      type: Boolean,
      required: false,
      default: false
    },
    add: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data: () => ({
    name: '',
    email: '',
    company: '',
    password: '',
    companyId: null,
    role_selected: null,
    activated: true,
    roles: [],
    user: {
      id: undefined,
      t_company_id: null,
      deactivated_at: null
    },
    loading: false,
    has404: false
  }),

  computed: {
    userIsActive () {
      return get(this.user, 'deactivated_at', null) === null
    }
  },

  beforeMount () {
    this.fetchRoles()
    if (this.canViewOtherCompanies()) {
      this.fetchCompanies()
    }
    if (this.edit) {
      this.getUserById(this.$route.params.id)
    }
  },

  methods: {
    ...mapActions(utils.moduleName, {
      setConfirmDialog: actionTypes.setConfirmationDialog
    }),
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),

    async getUserById (userId) {
      const [error, data] = await getUser(userId)

      if (error !== undefined) {
        this.has404 = true
        console.log(error)
        return
      }

      this.user = data
      this.name = this.user.name
      this.email = this.user.email
      this.companyId = this.user.t_company_id
      this.role_selected = get(this.user, 'roles.0.id', null)
    },

    async editUser () {
      const data = {
        name: this.name,
        email: this.email,
        role_id: this.role_selected
      }

      if (this.canViewOtherCompanies()) {
        data.company_id = this.companyId
      }

      let message = ''

      const [error] = await editUser(this.user.id, data)

      if (error === undefined) {
        message = 'User updated'
        this.$router.push('/user/dashboard')
      } else {
        message = 'An error has ocurred'
      }

      await this.setSnackbar({ message })
    },

    async addUser () {
      const data = {
        name: this.name,
        email: this.email,
        role_id: this.role_selected,
        password: this.password
      }

      if (this.canViewOtherCompanies()) {
        data.company_id = this.companyId
      }

      let message = ''

      const [error] = await addUser(data)

      if (error === undefined) {
        message = 'User Added'
        this.$router.push('/user/dashboard')
      } else {
        message = 'An error has ocurred'
      }

      await this.setSnackbar({ message })
    },

    async fetchRoles () {
      const [error, data] = await getRoles()

      if (error !== undefined) {
        return
      }

      this.roles = data.data
    },

    async toggleUserStatus () {
      const active = !this.userIsActive
      this.loading = true
      const [error] = await changeUserStatus(this.user.id, { active })
      this.loading = false

      if (error !== undefined) {
        await this.setSnackbar({ message: 'There was an error in the server, please try again.' })
        return
      }

      this.user.deactivated_at = active ? null : 'inactive'
    },

    async deleteUser () {
      this.setConfirmDialog({
        title: 'Are you sure you want to delete this user?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await deleteUser(this.user.id)

          let message = ''
          if (error === undefined) {
            message = 'User deleted'
            this.$router.push('/user/dashboard')
          } else {
            message = 'An error has ocurred'
          }

          await this.setSnackbar({ message })
          this.loading = false
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.user-edit__heading {
    display: flex;
    align-items: center;
    margin-bottom: rem(8);
    .v-btn {
      min-width: unset;
      margin-right: rem(8);
    }
  }
</style>
