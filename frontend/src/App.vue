<template>
  <v-app :style="cssVars">
    <v-container
      fluid
      pa-0
    >
      <v-row no-gutters>
        <v-col class="sidebar">
          <SidebarNavigation />
        </v-col>
        <v-col>
          <router-view />
        </v-col>
      </v-row>
    </v-container>
    <Snackbar />
    <ConfirmationDialog />
  </v-app>
</template>

<script>
import Snackbar from '@/components/General/Snackbar'
import ConfirmationDialog from '@/components/General/ConfirmationDialog'
import SidebarNavigation from '@/components/General/SidebarNavigation'
import { hexToRgb } from '@/utils/hex_to_rgb'

export default {
  name: 'App',
  components: {
    Snackbar,
    ConfirmationDialog,
    SidebarNavigation
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
@import "@/assets/styles/index.scss";

.sidebar {
  max-width: rem(map-get($sizes, sidebar-desktop-width));
}
</style>
