<template>
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
            data-cy="old-password-field"
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
            data-cy="password-field"
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
            data-cy="password-confirmation-field"
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
          @click="$router.push({ path: '/user/edit-profile/' })"
        >
          Cancel
        </v-btn>
        <v-btn
          data-cy="save-button"
          class="button"
          color="primary"
          @click="save"
        >
          Save
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import profile, { types } from '@/store/modules/profile'
import { mapActions } from 'vuex'
import utils, { type } from '@/store/modules/utils'

export default {

  data () {
    return {
      old_password: '',
      password: '',
      password_confirmation: '',
      error: false
    }
  },
  beforeMount () {
    this.showSidebar()
  },
  methods: {
    ...mapActions(profile.moduleName, [types.changePassword]),
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setSidebar]),

    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    },
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
        this.$router.push({ path: '/user/edit-profile/' })
      }
    }
  }

}
</script>
<style lang="scss" scoped>
.v-toolbar__title h1 {
  font-size: rem(26);
  font-weight: 700;
  letter-spacing: 0;
}
input {
  font-size: rem(12);
  border: rem(1) solid lightgray;
  margin: rem(5) auto;
  padding: rem(5) rem(65);
  border-radius: rem(5);

}
.button {
  margin-right: rem(10);
  letter-spacing: rem(.7);
}
.v-input::v-deep input {
  font-size: rem(12);
}
</style>
