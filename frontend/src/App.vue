<template>
  <v-app :style="cssVars">
    <NewSideBarNavigation v-if="!isException" />
    <AppBar v-if="!isException && !isInbox" />
    <v-main>
      <v-container
        pa-0
        fluid
        overflow-hidden
      >
        <v-row no-gutters>
          <v-col class="content">
            <router-view />
          </v-col>
        </v-row>
      </v-container>
      <Snackbar />
      <ConfirmationDialog />
      <PtImageUploadDialog />
    </v-main>
  </v-app>
</template>

<script>
import AppBar from '@/components/General/AppBar'
import NewSideBarNavigation from '@/components/General/NewSideBarNavigation'
import Snackbar from '@/components/General/Snackbar'
import ConfirmationDialog from '@/components/General/ConfirmationDialog'
import PtImageUploadDialog from '@/components/PtImageUploadDialog'
import { hexToRgb } from '@/utils/hex_to_rgb'

export default {
  name: 'App',

  components: {
    AppBar,
    NewSideBarNavigation,
    Snackbar,
    ConfirmationDialog,
    PtImageUploadDialog,
  },

  computed: {
    cssVars () {
      const cssVars = {}
      for (const key in this.$vuetify.theme.themes.light) {
        const rgbColor = hexToRgb(this.$vuetify.theme.themes.light[key])
        cssVars[`--v-${key}-base-rgb`] = `${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}`
      }
      return cssVars
    },

    isException () {
      return ['Login', 'Not Authorized', 'Not Found'].includes(this.$route.name)
    },

    isInbox () {
      return ['Inbox', 'Field Mapping'].includes(this.$route.name)
    }
  },
}
</script>

<style lang="scss">
@import "@/assets/styles/index.scss";
.v-application {
  overflow: hidden;
}
</style>
