<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <div>
        <template>
          <v-toolbar
            flat
            color="white"
          >
            <v-toolbar-title>
              <h1>
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
                name="org"
                label="Org"
                hide-details
                outlined
                dense
              />
            </div>
          </div>
        </template>

        <div class="row">
          <div class="col-4">
            <v-btn
              class="button"
              text
              color="primary"
              @click="$router.push({path: '/user/dashboard/change-password/'})"
            >
              Change Password
            </v-btn>
          </div>
          <div class="col-2 d-flex justify-end">
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
              @click="save"
            >
              Save
            </v-btn>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import { mapActions, mapState } from 'vuex'
import utils, { type } from '@/store/modules/utils'
import users, { types } from '@/store/modules/users'
import auth from '@/store/modules/auth'

export default {
  components: {
    SidebarNavigation
  },
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
    this.retrieveUser()
  },
  methods: {
    ...mapActions(users.moduleName, [types.editUser]),
    ...mapActions(utils.moduleName, [type.setSnackbar]),

    retrieveUser () {
      this.name = this.currentUser.name
      this.email = this.currentUser.email
      this.position = this.currentUser.position
      this.org = this.currentUser.org
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
<style lang="sass" scoped>

</style>
