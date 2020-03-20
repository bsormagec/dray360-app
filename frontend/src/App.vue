<template>
  <v-app>
    <div class="app-root">
      <router-view />
    </div>
  </v-app>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style lang="scss">
body {
  font-size: map-get($font, base-size);
}

.app-root {
  font-size: 1rem;
  height: 100%;
}
</style>
