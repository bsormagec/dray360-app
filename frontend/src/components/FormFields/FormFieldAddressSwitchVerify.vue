<template>
  <!--  eslint-disable vue/no-v-html -->
  <FormFieldPresentation
    :references="references"
    value=""
    label=""
    :edit-mode="editMode"
    only-hover
    :managed-by-template="managedByTemplate"
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
            <span class="form-field__label block__left">Address as Recognized</span>
            <span class="block__right">{{ recognizedText }}</span>
          </div>

          <div class="address-book-modal__body__block">
            <span class="form-field__label block__left">{{ !verified ? 'Closest Match' : 'Verified Address' }}</span>
            <span
              class="block__right"
              v-html="textAddressToShow"
            />
          </div>
        </div>

        <div class="address-book-modal__footer">
          <v-btn
            v-if="!verified && addressFound"
            :disabled="isLocked"
            :loading="isLoading"
            color="primary"
            outlined
            small
            class="mr-2"
            @click="verifyMatch"
          >
            Verify Closest Match
          </v-btn>

          <v-btn
            class="mr-2"
            :disabled="isLocked"
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
          :enable-address-filters="enableAddressFilters"
          :enable-search="enableSearch"
          :save-to-all="isMultiOrderRequest && hasPermission('all-orders-edit')"
          @change="handleChange"
          @close="addressModalOpen = false"
        />
      </div>
    </div>
  </FormFieldPresentation>
</template>

<script>
/* eslint-disable vue/no-v-html */
import AddressBookModalDialog from '@/components/Orders/AddressBookModalDialog'
import FormFieldPresentation from './FormFieldPresentation'

import { mapState, mapGetters } from 'vuex'
import orders from '@/store/modules/orders'
import orderForm from '@/store/modules/order-form'
import permissions from '@/mixins/permissions'

import get from 'lodash/get'
import { formatAddress } from '@/utils/order_form_general_functions'

export default {
  name: 'FormFieldAddressSwitchVerify',

  components: {
    AddressBookModalDialog,
    FormFieldPresentation
  },

  mixins: [permissions],

  props: {
    references: { type: String, default: null },
    label: { type: String, required: false, default: '' },
    verified: { type: Boolean, required: true },
    recognizedText: { type: String, default: '' },
    matchedAddress: { required: true },
    terminal: { type: Boolean, required: false, default: false },
    billable: { type: Boolean, required: false, default: false },
    editMode: { required: true, type: Boolean },
    enableAddressFilters: { type: Boolean, required: false, default: true },
    enableSearch: { type: Boolean, required: false, default: false },
    managedByTemplate: { type: Boolean, required: false, default: false },
  },

  data: (vm) => ({
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
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights
    }),
    addressFound () {
      return get(this.currentAddress, 'id') !== undefined
    },
    textAddressToShow () {
      return formatAddress(this.currentAddress)
    },
    isLoading () {
      return this.allHighlights[this.references]?.loading || false
    }
  },

  beforeMount () {
    this.setFilters()
  },

  methods: {
    handleChange (event) {
      this.addressModalOpen = false
      const { address, saveAll = false } = event

      this.currentAddress = {
        ...address,
        id: address.t_address_id
      }

      this.$emit('change', { ...(this.currentAddress), saveAll })
    },
    verifyMatch () {
      this.$emit('change', { ...(this.currentAddress), saveAll: false })
    },
    toggleAddressModal () {
      this.addressModalOpen = !this.addressModalOpen
    },
    setFilters () {
      /* eslint camelcase: 0 */
      const { t_company_id: company_id, t_tms_provider_id: tms_provider_id } = this.currentOrder

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
    border-bottom: rem(1) solid rgba(var(--v-slate-gray-base-rgb), 50%);

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
    margin-left: auto;

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

      &:last-child {
        text-align: right;
        width: 60%;
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
