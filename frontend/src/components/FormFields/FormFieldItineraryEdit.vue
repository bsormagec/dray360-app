<template>
  <!--  eslint-disable vue/no-v-html -->
  <FormFieldPresentation
    :references="references"
    value=""
    label=""
    :edit-mode="editMode"
    only-hover
    :admin-notes="adminNotes"
  >
    <div
      v-show="!(!!orderAddressEvent.deleted_at)"
      class="form-field-element-modal-address"
    >
      <div class="address-book-modal">
        <div
          v-if="!editMode"
          class="address-book-modal__title d-flex"
        >
          <h3 v-text="selectedEvent" />
        </div>
        <div v-else>
          <v-toolbar
            dense
            flat
            height="auto"
          >
            <TooltipIcon
              v-if="!!adminNotes"
              :text="adminNotes"
              :custom-icon-attrs="{
                small: false,
                color: 'grey-darken4',
                class: 'mr-1',
              }"
            />
            <div class="event-index">
              {{ currentIndex }}
            </div>
            <v-select
              :value="selectedEvent"
              dense
              outlined
              flat
              hide-details="true"
              :items="eventTypes"
              @change="handleEventTypeChange"
            />
            <v-btn
              class="px-0"
              color="primary"
              small
              outlined
              :disabled="isFirst"
              @click="() => this.$emit('sort', 'up')"
            >
              <v-icon>mdi-arrow-up</v-icon>
            </v-btn>
            <v-btn
              class="px-0"
              color="primary"
              small
              outlined
              :disabled="isLast"
              @click="() => this.$emit('sort', 'down')"
            >
              <v-icon>mdi-arrow-down</v-icon>
            </v-btn>
            <v-spacer />
            <v-btn
              class="px-0"
              color="primary"
              small
              outlined
              @click="() => this.$emit('delete')"
            >
              <v-icon>mdi-delete-outline</v-icon>
            </v-btn>
          </v-toolbar>
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
            <span class="block__right">{{ orderAddressEvent.t_address_raw_text }}</span>
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
            :disabled="isLocked ||readonly"
            color="primary"
            outlined
            small
            class="mr-2"
            @click="verifyMatch"
          >
            Verify Closest Match
          </v-btn>

          <v-btn
            :disabled="isLocked ||readonly"
            class="mr-2"
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
          :recognized-text="orderAddressEvent.t_address_raw_text || ''"
          :enable-address-filters="enableAddressFilters"
          :enable-search="enableSearch"
          :save-to-all="isMultiOrderRequest && hasPermission('all-orders-edit')"
          @change="handleAddressChange"
          @close="addressModalOpen = false"
        />
        <div class="form-field">
          <div
            class="form-field__group"
          >
            <div class="form-field__label">
              Notes
            </div>
            <v-textarea
              v-if="editMode"
              outlined
              auto-grow
              placeholder="Notes"
              :value="orderAddressEvent.note"
              hide-details="true"
              flat
              dense
              solo
              @input="handleNoteChange"
            />
            <div
              v-else
              class="field__value"
            >
              {{ orderAddressEvent.note !== '' && orderAddressEvent.note !== null ? orderAddressEvent.note : '--' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </FormFieldPresentation>
</template>

<script>
/* eslint-disable vue/no-v-html */
import AddressBookModalDialog from '@/components/Orders/AddressBookModalDialog'
import FormFieldPresentation from './FormFieldPresentation'
import TooltipIcon from '@/components/General/TooltipIcon'

import { mapState, mapGetters } from 'vuex'
import orders from '@/store/modules/orders'
import orderForm from '@/store/modules/order-form'
import permissions from '@/mixins/permissions'

import get from 'lodash/get'
import { formatAddress } from '@/utils/order_form_general_functions'

export default {
  name: 'FormFieldItineraryEdit',

  components: {
    AddressBookModalDialog,
    FormFieldPresentation,
    TooltipIcon,
  },

  mixins: [permissions],

  props: {
    orderAddressEvent: { type: Object, required: true },
    currentIndex: { type: Number, required: true },
    references: { type: String, default: null },
    editMode: { type: Boolean, required: true },
    isFirst: { type: Boolean, required: true },
    isLast: { type: Boolean, required: true },
    enableAddressFilters: { type: Boolean, required: false, default: true },
    enableSearch: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
    adminNotes: { type: String, required: false, default: '' },
  },

  data: (vm) => ({
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    currentAddress: vm.orderAddressEvent.address,
    addressModalOpen: false,
    filters: {
      company_id: null,
      tms_provider_id: null,
      rawtext: '',
      is_terminal_address: false,
      is_billable_address: false
    },
    eventTypes: ['Hook', 'Mount', 'Pick Up', 'Deliver', 'Dismount', 'Drop']
  }),

  computed: {
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),

    addressFound () {
      return get(this.currentAddress, 'id') !== undefined
    },

    textAddressToShow () {
      return formatAddress(this.currentAddress)
    },

    verified () {
      return this.orderAddressEvent.t_address_verified || false
    },

    eventTypeBooleanMap () {
      return {
        is_hook_event: 'Hook',
        is_mount_event: 'Mount',
        is_deliver_event: 'Deliver',
        is_pickup_event: 'Pick Up',
        is_dismount_event: 'Dismount',
        is_drop_event: 'Drop'
      }
    },

    selectedEvent () {
      for (const key in this.eventTypeBooleanMap) {
        if (this.orderAddressEvent[key] === true) {
          return this.eventTypeBooleanMap[key]
        }
      }
      return ''
    }
  },

  watch: {
    orderAddressEvent: {
      handler: function () {
        this.currentAddress = this.orderAddressEvent.address
        this.setFilters()
      },
      deep: true
    }
  },

  beforeMount () {
    this.setFilters()
  },

  methods: {
    handleAddressChange (value) {
      this.addressModalOpen = false
      if (value === undefined) {
        return
      }
      const { address, saveAll = false } = value

      this.currentAddress = {
        ...address,
        id: address.t_address_id
      }

      this.$emit('change', {
        ...(this.orderAddressEvent),
        t_address_id: this.currentAddress.id,
        t_address_verified: true,
        address: { ...(this.currentAddress) },
        saveAll
      })
    },

    handleEventTypeChange (value) {
      const eventBooleanMap = {
        ...(this.eventTypeBooleanMap)
      }

      for (const key in eventBooleanMap) {
        eventBooleanMap[key] = eventBooleanMap[key] === value
      }

      this.$emit('change', {
        ...(this.orderAddressEvent),
        ...eventBooleanMap
      })
    },

    handleNoteChange (value) {
      this.$emit('change', {
        ...(this.orderAddressEvent),
        note: value
      })
    },

    verifyMatch () {
      this.$emit('change', { ...(this.orderAddressEvent), t_address_verified: true })
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
        rawtext: this.orderAddressEvent.t_address_raw_text
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
      width: auto;

      &:last-child {
        text-align: right;
        max-width: 60%;
      }
    }
  }

  .address-book-modal__footer {
    display: flex;
    justify-content: flex-end;
    margin-bottom: rem(20);
  }
}

.v-textarea::v-deep {
  .v-input__slot {
    height: auto;
    .v-text-field__slot {
      margin-right: 0;
    }
  }
}

.form-field::v-deep {
  .form-field__group,
  .form-field__group + .action-btns {
    align-items: flex-start;
  }
}

.v-toolbar::v-deep {
  padding: rem(10);
  background-color: transparent !important;
  .v-toolbar__content {
    align-items: center;
    padding: 0;
  }
  .v-select fieldset {
    border-color: var(--v-primary-base) !important;
  }
  .v-select .v-input__append-inner {
    margin-top: rem(2) !important;
  }
  .v-input {
    font-size: rem(14);
  }
  .v-input__slot {
    height: rem(28);
    min-height: auto !important;
  }
  .v-btn {
    min-width: rem(26);
    .v-icon {
      font-size: rem(16);
    }
  }
  .event-index {
    height: rem(28);
    padding: rem(6) rem(10);
    background-color: rgba(var(--v-primary-base-rgb), 0.25);
    font-size: rem(10);
    font-weight: 500;
    line-height: (15 / 10);
    letter-spacing: rem(1.5);
    border: 1px solid var(--v-primary-base);
  }
  .event-index {
    border-radius: rem(4) 0  0 rem(4);
  }
  .v-btn:nth-child(3),
  .v-select {
    border-radius: 0;
    margin-left: rem(-1);
  }
  .v-btn:nth-child(4) {
    border-radius: 0 rem(4) rem(4) 0;
    margin-left: rem(-1);
  }
}
</style>
