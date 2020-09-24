<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <h1>Reset your password</h1>

        <input
          v-model="new_password"
          type="password"
          name="username"
          placeholder="New password"
        >
        <br>
        <input
          v-model="password_confirmation"
          type="password"
          name="username"
          placeholder="Password Confirmation"
        >

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

import utils, { type } from '@/store/modules/utils'
import { mapActions, mapState } from '@/utils/vuex_mappings'
import auth from '@/store/modules/auth'
import { reqStatus } from '@/enums/req_status'

export default {
  name: 'ResetPassword',

  data () {
    return {
      new_password: '',
      password_confirmation: ''
    }
  },
  computed: {
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig
    })
  },

  async created () {
    console.log('id', this.$route)
    await this[type.getTenantConfig]()
  },

  methods: {
    ...mapActions(utils.moduleName, [type.getTenantConfig, type.setSnackbar]),
    async ResetPassword () {
      try {
        const response = await this.$store.dispatch('AUTH/PasswordReset', {
          token: this.$route.params.id,
          email: this.$route.query.email,
          password: this.new_password,
          password_confirmation: this.password_confirmation
        })
        if (response.status === reqStatus.success) {
          await this[type.setSnackbar]({
            show: true,
            showSpinner: false,
            message: 'Your password has been reset!'
          })
          this.$router.push({ path: '/login' })
        } else {
          await this[type.setSnackbar]({
            show: true,
            showSpinner: false,
            message: 'A problem has Ocurred'
          })
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
      width: 36rem;
      justify-items: center;
      background-color: map-get($colors, white );
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      border-top: 0.8rem solid var(--v-primary-base) !important;
      border-left: 0.1rem solid map-get($colors, gray );
      border: 0.1rem solid map-get($colors, gray );
      box-shadow: 0rem 0.1rem 0.3rem rgba(0, 0, 0, 0.1);
      padding: 3rem;
      h1{
        align-self: flex-start;
        margin-bottom: 0.7rem;
        color: var(--v-primary-base);
      }
      .text__error{
        color: map-get($colors, red );
      }
      input{
        border: 0.1rem solid lightgray;
        margin: 0.5rem auto;
        padding: 0.5rem 6.5rem 0.5rem 9rem;
        border-radius: 0.5rem;
        max-width: 50rem;
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-end;
        font-weight: bold;
        width: 100%;
        margin-top: 2rem;
        .btn-login{
          padding: 0.5rem 2rem;
          background-color: var(--v-primary-base);
          color: map-get($colors, white);
          border-radius: 0.3rem;
        }
      }
    }
    .account{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        width: 36rem;
        border-top: 0.1rem solid map-get($colors , gray ) !important;
        border: 0.1rem solid map-get($colors, gray );
        box-shadow: 0rem 0.1rem 0.3rem rgba(0, 0, 0, 0.1);
        background-color: map-get($colors,white);
        padding-top: 0.2rem;
        padding: 1rem 1rem 0rem 1rem;
      }
      .copyright{
        position: absolute;
        bottom: 2rem;
        p{
          color: map-get($colors, grey-9 );
          line-height: 1.6rem;
        }
      }
  }
</style>
