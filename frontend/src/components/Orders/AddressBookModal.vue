/* eslint-disable vue/no-v-html */
<template>
  <div class="address-book-modal">
    <span class="address-book-modal__title"><strong>{{ field.name }}</strong></span>

    <div class="address-book-modal__body">
      <div
        v-if="!isVerified || !addressFound"
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
        <span class="block__right">{{ recogEmitted || field.value }}</span>
      </div>

      <div class="address-book-modal__body__block">
        <span class="block__left">{{ !isVerified ? 'Closest Match' : 'Verified Address' }}</span>
        <span
          class="block__right"
          v-html="matchedToDisplay"
        />
      </div>
    </div>

    <div class="address-book-modal__footer">
      <v-btn
        v-if="!isVerified && addressFound"
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
        @click="toggleIsOpen"
      >
        Select Different
      </v-btn>
    </div>

    <AddressBookModalDialog
      :is-open="isOpen"
      :filters="filters"
      @change="change"
    />
  </div>
</template>

<script>
import AddressBookModalDialog from '@/components/Orders/AddressBookModalDialog'

export default {

  components: {
    AddressBookModalDialog
  },

  props: {
    field: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      required: false,
      default: () => ({
        company_id: null,
        tms_provider_id: null,
        rawtext: '',
        is_terminal_address: false,
        is_billable_address: false,
        is_ssrr_address: false,
      })
    }
  },

  data: (vm) => ({
    isVerified: vm.field.verified === true,
    addressFound: vm.field.addressId !== null && vm.field.addressId !== undefined,
    isOpen: false,
    matchedToDisplay: '',
    recogEmitted: ''
  }),

  mounted () {
    this.matchedToDisplay = this.field.matchedAddress
    this.search = this.filters.rawtext

    this.loaded = true
  },

  methods: {
    verifyMatch () {
      this.change({ id: this.field.value, matchedAddress: this.field.matchedAddress })
    },

    toggleIsOpen () {
      this.isOpen = !this.isOpen
    },

    change ({ id, matchedAddress } = {}) {
      if (typeof id !== 'undefined') {
        this.isVerified = true
        this.addressFound = true
        this.recogEmitted = this.field.value
        this.matchedToDisplay = matchedAddress
        this.$emit('change', id)
      }

      this.isOpen = false
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
        font-weight: 700;
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
