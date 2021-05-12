<template>
  <v-btn
    v-if="isMobile && currentUser !== undefined"
    v-bind="btnAttributes"
    @click.stop="toogleSidebar"
  >
    <v-icon v-bind="iconAttributes">
      {{ showSidebar ? 'mdi-close' : 'mdi-menu' }}
    </v-icon>
  </v-btn>
</template>

<script>
import auth from '@/store/modules/auth'
import { mapState, mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'
import isMobile from '@/mixins/is_mobile'

export default {
  mixins: [isMobile],
  props: {
    dark: {
      type: Boolean,
      required: false,
      default: true
    }
  },
  data () {
    return {
      btnAttributes: {
        color: 'primary',
        small: true,
        dark: this.dark,
        outlined: !this.dark,
        depressed: true,
        width: 40,
        height: 40
      },
      iconAttributes: {
        dark: true
      }
    }
  },
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    ...mapState(utils.moduleName, {
      showSidebar: state => state.sidebar.show
    })
  },
  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSidebar]),
    toogleSidebar () {
      this.setSidebar({ show: !this.showSidebar })
    }
  }
}
</script>

<style lang="sass" scoped>

</style>
