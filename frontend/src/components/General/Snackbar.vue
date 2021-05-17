<template>
  <v-snackbar
    v-model="show"
    :timeout="snackbar.timeout"
    top
  >
    <span :class="{'v-snack-multiline__content': snackbar.multiline}">
      {{ snackbar.message.trim() }}
    </span>

    <template v-slot:action="{ attrs }">
      <v-btn
        text
        v-bind="attrs"
        @click="show = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>

import utils, { actionTypes } from '@/store/modules/utils'
import { mapState, mapActions } from 'vuex'

export default {
  data: () => ({
    show: false,
  }),

  computed: {
    ...mapState(utils.moduleName, { snackbar: state => state.snackbar }),
  },

  watch: {
    snackbar: {
      handler () {
        if (this.snackbar.message !== '') {
          this.show = true
        }
      },
      deep: true,
    },
    show () {
      if (!this.show) {
        this.setSnackbar({ message: '' })
      }
    },
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar])
  }
}
</script>

<style lang="scss">
.v-snack {
  .v-snack__wrapper{
    min-height: rem(56);
  }
  .v-snack-multiline__content {
    white-space: break-spaces;
  }
}
</style>
