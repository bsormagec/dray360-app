<template>
  <div class="pagenotfound">
    <div class="title">
      <div class="rownotfound">
        <div class="">
          <h1>403:</h1>
        </div>
        <div class="">
          <h1><span>PAGE NOT AUTHORIZED</span></h1>
        </div>
      </div>
      <div class="row">
        <div class="rownotfound">
          <p class="copy">
            You are not authorized to access this page.
          </p>
        </div>
      </div>
      <div class="row">
        <div class="rownotfound">
          <v-btn
            class="btn btn-home"
            color="primary"
            outlined
            @click="$router.push('/inbox')"
          >
            Return Home
          </v-btn>
        </div>
      </div>
      <div class="row">
        <div class="rownotfound">
          <p
            class="contact__us"
          >
            Contact Us
          </p>
        </div>
      </div>
    </div>
    <div class="copyright">
      <p>©2020 Dray360 — an Affiliate of TCompanies Inc. Privacy Policy • Terms of Use</p>
    </div>
  </div>
</template>
<script>
import utils, { actionTypes } from '@/store/modules/utils'
import { mapActions } from 'vuex'

export default {
  name: 'NotAuthorized',
  async created () {
    await this.getTenantConfig()
  },
  methods: {
    ...mapActions(utils.moduleName, [actionTypes.getTenantConfig]),
  }
}
</script>
<style lang="scss" scoped>
$background_login: url("../assets/images/login_background.png");
.pagenotfound{
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
    .title{
      margin: 0 auto;
      width: rem(500);
      .rownotfound{
        display: flex;
        align-items: center;
        margin: 0 auto;
        .copy{
          font-size: rem(21) !important;
          font-weight: 300;
          text-align: left;
          margin-top: rem(30);
          width: rem(450);
        }
        .btn-home{
          margin-top: rem(30);
          width: rem(400);
          padding: rem(20);
        }
        .contact__us{
          margin-top: rem(10);
          color: var(--v-primary-base);
        }
      }
        h1{
          font-family: 'Oswald', sans-serif;
          font-size: rem(14.4);
          color: var(--v-primary-base) !important;
          span{
            font-size: rem(62) !important;
            color: var(--v-primary-base) !important;
            line-height: rem(65);
          }
        }
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
