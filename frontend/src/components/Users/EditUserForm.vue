<template>
  <div>
    <template>
      <v-toolbar
        flat
        color="white"
      >
        <v-toolbar-title><h1>Edit User</h1></v-toolbar-title>

        <v-spacer />
      </v-toolbar>
    </template>
    <template>
      <div class="form-field-element-input">
        <v-text-field
          v-model="name"
          data-cy="name-input"
          label="Name"
          placeholder="Name"
          type="input"
          outlined
        />
        <v-text-field
          v-model="email"
          data-cy="email-input"
          label="Email"
          placeholder="Email"
          type="input"
          outlined
        />
        <v-select
          v-if="hasPermission('roles-update')"
          v-model="role_selected"
          data-cy="roles-selector"
          label="User Role"
          item-text="display_name"
          item-value="id"
          :items="roles"
          solo
          dense
          persistent-hint
        />
      </div>
    </template>

    <v-row>
      <v-col
        cols="4"
        sm="4"
      >
        <v-btn
          class="delete-button button"
          outlined
          @click="deleteUser()"
        >
          Delete
        </v-btn>
        <v-btn
          v-if="getActivationState()"
          class="secondary-button button"
          outlined
          @click="deactivateUser()"
        >
          Deactivate
        </v-btn>
        <v-btn
          v-if="!getActivationState()"
          class="secondary-button button"
          outlined
          @click="activateUser()"
        >
          Activate
        </v-btn>
      </v-col>
      <v-col
        cols="2"
        sm="2"
      >
        <v-btn
          class="cancel-button button"
          outlined
          @click="$router.push('/user/dashboard')"
        >
          Cancel
        </v-btn>
        <v-btn
          data-cy="save-button"
          class="save-button button"
          @click="editUser()"
        >
          Save
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import userDashboard, { types } from '@/store/modules/users'
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import hasPermission from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import { getUser } from '@/store/api_calls/users'

export default {

  mixins: [hasPermission],

  data: () => ({
    name: '',
    email: '',
    company: '',
    role_selected: 1,
    activated: true,
    editedUser: ''
  }),

  computed: {
    ...mapState(userDashboard.moduleName, {
      roles: state => state.roles
    })
  },

  mounted () {
    this.getUserByID(this.$route.params.id)
    this.fetchRoles()
    this.showSideBar()
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.editUser, types.getRoles, types.changeUserStatus, types.deleteUser]),
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setSidebar]),

    async getUserByID (userId) {
      const [error, data] = await getUser(userId)

      if (error !== undefined) {
        console.log(error)
      } else {
        this.editedUser = data
        this.name = this.editedUser.name
        this.email = this.editedUser.email
        this.role_selected = this.editedUser.roles[0].id
      }
    },

    async showSideBar () {
      await this[type.setSidebar]({
        show: true
      })
    },

    getActivationState () {
      if (this.editedUser) {
        if (this.editedUser.deactivated_at != null) {
          return false
        } else {
          return true
        }
      }
    },

    async editUser () {
      const userId = this.$route.params.id

      const userData = {
        name: this.name,
        email: this.email,
        role_id: this.role_selected,
        user_id: userId
      }

      let message = ''

      const status = await this[types.editUser](userData)

      if (status === reqStatus.success) {
        message = 'User updated'
        this.$router.push('/user/dashboard')
      } else {
        message = 'An error has ocurred'
      }

      await this[type.setSnackbar]({ show: true, message })
    },

    async fetchRoles () {
      await this[types.getRoles]()
    },

    async activateUser () {
      const userId = this.$route.params.id

      const payload = {
        userId: userId,
        newStatus: {
          active: true
        }
      }

      const status = await this[types.changeUserStatus](payload)

      if (status === reqStatus.successs) {
        console.log('success')
      } else {
        console.log('error')
      }

      this.$router.push('/user/dashboard')
    },

    async deactivateUser () {
      const userId = this.$route.params.id

      const payload = {
        userId: userId,
        newStatus: {
          active: false
        }
      }

      const status = await this[types.changeUserStatus](payload)

      if (status === reqStatus.successs) {
        console.log('success')
      } else {
        console.log('error')
      }

      this.$router.push('/user/dashboard')
    },

    async deleteUser () {
      const userId = this.$route.params.id

      const status = await this[types.deleteUser](userId)

      if (status === reqStatus.success) {
        console.log('delete user success')
      } else {
        console.log('error')
      }

      this.$router.push('/user/dashboard')
    }
  }
}
</script>
<style lang="scss" scoped>

  h1 {
    color: rgba(map-get($colors , black-2), 1) !important;
    font-size: rem(26);
    letter-spacing: 0;
    font-weight: 700;
  }
  .form-field-element-input {
    width: 44%
  }
  .button {
    margin-right: rem(10);
    letter-spacing: rem(.75);
  }
  .delete-button {
    color: rgba(map-get($colors , red), 1) !important;
  }
  .secondary-button {
    color: rgba(map-get($colors, secondary-blue), 1) !important;
  }
  .save-button {
    color: rgba(map-get($colors , white), 1) !important;
    background-color: rgba(map-get($colors , secondary-blue), 1) !important;
  }
  .cancel-button {
    color: rgba(map-get($colors , secondary-blue), 1) !important;
    border: unset !important;
  }
  .v-input::v-deep .v-label,
  .v-input::v-deep input {
    font-size: rem(12);
  }
</style>
