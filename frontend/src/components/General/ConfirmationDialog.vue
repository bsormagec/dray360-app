<template>
  <v-dialog
    :value="confirmationDialog.open"
    persistent
    max-width="520"
  >
    <v-card>
      <v-card-title
        v-show="confirmationDialog.title !== ''"
        :class="{'no-wrap':confirmationDialog.noWrap}"
      >
        {{ confirmationDialog.title }}
      </v-card-title>
      <v-card-text v-show="confirmationDialog.text !== ''">
        {{ confirmationDialog.text }}
      </v-card-text>
      <v-card-text v-show="confirmationDialog.hasInputValue">
        <v-text-field
          v-model="inputValue"
          dense
          flat
          outlined
          solo
          hide-details="true"
        />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          v-show="confirmationDialog.cancelText !== ''"
          color="grey"
          text
          @click="cancel"
        >
          {{ confirmationDialog.cancelText }}
        </v-btn>
        <v-btn
          color="primary"
          text
          @click="acceptDialog"
        >
          {{ confirmationDialog.confirmText }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>

import utils, { type } from '@/store/modules/utils'
import { mapActions, mapState } from 'vuex'
export default {
  name: 'ConfirmationDialog',
  data () {
    return {
      inputValue: ''
    }
  },
  computed: {
    ...mapState(utils.moduleName, { confirmationDialog: state => state.confirmationDialog })
  },
  methods: {
    ...mapActions(utils.moduleName, {
      accept: type.acceptConfirmationDialog,
      cancel: type.cancelConfirmationDialog
    }),

    acceptDialog () {
      this.accept(this.inputValue)
      this.inputValue = ''
    }
  }
}
</script>

<style lang="scss" scoped>
.v-card__title {
  white-space: normal;
  &.no-wrap{
    white-space: nowrap !important;
  }
}
</style>
