<template>
  <v-app-bar
    color="primary"
    app
    dark
    dense
    flat
    height="40"
  >
    <v-app-bar-nav-icon
      v-if="isMobile"
      @click="toogleMenu"
    />
    <v-toolbar-title
      class="text-body-1"
    >
      {{ currentRouteName }}
    </v-toolbar-title>
  </v-app-bar>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'

export default {
  name: 'AppBar',

  computed: {
    ...mapState(utils.moduleName, {
      showSidebar: state => state.sidebar.show
    }),

    currentRouteName () {
      return this.$route.name
    },

    isMobile () {
      return this.$vuetify.breakpoint.mobile
    },
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSidebar]),

    toogleMenu () {
      this.setSidebar({ show: !this.showSidebar })
    }
  },
}
</script>
