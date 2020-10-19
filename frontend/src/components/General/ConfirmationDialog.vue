<template>
  <v-row justify="center">
    <v-dialog
      :value="confirmationDialog.open"
      persistent
      max-width="520"
    >
      <v-card>
        <v-card-title
          v-show="confirmationDialog.title !== ''"
        >
          {{ confirmationDialog.title }}
        </v-card-title>
        <v-card-text v-show="confirmationDialog.text !== ''">
          {{ confirmationDialog.text }}
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="grey"
            text
            @click="cancel"
          >
            {{ confirmationDialog.cancelText }}
          </v-btn>
          <v-btn
            color="primary"
            text
            @click="accept"
          >
            {{ confirmationDialog.confirmText }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>

import utils, { type } from '@/store/modules/utils'
import { mapActions, mapState } from 'vuex'
export default {
  name: 'ConfirmationDialog',
  computed: {
    ...mapState(utils.moduleName, { confirmationDialog: state => state.confirmationDialog })
  },
  methods: {
    ...mapActions(utils.moduleName, {
      accept: type.acceptConfirmationDialog,
      cancel: type.cancelConfirmationDialog
    })
  }
}
</script>

<style lang="scss">

</style>
