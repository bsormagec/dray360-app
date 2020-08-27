<template>
  <v-app :style="cssVars">
    <router-view />
    <Snackbar />
  </v-app>
</template>

<script>
import Snackbar from '@/components/General/Snackbar'
import { hexToRgb } from '@/utils/hex_to_rgb'

export default {
  name: 'App',
  components: {
    Snackbar
  },
  computed: {
    cssVars () {
      const primaryRgb = hexToRgb(this.$vuetify.theme.themes.light.primary)
      const secondaryRgb = hexToRgb(this.$vuetify.theme.themes.light.secondary)
      return {
        '--v-primary-base-rgb': `${primaryRgb.r}, ${primaryRgb.g}, ${primaryRgb.b}`,
        '--v-secondary-base-rgb': `${secondaryRgb.r}, ${secondaryRgb.g}, ${secondaryRgb.b}`
      }
    }
  }
}
</script>

<style lang="scss">
@import "./assets/styles/_index.scss";
</style>
