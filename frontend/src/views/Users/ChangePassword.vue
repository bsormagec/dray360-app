<template>
  <div class="pa-4">
    <v-row>
      <v-col md="4">
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
      </v-col>
    </v-row>
    <v-row>
      <v-col md="4">
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
      </v-col>
    </v-row>
    <v-row>
      <v-col md="4">
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
      </v-col>
    </v-row>
    <v-row>
      <v-col
        md="4"
        class="d-flex justify-space-between"
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
import utils, { actionTypes } from '@/store/modules/utils'

export default {
  data () {
    return {
      old_password: '',
      password: '',
      password_confirmation: '',
      error: false
    }
  },

  methods: {
    ...mapActions(profile.moduleName, [types.changePassword]),
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),
    async save () {
      const status = await this[types.changePassword](
        {
          old_password: this.old_password,
          password: this.password,
          password_confirmation: this.password_confirmation
        }
      )
      if ('errors' in status) {
        await this.setSnackbar({ message: status.message })
      } else {
        await this.setSnackbar({ message: 'Password changed' })
        this.$router.push({ path: '/user/edit-profile/' })
      }
    }
  }

}
</script>
<style lang="scss" scoped>
.password-change__label {
  display: flex;
  align-items: center;
  margin-bottom: rem(8);
  .v-btn {
    min-width: unset;
    margin-right: rem(8);
  }
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
