<template>
  <div>
    <template>
      <v-toolbar
        flat
        color="white"
      >
        <v-toolbar-title>
          <h1 class="headline">
            Add User
          </h1>
        </v-toolbar-title>

        <v-spacer />
      </v-toolbar>
    </template>
    <template>
      <div class="form-field-element-input">
        <v-text-field
          v-model="name"
          data-cy="name-input"
          class="add-user-field"
          label="Name"
          placeholder="Name"
          type="input"
          outlined
        />
        <v-text-field
          v-model="email"
          data-cy="email-input"
          class="add-user-field"
          label="Email"
          placeholder="Email"
          type="input"
          outlined
        />
        <v-text-field
          v-model="password"
          data-cy="password-input"
          class="add-user-field"
          label="Password"
          placeholder="Password"
          type="password"
          outlined
        />
        <v-select
          v-if="hasPermission('roles-update')"
          v-model="role_selected"
          data-cy="roles-selector"
          class="add-user-field"
          label="User Role"
          item-text="display_name"
          item-value="id"
          :items="roles()"
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
      />
      <v-col
        cols="2"
        sm="2"
      >
        <v-btn
          name="add-user"
          data-cy="add-user-button"
          class="save-button button"
          @click="addUser()"
        >
          Add User
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
export default {

  mixins: [hasPermission],

  data: () => ({
    ...mapState(userDashboard.moduleName, {
      users: state => state.users,
      roles: state => state.roles
    }),

    name: '',
    email: '',
    password: '',
    org: '',
    role_selected: 2

  }),

  mounted () {
    this.fetchRoles()
  },

  methods: {
    ...mapActions(userDashboard.moduleName, [types.addUser, types.getRoles]),

    async addUser () {
      const userData = {
        name: this.name,
        email: this.email,
        password: this.password,
        role_id: this.role_selected
      }

      const status = await this[types.addUser](userData)

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }

      this.$router.push('/user/dashboard')
    },

    async fetchRoles () {
      const status = await this[types.getRoles]()

      if (status === reqStatus.successs) {
        console.log('success')
      } else {
        console.log('error')
      }
    }

  }
}
</script>
<style lang="scss" scoped>

.form-field-element-input {
  width: 44%
}

.add-user-field::v-deep input {
  font-size: rem(12);
}

.add-user-field::v-deep .v-label {
  font-size: rem(12);
}

h1.headline {
  color: rgba(map-get($colors , black-2), 1) !important;
  font-size: rem(26);
  font-weight: 700;
}
  .button {
    margin-left: rem(60);
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
</style>
