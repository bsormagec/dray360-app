<template>
  <div class="pa-5">
    <template>
      <v-row>
        <v-col md="6">
          <v-text-field
            v-model="name"
            data-cy="name-field"
            class="profile-field"
            name="First Name"
            label="First Name"
            hide-details
            outlined
            dense
          />
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6">
          <v-text-field
            v-model="email"
            data-cy="email-field"
            class="profile-field"
            name="email"
            label="Email"
            hide-details
            outlined
            dense
          />
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6">
          <v-text-field
            v-model="position"
            data-cy="position-field"
            class="profile-field"
            name="position"
            label="Position"
            hide-details
            outlined
            dense
          />
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6">
          <v-text-field
            v-model="org"
            data-cy="org-field"
            class="profile-field"
            name="org"
            label="Org"
            hide-details
            outlined
            dense
          />
        </v-col>
      </v-row>
    </template>

    <v-row>
      <v-col md="3">
        <v-btn
          data-cy="change-password-button"
          class="button"
          text
          color="primary"
          @click="$router.push({path: '/user/dashboard/change-password/'})"
        >
          Change Password
        </v-btn>
      </v-col>
      <v-col
        md="3"
        class="d-flex justify-end"
      >
        <v-btn
          class="button"
          color="primary"
          text
          @click="$router.push({ path: '/inbox' })"
        >
          Cancel
        </v-btn>
        <v-btn
          class="button"
          color="primary"
          data-cy="save-button"
          @click="save"
        >
          Save
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>

import { mapActions, mapState } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import utils, { actionTypes } from '@/store/modules/utils'
import users, { types } from '@/store/modules/users'
import auth from '@/store/modules/auth'

export default {
  data () {
    return {
      name: '',
      email: '',
      position: '',
      org: ''
    }
  },
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser })
  },

  mounted () {
    this.getUserData()
  },
  methods: {
    ...mapActions(users.moduleName, [types.editUser]),
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),
    ...mapActions(auth.moduleName, ['getCurrentUser']),
    getUserData () {
      if (this.currentUser) {
        this.name = this.currentUser.name
        this.email = this.currentUser.email
        this.position = this.currentUser.position
        this.org = this.currentUser.org
      }
    },

    async save () {
      const userData = {
        name: this.name,
        email: this.email,
        position: this.position,
        org: this.org,
        user_id: this.currentUser.id
      }
      const status = await this[types.editUser](userData)
      let message = ''

      if (status === reqStatus.success) {
        message = 'Profile updated'
        await this.getCurrentUser(true)
      } else {
        message = 'An error has occurred, please contact to technical support'
      }
      this.setSnackbar({ message })
    }
  }

}
</script>
<style lang="scss" scoped>
.edit-profile-headline {
  display: flex;
  align-items: center;
  margin-bottom: rem(8);
  .v-btn {
    min-width: unset;
    margin-right: rem(8);
  }
}
.profile-field::v-deep label, input[type="text"] {
  font-size: rem(12);
}
.button::v-deep span {
  font-size: rem(12);
}
</style>
