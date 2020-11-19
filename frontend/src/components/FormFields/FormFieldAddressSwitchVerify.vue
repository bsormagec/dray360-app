<template>
  <!--  eslint-disable vue/no-v-html -->
  <FormFieldPresentation
    :references="references"
    value=""
    label=""
    :edit-mode="false"
    only-hover
  >
    <div class="form-field-element-modal-address">
      <div class="address-book-modal">
        <div
          v-if="label !== ''"
          class="address-book-modal__title"
        >
          <h3 v-html="label" />
        </div>

        <div class="address-book-modal__body">
          <div
            v-if="!verified || !addressFound"
            class="address-book-modal__body__status"
          >
            <v-icon :color="addressFound ? 'warning' : 'error'">
              mdi-alert-outline
            </v-icon>
            <span :class="{'not-found': !addressFound}">
              {{ addressFound ? 'Address Verification Needed': 'Address Not Found' }}
            </span>
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
            small
            class="mr-2"
            @click="verifyMatch"
          >
            Verify Closest Match
          </v-btn>

          <v-btn
            color="primary"
            outlined
            small
            @click="toggleAddressModal"
          >
            Select Different
          </v-btn>
        </div>

        <AddressBookModalDialog
          :is-open="addressModalOpen"
          :filters="filters"
          :recognized-text="recognizedText"
          @change="handleChange"
        />
      </div>
    </div>
  </FormFieldPresentation>
</template>

<script>
/* eslint-disable vue/no-v-html */
import AddressBookModalDialog from '@/components/Orders/AddressBookModalDialog'
import FormFieldPresentation from './FormFieldPresentation'

import { mapState } from 'vuex'
import orders from '@/store/modules/orders'

import get from 'lodash/get'
import { formatAddress } from '@/utils/order_form_general_functions'

export default {
  name: 'FormFieldAddressSwitchVerify',

  components: {
    AddressBookModalDialog,
    FormFieldPresentation
  },

  props: {
    references: { type: String, default: null },
    label: { type: String, required: false, default: '' },
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
    padding: rem(4) rem(10) rem(3);
    margin-bottom: rem(5);
    background-color: transparent;
    border-bottom: 1px solid rgba(map-get($colors, slate-gray), 50%);

    h3 {
      text-transform: uppercase;
      font-size: rem(13);
      font-weight: 700;
      line-height: (24 / 13);
      letter-spacing: rem(.75);
      color: map-get($colors, mainblue);
    }
  }

  .address-book-modal__body {
    display: flex;
    flex-direction: column;
    padding: rem(10);
  }

  .address-book-modal__body__status {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;

    span {
      padding-left: rem(6);
      color: map-get($colors, yellow );
      font-weight: 700;

      &.not-found {
        color: map-get($colors, red)
      }
    }
  }

  .address-book-modal__body__block {
    display: flex;
    justify-content: space-between;
    margin-bottom: rem(33);

    &:last-child {
      margin-bottom: 0;
    }

    span {
      width: 50%;

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
