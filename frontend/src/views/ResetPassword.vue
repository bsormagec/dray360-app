<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <h1>Reset your password</h1>

        <v-text-field
          v-model="new_password"
          class="password__input"
          type="password"
          label="password"
          outlined
          dense
          :error="error"
          :error-messages="errorPassword"
          @focus="change"
        />

        <v-text-field
          v-model="password_confirmation"
          class="password__input"
          type="password"
          label="Password Confirmation"
          outlined
          dense
          :error="error"
          :error-messages="TokenError"
          @focus="change"
        />

        <div class="button_checkbox">
          <v-btn
            class="btn-login"
            @click="ResetPassword"
          >
            Email
          </v-btn>
        </div>
      </div>

      <div class="copyright">
        <p>©2020 Dray360 — an Affiliate of TCompanies Inc. Privacy Policy • Terms of Use</p>
      </div>
    </div>
  </div>
</template>
<script>

import utils, { actionTypes } from '@/store/modules/utils'
import { mapActions, mapState } from 'vuex'
import { reqStatus } from '@/enums/req_status'

export default {
  name: 'ResetPassword',

  data () {
    return {
      new_password: '',
      password_confirmation: '',
      error: false,
      TokenError: '',
      errorPassword: []
    }
  },
  computed: {
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig
    })
  },

  async created () {
    await this[actionTypes.getTenantConfig]()
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.getTenantConfig, actionTypes.setSnackbar]),
    async ResetPassword () {
      const response = await this.$store.dispatch('AUTH/PasswordReset', {
        token: this.$route.params.id,
        email: this.$route.query.email,
        password: this.new_password,
        password_confirmation: this.password_confirmation
      })

      if (response.status === reqStatus.success) {
        this.setSnackbar({ message: 'Your password has been reset!' })
        this.$router.push({ path: '/login' })
      } else {
        this.error = true
        if (response.errors.password !== undefined && response.errors.password.length > 0) {
          this.errorPassword = response.errors.password
        } else if (response.errors.email !== undefined && response.errors.email.length > 0) {
          this.TokenError = response.errors.email
        }
      }
    },
    change () {
      this.errorPassword = ''
      this.TokenError = ''
      this.error = false
    }
  }
}
</script>
<style lang="scss" scoped>
  $background_login: url("../assets/images/login_background.png");

  .wrapper{
    background: $background_login no-repeat center center fixed;
    background-size: cover;
    display: flex;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    .login-box{
      width: rem(360);
      justify-items: center;
      background-color: map-get($colors, white );
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      border-top: rem(8) solid var(--v-primary-base) !important;
      border-left: rem(1) solid map-get($colors, gray );
      border: rem(1) solid map-get($colors, gray );
      box-shadow: 0 rem(1) rem(3) rgba(0, 0, 0, 0.1);
      padding: rem(30);
      h1{
        align-self: flex-start;
        margin-bottom: rem(7);
        color: var(--v-primary-base);
      }
      .v-input > div{
        color: map-get($colors, red ) !important
      }

      .password__input{
        width: 100%;
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-end;
        font-weight: bold;
        width: 100%;
        margin-top: rem(20);
        .btn-login{
          padding: rem(5) rem(20);
          background-color: var(--v-primary-base);
          color: map-get($colors, white);
          border-radius: rem(3);
        }
      }
    }
    .account{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        width: rem(360);
        border-top: rem(1) solid map-get($colors , gray ) !important;
        border: rem(1) solid map-get($colors, gray );
        box-shadow: 0 rem(1) rem(3) rgba(0, 0, 0, 0.1);
        background-color: map-get($colors,white);
        padding-top: rem(2);
        padding: rem(10) rem(10) 0 rem(10);
      }
      .copyright{
        position: absolute;
        bottom: rem(20);
        p{
          color: map-get($colors, grey-9 );
          line-height: rem(16);
        }
      }
  }
</style>
