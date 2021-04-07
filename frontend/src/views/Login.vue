<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <img
          v-if="!tenantConfig.logo1"
          class="logo"
          src="../assets/images/envase-order-ai-2.png"
          alt=""
        >
        <img
          v-else
          class="logo"
          :src="tenantConfig.logo1"
          alt=""
        >
        <v-text-field
          v-model="email"
          class="login__input"
          outlined
          dense
          type="text"
          name="username"
          label="Email"
          placeholder=" "
          @focus="error = false"
          @keypress.enter.stop="login()"
        />
        <v-text-field
          v-model="password"
          class="login__input"
          outlined
          dense
          type="password"
          name="password"
          label="Password"
          placeholder=" "
          @focus="error = false"
          @keypress.enter.stop="login()"
        />
        <p
          v-if="error"
          class="text__error"
        >
          {{ errorMessage }}
        </p>
        <div class="button_checkbox">
          <v-checkbox
            v-model="disabled"
            class="remember-me-check mx-2"
            label="Remember me"
          />
          <button
            type="button"
            class="btn-login"
            @click="login()"
          >
            Sign In
          </button>
        </div>
      </div>
      <div class="account">
        <p>
          <span><a
            href="/forgot-password"
          >Forgot your password?</a></span>
        </p>
        <!-- <p><span><a href="">Don't have an account?</a></span></p> -->
      </div>
      <br><div v-if="loginError">
        There was a problem. Please check your email and password.
      </div>
      <div class="copyright">
        <p>©2020 Dray360 — an Affiliate of TCompanies Inc.</p>
      </div>
    </div>
  </div>
</template>
<script>
import utils, { type } from '@/store/modules/utils'
import { mapActions, mapState, mapGetters } from 'vuex'
import auth from '@/store/modules/auth'
import { reqStatus } from '@/enums/req_status'

export default {
  name: 'Login',

  data () {
    return {
      disabled: false,
      error: false,
      errorMessage: '',
      email: '',
      password: '',
      loginError: false,
      fields_email: { name: 'email', label: 'Email', placeholder: 'Email', el: { value: '' } },
      fields_password: { name: 'Password', type: 'password', label: 'Password', placeholder: 'Password', el: { value: '' } }
    }
  },
  computed: {
    ...mapGetters(auth.moduleName, ['loggedIn']),
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig
    }),
  },
  async created () {
    await this.getTenantConfig()
  },
  beforeMount () {
    this.setSidebar({ show: false })
  },
  mounted () {
    if (this.loggedIn) this.$router.push('/inbox')
  },

  methods: {
    ...mapActions(utils.moduleName, {
      getTenantConfig: type.getTenantConfig,
      setSidebar: type.setSidebar,
    }),
    async login () {
      this.loginError = false
      try {
        const response = await this.$store.dispatch('AUTH/login', { email: this.email, password: this.password })
        if (response.status === reqStatus.success) {
          this.error = false
          if (this.$store.state.AUTH.intendedUrl === undefined) {
            this.$router.push('/inbox')
          } else {
            this.$router.push(this.$store.state.AUTH.intendedUrl)
          }
        } else {
          this.errorMessage = response.message
          this.error = true
          console.log('error')
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

  .wrapper::v-deep{
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
      .text__error{
        color: map-get($colors, red );
        font-size: rem(12);
      }
      .logo{
        width: rem(210);
        margin-bottom: rem(40);
        margin-top: rem(10);
      }
      .login__input input{
        border-radius: rem(5);
        max-width: rem(250);
        min-width: rem(250);
        font-size: rem(12);
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
        font-weight: bold;
        width: 100%;
        .btn-login{
          padding: rem(5) rem(40);
          background-color: var(--v-primary-base);
          color: map-get($colors, white);
          border-radius: rem(3);
          font-size: rem(12);
        }
      }
    }
    .remember-me-check .v-label{
      font-size: rem(12);
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

        a { font-size: rem(12); }
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
