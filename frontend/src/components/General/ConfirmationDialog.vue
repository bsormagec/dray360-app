<template>
  <!--  eslint-disable vue/no-v-html -->
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
      <v-card-text
        v-show="confirmationDialog.text !== ''"
        v-html="confirmationDialog.text"
      />
      <v-card-text v-show="confirmationDialog.hasInputValue">
        <v-form ref="form">
          <v-text-field
            ref="userInput"
            v-model="inputValue"
            :v-bind="inputFieldAttributes"
            :type="inputFieldAttributes.type"
            :rules="rules"
          />
        </v-form>
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
          :disabled="confirmationDialog.validate ? !isValid : false"
          @click="acceptDialog"
        >
          {{ confirmationDialog.confirmText }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
/* eslint-disable vue/no-v-html */
import { mapActions, mapState } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'

export default {
  name: 'ConfirmationDialog',

  data: () => ({
    inputValue: '',
    isValid: false,
    defaultProps: {
      type: 'text',
      dense: true,
      flat: true,
      outlined: true,
      solor: true,
      'hide-details': true
    }
  }),

  computed: {
    ...mapState(utils.moduleName, { confirmationDialog: state => state.confirmationDialog }),

    inputFieldAttributes () {
      return {
        ...this.defaultProps,
        ...this.confirmationDialog.inputProps,
      }
    },

    rules () {
      const props = this.confirmationDialog.inputProps
      const rules = []

      if (props.type === 'number') {
        const number = v => !!Number(v) || 'Value should be a valid number'
        rules.push(number)
      }

      if (props.min) {
        const min = v => {
          return !(Number(v) < props.min) || `Value should be greater than ${props.min}`
        }
        rules.push(min)
      }

      if (props.max) {
        const max = v => !(Number(v) > props.max) || `Value should not be greater than ${props.max}`
        rules.push(max)
      }

      return rules
    }
  },

  watch: {
    inputValue: 'validateField',
  },

  methods: {
    ...mapActions(utils.moduleName, {
      accept: actionTypes.acceptConfirmationDialog,
      cancel: actionTypes.cancelConfirmationDialog
    }),

    acceptDialog () {
      this.accept(this.inputValue)
      this.inputValue = ''
    },

    validateField () {
      this.$refs.form.validate()
      this.isValid = this.$refs.userInput.valid
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
