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
            @click="$router.push('/dashboard')"
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
import utils, { type } from '@/store/modules/utils'
import { mapActions } from '@/utils/vuex_mappings'

export default {
  name: 'NotAuthorized',
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
      width: 50rem;
      .rownotfound{
        display: flex;
        align-items: center;
        margin: 0 auto;
        .copy{
          font-size: 2.1rem !important;
          font-weight: 300;
          text-align: left;
          margin-top: 3rem;
          width: 45rem;
        }
        .btn-home{
          margin-top: 3rem;
          width: 40rem;
          padding: 2rem;
        }
        .contact__us{
          margin-top: 1rem;
          color: var(--v-primary-base);
        }
      }
        h1{
          font-family: 'Oswald', sans-serif;
          font-size: 14.4rem;
          color: var(--v-primary-base) !important;
          span{
            font-size: 6.2rem !important;
            color: var(--v-primary-base) !important;
            line-height: 6.5rem;
          }
        }
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
