<template>
  <!--  eslint-disable vue/no-v-html -->
  <div class="form-field-element-modal-address">
    <div class="address-book-modal">
      <span class="address-book-modal__title"><strong>{{ label }}</strong></span>

      <div class="address-book-modal__body">
        <div
          v-if="!verified || !addressFound"
          class="address-book-modal__body__status"
        >
          <span :class="{'not-found': !addressFound}">
            {{ addressFound ? 'Address Verification Needed': 'Address Not Found' }}
          </span>
          <v-icon :color="addressFound ? 'warning' : 'error'">
            mdi-alert
          </v-icon>
        </div>

        <div class="address-book-modal__body__block">
          <span class="block__left">Address as Recognized</span>
          <span class="block__right">{{ recognizedText }}</span>
        </div>

        <div class="address-book-modal__body__block">
          <span class="block__left">{{ !verified ? 'Closest Match' : 'Verified Address' }}</span>
          <span
            class="block__right"
            v-html="textAddressToShow"
          />
        </div>
      </div>

      <div class="address-book-modal__footer">
        <v-btn
          v-if="!verified && addressFound"
          color="primary"
          outlined
          style="margin-right: 20px;"
          @click="verifyMatch"
        >
          Verify Closest Match
        </v-btn>

        <v-btn
          color="primary"
          outlined
          @click="toggleAddressModal"
        >
          Select Different
        </v-btn>
      </div>

      <AddressBookModalDialog
        :is-open="addressModalOpen"
        :filters="filters"
        @change="handleChange"
      />
    </div>
  </div>
</template>

<script>
/* eslint-disable vue/no-v-html */
import AddressBookModalDialog from '@/components/Orders/AddressBookModalDialog'

import { mapState } from 'vuex'
import orders from '@/store/modules/orders'

import get from 'lodash/get'
import { formatAddress } from '@/utils/order_form_general_functions'

export default {
  name: 'FormFieldAddressSwitchVerify',

  components: {
    AddressBookModalDialog
  },

  props: {
    label: { type: String, required: true },
    verified: { type: Boolean, required: true },
    recognizedText: { type: String, default: '' },
    matchedAddress: { required: true },
    terminal: { type: Boolean, required: false, default: false },
    billable: { type: Boolean, required: false, default: false }
  },

  data: (vm) => ({
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    currentAddress: vm.matchedAddress,
    addressModalOpen: false,
    filters: {
      company_id: null,
      tms_provider_id: null,
      rawtext: '',
      is_terminal_address: vm.terminal,
      is_billable_address: vm.billable
    }
  }),

  computed: {
    addressFound () {
      return get(this.currentAddress, 'id') !== undefined
    },
    textAddressToShow () {
      return formatAddress(this.currentAddress)
    }
  },

  beforeMount () {
    this.setFilters()
  },

  methods: {
    handleChange (value) {
      this.addressModalOpen = false
      if (value === undefined) {
        return
      }
      const { address } = value

      this.currentAddress = {
        ...address,
        id: address.t_address_id
      }

      this.$emit('change', this.currentAddress)
    },
    verifyMatch () {
      this.$emit('change', this.currentAddress)
    },
    toggleAddressModal () {
      this.addressModalOpen = !this.addressModalOpen
    },
    setFilters () {
      /* eslint camelcase: 0 */
      const { t_company_id: company_id, t_tms_provider_id: tms_provider_id } = this.currentOrder()

      this.filters = {
        ...(this.filters),
        company_id,
        tms_provider_id,
        rawtext: this.recognizedText
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.address-book-modal {
  .address-book-modal__title {
    display: block;
    font-size: rem(14) !important;
    padding-bottom: rem(11);
    border-bottom: rem(1) solid map-get($colors, grey-9);
    margin-bottom: rem(20);
    text-transform: capitalize;
  }

  .address-book-modal__body {
    display: flex;
    flex-direction: column;
  }

  .address-book-modal__body__status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;

    span {
      flex-grow: 1;
      text-align: right;
      padding-right: rem(16);
      color: #cc904c;
      font-weight: bold;
      font-size: rem(14.4) !important;
      &.not-found {
        color: map-get($colors, red)
      }
    }
  }

  .address-book-modal__body__block {
    display: flex;
    justify-content: space-between;
    margin-bottom: rem(20);

    span {
      width: 50%;
      font-size: rem(14.4) !important;

      &:first-child {
        font-weight: bold;
      }

      &:last-child {
        text-align: right;
      }
    }
  }

  .address-book-modal__footer {
    display: flex;
    justify-content: flex-end;
    margin-bottom: rem(20);
  }
}
</style>
