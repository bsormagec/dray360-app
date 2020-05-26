<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <img
          class="logo"
          src="../assets/images/2x@Cushing-Logo-BW.png"
          alt=""
        >
        <FormFieldElementInput
          :field="fields_email"
          @change="e =>(email = e)"
        />
        <FormFieldElementInput
          :field="fields_password"
          @change="e =>(password = e)"
        />
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
        <p><span class=""><a href="">Forgot your password?</a></span></p>
        <p><span><a href="">Don't have an account?</a></span></p>
      </div>
      <br><div v-if="loginError">
        There was a problem. Please check your email and password.
      </div>
    </div>
  </div>
</template>
<script>
// import axios from '@/store/api_calls/axios_config'
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
export default {
  name: 'Login',
  components: {
    FormFieldElementInput
  },
  data () {
    return {
      disabled: false,
      email: '',
      password: '',
      loginError: false,
      fields_email: { name: 'email', label: 'Email', placeholder: 'Email', el: { value: '' } },
      fields_password: { name: 'Password', type: 'password', label: 'Password', placeholder: 'Password', el: { value: '' } }
    }
  },
  methods: {
    async login () {
      if (this.email !== '' && this.password !== '') {
        this.loginError = false
        try {
          await this.$store.dispatch('AUTH/login', { email: this.email, password: this.password })
          this.$router.push('/')
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
  $login_logo: url("../assets/images/cushing_logo.svg");

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
      border-top: 0.8rem solid map-get($colors, blue );
      padding: 3rem;
      .logo{
        width: 24rem;
        margin-bottom: 5rem;
      }
      .form-field-element-input{
           width: 100%;
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
        border-top: 0.1rem solid map-get($colors , grey12 );
        background-color: map-get($colors,white);
        padding-top: 0.2rem;
        padding: 1rem 1rem 0rem 1rem;
      }
  }
</style>
