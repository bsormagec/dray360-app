<template>
  <div class="pa-4">
    <h6 class="password-change__label">
      <SidebarNavigationButton :dark="false" />
      Change Password
    </h6>
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
import SidebarNavigationButton from '@/components/General/SidebarNavigationButton'
import profile, { types } from '@/store/modules/profile'
import { mapActions } from 'vuex'
import utils, { type } from '@/store/modules/utils'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'

export default {
  components: { SidebarNavigationButton },
  mixins: [isMobile, isMedium],
  data () {
    return {
      old_password: '',
      password: '',
      password_confirmation: '',
      error: false
    }
  },
  watch: {
    isMedium: function (newVal, oldVal) {
      if (!newVal) this.setSidebar({ show: true })
    },
    isMobile: function (newVal, oldVal) {
      if (newVal) this.setSidebar({ show: false })
      else this.setSidebar({ show: true })
    }
  },

  beforeMount () {
    if (!this.isMobile) return this.setSidebar({ show: true })
    return this.setSidebar({ show: false })
  },
  methods: {
    ...mapActions(profile.moduleName, [types.changePassword]),
    ...mapActions(utils.moduleName, { setSidebar: type.setSidebar }),
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
