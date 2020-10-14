<template>
  <div>
    <div class="wrapper">
      <div class="login-box">
        <h1>Check your inbox…</h1>
        <p>A password reset link has been sent to {{ $route.query.email }}. If you do not receive this email, and it’s not your junk mail, contact support.</p>

        <div class="button_checkbox">
          <v-btn
            class="btn-login"
            href="/login"
          >
            Login
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
import { mapActions, mapState } from 'vuex'
import auth from '@/store/modules/auth'

export default {
  name: 'EmailConfirmation',
  computed: {
    ...mapState(utils.moduleName, {
      tenantConfig: state => state.tenantConfig
    })
  },

  async created () {
    await this[type.getTenantConfig]()
  },

  methods: {
    ...mapActions(utils.moduleName, [type.getTenantConfig])
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
      .text__error{
        color: map-get($colors, red );
      }
      input{
        border: rem(1) solid lightgray;
        margin: rem(5) auto;
        padding: rem(5) rem(65) rem(5) rem(90);
        border-radius: rem(5);
        max-width: rem(500);
      }
      .button_checkbox{
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
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
