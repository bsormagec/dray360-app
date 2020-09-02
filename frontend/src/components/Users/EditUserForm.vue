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
          label="Name"
          placeholder="Name"
          type="input"
          outlined
        />
        <v-text-field
          v-model="email"
          label="Email"
          placeholder="Email"
          type="input"
          outlined
        />
        <v-text-field
          v-model="password"
          label="Password"
          placeholder="Password"
          type="password"
          outlined
        />
        <v-select
          v-model="role_selected"
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
        >
          Delete
        </v-btn>
        <v-btn
          class="secondary-button button"
          outlined
        >
          Deactivate
        </v-btn>
      </v-col>
      <v-col
        cols="2"
        sm="2"
      >
        <v-btn
          class="cancel-button button"
          outlined
        >
          Cancel
        </v-btn>
        <v-btn
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
import FormField from '@/components/FormField/FormField'
import userDashboard, { types } from '@/store/modules/users'
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
export default {
  data: () => ({
    name: '',
    email: '',
    password: '',
    org: '',
    role_selected: 1,
    roles: [1, 2]

  }),

  methods: {
    ...mapActions(userDashboard.moduleName, [types.editUser]),

    async editUser () {
      const userId = this.$route.params.id

      const userData = {
        name: this.name,
        email: this.email,
        role_id: this.role_selected,
        user_id: userId
      }

      const status = await this[types.editUser](userData)

      if (status === reqStatus.success) {
        console.log('success')
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
  }
  .form-field-element-input {
    width: 44%
  }
  .button {
    margin-right: 1rem;
    letter-spacing: 0.075rem;
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
