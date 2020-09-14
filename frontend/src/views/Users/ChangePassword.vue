<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation :menu-items="[]" />
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
                Change Password
              </h1>
            </v-toolbar-title>

            <v-spacer />
          </v-toolbar>
        </template>
        <template
          style="width: 44%"
        >
          <div class="row">
            <div class="col-4">
              <v-text-field
                v-model="old_password"
                type="password"
                name="password"
                label="Old Password"
                hide-details
                outlined
                dense
              />
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <v-text-field
                v-model="password"
                type="password"
                name="password"
                label="Password"
                hide-details
                outlined
                dense
              />
            </div>
          </div>
          <div class="row">
            <div class="col-4">
              <v-text-field
                v-model="password_confirmation"
                type="password"
                name="password"
                label="Password Confirmation"
                hide-details
                outlined
                dense
              />
            </div>
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
              class="button"
              color="primary"
              text
              @click="$router.push({ path: '/user/dashboard/edit-profile/' })"
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
          </v-col>
        </v-row>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import users, { types } from '@/store/modules/users'
import { mapActions } from '@/utils/vuex_mappings'
import utils, { type } from '@/store/modules/utils'

export default {
  components: {
    SidebarNavigation
  },
  data () {
    return {
      old_password: '',
      password: '',
      password_confirmation: '',
      error: false
    }
  },
  methods: {
    ...mapActions(users.moduleName, [types.changePassword]),
    ...mapActions(utils.moduleName, [type.setSnackbar]),

    async save () {
      const status = await this[types.changePassword](
        {
          old_password: this.old_password,
          password: this.password,
          password_confirmation: this.password_confirmation
        }
      )
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
          message: 'Password changed'
        })
        this.$router.push({ path: '/user/dashboard/edit-profile/' })
      }
    }
  }

}
</script>
<style lang="scss" scoped>
input{
        border: 0.1rem solid lightgray;
        margin: 0.5rem auto;
        padding: 0.5rem 6.5rem;
        border-radius: 0.5rem;

      }
  .button {
    margin-right: 1.0rem;
    letter-spacing: 0.07rem;
  }
</style>
