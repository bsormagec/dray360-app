<template>
  <div class="pagenotfound">
    <div class="title">
      <div>
        <h1>404: <span>{{ containerType }} NOT FOUND</span></h1>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ContainerNotFound',
  props: {
    containerType: {
      type: String,
      required: true
    }
  }
}
</script>
<style lang="scss" scoped>
$background_login: url("../assets/images/login_background.png");
.pagenotfound {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 0;
  left: map-get($sizes, sidebar-desktop-width );
  bottom: 0;
  right: 0;
  background-image: $background_login;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  .title {
    width: auto;
    max-width: rem(716);
    h1 {
      display: flex;
      align-items: center;
      font-family: 'Oswald', sans-serif;
      font-size: rem(144);
      font-weight: 700;
      color: var(--v-primary-base);
      letter-spacing: 0;
      line-height: (32 / 144);
      span {
        font-size: rem(62);
        color: var(--v-primary-base);
        line-height: (65 / 62);
        letter-spacing: rem(1.25);
      }
    }
  }
}
</style>
