<template>
  <v-app>
    <router-view />
    <Snackbar />
  </v-app>
</template>

<script>
import Snackbar from '@/components/General/Snackbar'

export default {
  name: 'App',
  components: {
    Snackbar
  }
}
</script>

<style lang="scss">
@import "./assets/styles/_index.scss";
</style>
