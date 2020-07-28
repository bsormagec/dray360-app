<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <img
          class="logo"
          src="../assets/images/dry360_logo.svg"
          alt=""
        >
        <input
          v-model="email"
          type="text"
          name="username"
          placeholder="Email"
          @focus="error = false"
        >
        <br>
        <input
          v-model="password"
          type="password"
          name="password"
          placeholder="Password"
          @focus="error = false"
        >
        <p
          v-if="error"
          class="text__error"
        >
          Wrong username or password
        </p>
        <div class="button_checkbox">
          <v-checkbox
            v-model="disabled"
            class="mx-2"
            label="Remember me"
          />
          <button
            type="button"
            class="btn-login"
            @click="login()"
          >
            Login
          </button>
        </div>
      </div>
      <div class="account">
        <p><span><a href="">Forgot your password?</a></span></p>
        <p><span><a href="">Don't have an account?</a></span></p>
      </div>
      <br><div v-if="loginError">
        There was a problem. Please check your email and password.
      </div>
      <div class="copyright">
        <p>©2020 Dray360 — an Affiliate of TCompanies Inc. Privacy Policy • Terms of Use</p>
      </div>
    </div>
  </div>
</template>
<script>
// import axios from '@/store/api_calls/axios_config'
import { mapState } from '@/utils/vuex_mappings'
import auth from '@/store/modules/auth'
import { reqStatus } from '@/enums/req_status'

export default {
  name: 'Login',

  data () {
    return {
      ...mapState(auth.moduleName, {
        loggedIn: state => state.loggedIn
      }),
      disabled: false,
      error: false,
      email: '',
      password: '',
      loginError: false,
      fields_email: { name: 'email', label: 'Email', placeholder: 'Email', el: { value: '' } },
      fields_password: { name: 'Password', type: 'password', label: 'Password', placeholder: 'Password', el: { value: '' } }
    }
  },

  mounted () {
    if (this.loggedIn()) this.$router.push('/dashboard/')
  },

  methods: {
    async login () {
      if (this.email !== '' && this.password !== '') {
        this.loginError = false
        try {
          const status = await this.$store.dispatch('AUTH/login', { email: this.email, password: this.password })
          if (status === reqStatus.success) {
            this.error = false
            if (this.$store.state.AUTH.intendedUrl === undefined) {
              this.$router.push('/dashboard/')
            } else {
              this.$router.push(this.$store.state.AUTH.intendedUrl)
            }
          } else {
            this.error = true
            console.log('error')
          }
        } catch (exception) {
          this.loginError = true
        }
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
      border-top: 0.8rem solid map-get($colors, blue ) !important;
      border-left: 0.1rem solid map-get($colors, gray );
      border: 0.1rem solid map-get($colors, gray );
      box-shadow: 0rem 0.1rem 0.3rem rgba(0, 0, 0, 0.1);
      padding: 3rem;
      .text__error{
        color: map-get($colors, red );
      }
      .logo{
        width: 20rem;
        margin-bottom: 5rem;
      }
      input{
        border: 0.1rem solid lightgray;
        margin: 0.5rem auto;
        padding: 0.5rem 6.5rem;
        border-radius: 0.5rem;
        max-width: 25rem;
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        font-weight: bold;
        width: 100%;
        .btn-login{
          padding: 0.5rem 4rem;
          background-color: map-get($colors, blue);
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
