<template>
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
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
export default {
  name: 'ConfirmationDialog',
  data () {
    return {
      inputValue: ''
    }
  },
  computed: {
    ...mapState(utils.moduleName, { confirmationDialog: state => state.confirmationDialog }),
    ...mapState(orderForm.moduleName, {
      order: state => state.order
    })
  },
  methods: {
    ...mapActions(utils.moduleName, {
      accept: type.acceptConfirmationDialog,
      cancel: type.cancelConfirmationDialog,
      setConfirmationDialog: type.setConfirmationDialog
    }),

    ...mapActions(orderForm.moduleName, {
      updateOrder: orderFormTypes.updateOrder
    }),

    acceptDialog () {
      this.accept(this.inputValue)
    }
  }
}
</script>

<style lang="scss">

</style>
