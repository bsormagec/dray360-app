<template>
  <v-app :style="cssVars">
    <v-container
      fluid
      pa-0
    >
      <v-row no-gutters>
        <v-col
          :class="Sidebar ? 'sidebar' : 'no__sidebar'"
        >
          <SidebarNavigation />
        </v-col>
        <v-col
          :md="isMobile ? 12 : false"
          :sm="isMobile ? 12 : false"
          class="content"
        >
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
    }
  }

}
</script>

<style lang="scss">
@import "@/assets/styles/index.scss";

.sidebar {
  background-color: transparent;
  @include media("min") {
    max-width: rem(0);
  }
  @include media("lg") {
    max-width: rem(map-get($sizes, sidebar-desktop-width));
  }
}
.no__sidebar{
  max-width: rem(0);
}

</style>
