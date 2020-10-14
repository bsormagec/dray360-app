<template>
  <div>
    <div
      v-if="snackbar().showSpinner"
      class="snackbar"
    >
      <v-snackbar

        v-model="snackbar().show"
        :top="true"
        :timeout="0"
        @input="close"
      >
        <v-progress-circular
          v-if="snackbar().showSpinner"
          indeterminate
          color="white"
        />
      </v-snackbar>
    </div>
    <div
      v-else-if="!snackbar().showSpinner && snackbar().show"
      class="snackbar__withMessage"
    >
      <v-snackbar
        v-model="snackbar().show"
        :top="true"
        :timeout="4000"
        @input="close"
      >
        {{ snackbar().message }}
        <v-btn
          color="white"
          text
          @click="close"
        >
          Close
        </v-btn>
      </v-snackbar>
    </div>
  </div>
</template>

<script>

import utils, { type } from '@/store/modules/utils'
import { mapActions, mapState } from 'vuex'
export default {
  data () {
    return {
      ...mapState(utils.moduleName, { snackbar: state => state.snackbar }),
      loaded: false,
      snackclass: ''
    }
  },
  methods: {
    ...mapActions(utils.moduleName, [type.setSnackbar]),
    async close () {
      this.loaded = !this.snackbar().showSpinner
      this.$emit('close',
        await this[type.setSnackbar]({
          show: undefined,
          showSpinner: undefined,
          message: ''
        })

      )
    }
  }
}
</script>

<style lang="scss">
.snackbar{
  .v-snack__wrapper{
    min-width: rem(60) !important;
    max-width: rem(60) !important;
  }
  .v-snack__content{
    color: map-get($colors, white ) !important;
  }
}
.snackbar__withMessage{
  .v-snack__content{
    color: map-get($colors, white ) !important;
  }
}
</style>
