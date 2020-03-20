<template>
  <v-app>
    <router-view />
  </v-app>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="scss">
html {
  font-size: map-get($font, base-size) !important;
}

body {
  font-size: 1.6rem;
}
</style>
