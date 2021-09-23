<template>
  <v-dialog
    :value="isOpen"
    max-width="550px"
    scrollable
    @click:outside="$emit('close')"
    @keydown.esc="$emit('close')"
  >
    <v-card>
      <v-card-title class="px-0 py-0">
        <div class="d-flex justify-space-between align-center px-4 py-2">
          <h6 class="secondary--text">
            Addresses
          </h6>
          <v-spacer />
          <v-btn
            icon
            @click="$emit('close')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
        <v-divider />
        <div class="recognized__address">
          <span class="d-flex secondary--text font-weight-medium caption mb-2">ADDRESS AS RECOGNIZED</span>
          <span class="d-flex secondary--text body-2">{{ recognizedText.trim() === '' || recognizedText === null ? '--' : recognizedText }}</span>
        </div>
        <v-divider />
        <div class="d-flex flex-column px-2 py-2">
          <v-row
            v-if="enableSearch"
            no-gutters
          >
            <v-col
              class="px-2 py-2"
              cols="12"
            >
              <v-text-field
                v-model="search"
                label="Search"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
          </v-row>
          <v-row
            v-if="enableAddressFilters"
            no-gutters
          >
            <v-col
              class="px-2 py-2"
              sm="4"
              cols="12"
            >
              <v-text-field
                v-model="locationName"
                label="Location Name"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
            <v-col
              class="px-2 py-2"
              sm="8"
              cols="12"
            >
              <v-text-field
                v-model="address"
                label="Address"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
            <v-col
              class="px-2 py-2"
              sm="4"
              cols="5"
            >
              <v-text-field
                v-model="city"
                label="City"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
            <v-col
              class="px-2 py-2"
              sm="2"
              cols="3"
            >
              <v-text-field
                v-model="state"
                label="State"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
            <v-col
              class="px-2 py-2"
              sm="3"
              cols="4"
            >
              <v-text-field
                v-model="postalCode"
                label="ZIP"
                placeholder=" "
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
            </v-col>
            <v-col
              class="px-2 py-2"
              sm="3"
              cols="12"
            >
              <v-btn
                class="primary"
                width="100%"
                height="100%"
                @click="searchApi"
              >
                <v-icon>mdi-magnify</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </div>
        <v-divider />
      </v-card-title>
      <v-card-text
        class="px-0"
        style="position:relative"
      >
        <v-overlay
          :value="loading"
          :absolute="true"
          :opacity="0.3"
        >
          <v-progress-circular
            indeterminate
            size="64"
          />
        </v-overlay>
        <div
          v-if="addresses.length ===0"
          class="d-flex flex-column justify-center align-center mt-4"
        >
          <img
            src="../../assets/images/container.png"
            width="25%"
          >
          <span class="primary--text font-weight-light h6 mt-4">
            No addresses...
          </span>
        </div>
        <div
          v-for="item in addresses"
          v-else
          :key="item.id"
        >
          <div class="d-flex pa-4 align-center">
            <div class="mr-auto">
              <div class="subtitle-1 secondary--text font-weight-medium">
                {{ item.location_name }}
              </div>
              <div class="body-2 black--text">
                {{ !!item.address_line_1 !== '' ? item.address_line_1 : '' }}
                {{ !!item.address_line_2 ? ` ${item.address_line_2}, ` : '' }}
                {{ !!item.city ? `${item.city}, ` : '' }}{{ !!item.state ? `${item.state}, ` : '' }}{{ !!item.postal_code ? item.postal_code : '' }}
              </div>
            </div>
            <v-btn
              class="ml-12"
              color="primary"
              dense
              outlined
              @click="() => change({ id: item.t_address_id, address: item })"
            >
              Select
            </v-btn>
            <v-btn
              v-if="saveToAll"
              class="ml-2"
              color="primary"
              dense
              outlined
              @click="() => change({ id: item.t_address_id, address: item, saveAll: true })"
            >
              Select For All
            </v-btn>
          </div>
          <v-divider />
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { getSearchAddress } from '@/store/api_calls/addresses'
import { mapActions } from 'vuex'
import utils, { actionTypes } from '@/store/modules/utils'

export default {
  props: {
    isOpen: {
      type: Boolean,
      required: true
    },
    recognizedText: {
      type: String,
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
    },
    enableSearch: {
      type: Boolean,
      required: false,
      default: true
    },
    enableAddressFilters: {
      type: Boolean,
      required: false,
      default: true
    },
    saveToAll: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      addresses: [],
      loading: false,
      search: this.filters.rawtext,
      locationName: '',
      city: '',
      postalCode: '',
      address: '',
      state: ''
    }
  },

  watch: {
    async isOpen (newVal) {
      if (!newVal || this.enableAddressFilters) {
        return
      }

      await this.fetchAddressList(this.filters)
    },
    filters: {
      handler () {
        this.search = this.filters.rawtext
        this.locationName = ''
        this.city = ''
        this.postalCode = ''
        this.address = ''
        this.state = ''
      },
      deep: true
    }
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),
    async searchApi () {
      const extraFilters = {
        locationName: { enabled: this.enableAddressFilters, filterParam: 'location_name' },
        city: { enabled: this.enableAddressFilters, filterParam: 'city' },
        postalCode: { enabled: this.enableAddressFilters, filterParam: 'postal_code' },
        address: { enabled: this.enableAddressFilters, filterParam: 'address' },
        state: { enabled: this.enableAddressFilters, filterParam: 'state' }
      }
      const filters = { ...this.filters, rawtext: this.search }

      for (const key in extraFilters) {
        if (extraFilters[key].enabled && this[key] !== '') {
          filters[extraFilters[key].filterParam] = this[key]
        }
      }

      await this.fetchAddressList(filters)
    },

    async fetchAddressList (filters) {
      filters.rawtext = this.search
      this.loading = true
      const [error, data] = await getSearchAddress(filters)

      if (error === undefined) {
        this.addresses = data.address_list
      } else if (error.message && error.message.includes('timeout')) {
        this.setSnackbar({ message: 'The search timed out. Please add more details to the search' })
      } else {
        this.setSnackbar({ message: 'There was an error in the search, please try again' })
      }
      this.loading = false
    },

    change (e) {
      this.$emit('change', e)
    }
  }
}
</script>

<style lang="scss" scoped>
.v-card__title {
  width: 100%;
  display: block;
}

.recognized__address {
  display: flex;
  flex-direction: column;
  position: relative;
  padding: rem(16) rem(32);
  margin: 0;
  background-color: rgba(var(--v-primary-base-rgb), 0.05);

  &::before{
    content: '';
    position: absolute;
    display: block;
    height: calc(100% - 24px);
    top: rem(12);
    left: rem(16);
    width: rem(5);
    background-color: rgba(var(--v-secondary-base-rgb), 0.25);
  }
}
</style>
