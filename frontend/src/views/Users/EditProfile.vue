<template>
  <div class="pa-5">
    <template>
      <v-toolbar
        flat
        color="white"
      >
        <v-toolbar-title>
          <h1 class="edit-profile-headline">
            Edit Profile
          </h1>
        </v-toolbar-title>

        <v-spacer />
      </v-toolbar>
    </template>
    <template>
      <div class="row">
        <div class="col-6">
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
        </div>
      </div>
      <div class="row">
        <div class="col-6">
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
        </div>
      </div>
      <div class="row">
        <div class="col-6">
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
        </div>
      </div>
      <div class="row">
        <div class="col-6">
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
        </div>
      </div>
    </template>

    <v-row>
      <v-col>
        <v-btn
          class="button"
          text
          color="primary"
          @click="$router.push({path: '/user/dashboard/change-password/'})"
        >
          Change Password
        </v-btn>
      </v-col>
      <v-col>
        <v-btn
          class="button"
          color="primary"
          text
          @click="$router.push({ path: '/dashboard/' })"
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
import utils, { type } from '@/store/modules/utils'
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
  beforeMount () {
    if (!this.isMobile) {
      this.showSidebar()
    }
  },
  mounted () {
    this.retrieveUser()
  },
  methods: {
    ...mapActions(users.moduleName, [types.editUser]),
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setSidebar]),

    retrieveUser () {
      this.name = this.currentUser.name
      this.email = this.currentUser.email
      this.position = this.currentUser.position
      this.org = this.currentUser.org
    },
    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
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
      if ('errors' in status) {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: status.message
        })
      } else {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'Profile updated'
        })
      }
    }

  }

}
</script>
<style lang="scss" scoped>
  .edit-profile-headline {
    font-size: rem(26);
    font-weight: 700;
    letter-spacing: 0;
  }
  .profile-field::v-deep label, input[type="text"] {
    font-size: rem(12);
    // label {
    //   font-size: rem(12);
    // }
    // input {
    //   font-size: rem(12);
    // }
  }
  .button::v-deep span {
    font-size: rem(12);
  }
</style>
