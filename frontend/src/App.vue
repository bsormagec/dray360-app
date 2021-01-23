<template>
  <v-app :style="cssVars">
    <SidebarNavigation />
    <v-main :style="sidebarStyles">
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
    </v-main>
  </v-app>
</template>

<script>
import Snackbar from '@/components/General/Snackbar'
import ConfirmationDialog from '@/components/General/ConfirmationDialog'
import SidebarNavigation from '@/components/General/SidebarNavigation'
import { hexToRgb } from '@/utils/hex_to_rgb'
import utils from '@/store/modules/utils'
import { mapState } from 'vuex'
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'App',
  components: {
    Snackbar,
    ConfirmationDialog,
    SidebarNavigation
  },
  mixins: [isMobile],
  computed: {
    ...mapState(utils.moduleName, {
      Sidebar: state => state.sidebar.show
    }),
    cssVars () {
      const cssVars = {}
      for (const key in this.$vuetify.theme.themes.light) {
        const rgbColor = hexToRgb(this.$vuetify.theme.themes.light[key])
        cssVars[`--v-${key}-base-rgb`] = `${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}`
      }
      return cssVars
    },
    sidebarStyles () {
      if (this.isMobile && this.Sidebar) return 'left: 196px; position: relative; overflow: hidden;'
      return ''
    }
  }

}
</script>

<style lang="scss">
@import "@/assets/styles/index.scss";
.v-application {
  overflow: hidden;
}
</style>
