<template>
  <div>
    This is a login page . . <br>
    <input
      v-model="email"
      type="text"
      name="username"
      placeholder="Email"
    >
    <br>
    <input
      v-model="password"
      type="password"
      name="password"
      placeholder="Password"
    >
    <button
      type="button"
      @click="login()"
    >
      Login
    </button>
    <br><div v-if="loginError">
      There was a problem. Please check your email and password.
    </div>
  </div>
</template>

<script>
// import axios from '@/store/api_calls/axios_config'

export default {
  name: 'Login',
  data () {
    return {

      email: '',
      password: '',
      loginError: false

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
