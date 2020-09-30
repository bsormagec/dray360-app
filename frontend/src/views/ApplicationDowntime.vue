<template>
  <div class="wrapper">
    <div class="pagenotfound">
      <v-row class="text-row">
        <v-col
          md="7"
        >
          <h1 class="down-for">
            <span class="span-down-for">DOWN FOR</span>
          </h1>
          <h1 class="maintenance">
            <span class="span-maintenance">MAINTENANCE</span>
          </h1>
          <p class="message">
            Dray360 is currently in the shop for a little scheduled upkeep. We will return shortly.
          </p>
        </v-col>
        <v-col md="3">
          <img
            src="../assets/images/application_downtime.png"
          >
        </v-col>
      </v-row>
      <div class="copyright">
        <p>©2020 Dray360 — an Affiliate of TCompanies Inc. Privacy Policy • Terms of Use</p>
      </div>
    </div>
  </div>
</template>
<script>

export default {
  name: 'ApplicationDowntime'
}
</script>
<style lang="scss" scoped>
$background_login: url("../assets/images/login_background.png");
.wrapper {
  background: $background_login no-repeat center center fixed;
}
.text-row {
  padding-top: 30rem;
}
.maintenance {
    font-family: 'Oswald', sans-serif;
    color: var(--v-primary-base) !important;
    span{
      color: var(--v-primary-base) !important;
    }
    .span-maintenance {
      font-size: 6rem !important;
    }
}
.down-for {
  font-family: 'Oswald', sans-serif;
  span{
    font-size: 3.0rem !important;
    color: var(--v-primary-base) !important;
  }
  .span-down-for {
    font-size: 3rem !important;
  }
}
.pagenotfound {
  width: 35%;
  margin: 0 auto;
}
.message {
  font-size: 1.4rem !important;
  font-style: normal;
  color: map-get($colors, black);
  max-width: 30rem;
  font-weight: 300;
  padding-top: 5rem;
}
.copyright{
  bottom: 2rem;
  text-align: center;
  margin-top: 25rem;
  p{
    color: map-get($colors, grey-9 );
    line-height: 1.6rem;
  }
}
img {
  width: 350px
}
</style>
