<template>
  <div class="sidebar">
    <div class="sidebar__logo" />
    <div class="sidebar__footer" />
  </div>
</template>

<script>
export default {
  name: 'Sidebar'
}
</script>

<style lang="scss" scoped>
$sidebar-width: 13.5%;
$cushing-logo: url("../assets/images/cushing_logo.svg");
$ordermaster-logo: url("../assets/images/ordermaster_logo.svg");

.sidebar {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: $sidebar-width;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: 4rem;
  padding-bottom: 3rem;
}

.sidebar__logo {
  width: 72%;
  max-width: 14rem;
  height: 4.3rem;
  background-image: $cushing-logo;
  background-size: contain;
  background-position: center center;
}

.sidebar__footer {
  width: 57%;
  max-width: 11.1rem;
  height: 4.5rem;
  margin-top: auto;
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
}
</style>
