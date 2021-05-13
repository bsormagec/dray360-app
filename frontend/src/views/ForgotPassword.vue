<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <h1>Forgot your password?</h1>
        <p>Enter your email address below, and we will send a link to reset your password.</p>
        <v-text-field
          v-model="email"
          class="email__input"
          label="Email"
          outlined
          dense
          :error="error"
          :error-messages="errorMessage"
          @focus="error = false"
        />
        <br>

        <div class="button_checkbox">
          <v-btn
            text
            small
            href="/login"
          >
            Cancel
          </v-btn>
          <v-btn
            class="btn-login"
            @click="forgotPassword"
          >
            Email
          </v-btn>
        </div>
        </v-text-field>
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
  name: 'ForgotPassword',

  data () {
    return {
      email: '',
      error: false,
      errorMessage: ''
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

    async forgotPassword () {
      this.loginError = false
      try {
        const response = await this.$store.dispatch('AUTH/ForgotPassword', { email: this.email })

        if (response.status === reqStatus.success) {
          this.setSnackbar({ message: 'Processing' })
          this.$router.push({ path: '/email-confirmation', query: { email: this.email } })
        } else {
          this.error = true
          this.errorMessage = response.errors.email
        }
      } catch (exception) {
        this.loginError = true
      }
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
        font-size: rem(26);
        font-weight: 700;
      }
      p { font-size: rem(12); }
      .v-messages__message{
        color: map-get($colors, red ) !important;
      }
      .email__input {
        width: 100%;
      }
      .email__input::v-deep label {
        font-size: rem(12);
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        font-weight: bold;
        width: 100%;
        margin-top: rem(20);
        .btn-login{
          padding: rem(5) rem(20);
          background-color: var(--v-primary-base);
          color: map-get($colors, white);
          border-radius: rem(3);
        }
        .btn-login::v-deep span {
          font-size: rem(12);
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
          font-size: rem(12);
        }
      }
  }
</style>
