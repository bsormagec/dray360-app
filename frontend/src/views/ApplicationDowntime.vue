<template>
  <div class="wrapper">
    <div class="pagenotfound">
      <v-row class="text-row">
        <v-col
          md="7"
        >
          <h1 class="down-for">
            <span class="span-down-for">DOWN FOR</span>
            <br>
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
  padding-top: rem(300);
}
.down-for {
  font-family: 'Oswald', sans-serif;
  font-size: rem(26);
  letter-spacing: normal;
  line-height: normal;
  font-weight: 700;
  span{
    font-size: rem(30) !important;
    color: var(--v-primary-base) !important;
  }
  .span-down-for {
    font-size: rem(30) !important;
  }
  .span-maintenance {
    font-size: rem(60) !important;
  }
}
.pagenotfound {
  width: 35%;
  margin: 0 auto;
}
.message {
  font-size: rem(14) !important;
  font-style: normal;
  color: map-get($colors, black);
  max-width: rem(300);
  font-weight: 300;
  padding-top: rem(50);
}
.copyright{
  bottom: rem(20);
  text-align: center;
  margin-top: rem(250);
  p{
    color: map-get($colors, grey-9 );
  }
}
img {
  width: rem(230);
}
</style>
