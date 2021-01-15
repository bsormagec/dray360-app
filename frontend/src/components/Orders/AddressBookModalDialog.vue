<template>
  <v-dialog
    :value="isOpen"
    max-width="700px"
    scrollable
    @click:outside="() => change(undefined)"
  >
    <v-card>
      <v-card-title>
        <div
          class="d-flex flex-column"
          style="width:100%;"
        >
          <v-row
            align="center"
            no-gutters
          >
            <h5 class="secondary--text">
              Addresses
            </h5>
            <v-spacer />
            <v-btn
              icon
              @click="() => change(undefined)"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-row>
          <v-row
            align="center"
            no-gutters
          >
            <v-col cols="10">
              <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Search"
                outlined
                dense
                :class="{'mb-4': enableLocationName||enableCity||enablePostalCode||enableAddress||enableState}"
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
              <v-text-field
                v-if="enableLocationName"
                v-model="locationName"
                label="Location Name"
                outlined
                dense
                class="mb-4"
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
              <v-text-field
                v-if="enableAddress"
                v-model="address"
                label="Address"
                outlined
                dense
                :hide-details="true"
                @keypress.enter.stop="searchApi"
              />
              <v-row>
                <v-col
                  v-if="enableCity"
                  cols="5"
                >
                  <v-text-field
                    v-model="city"
                    label="City"
                    outlined
                    dense
                    class="mb-4"
                    :hide-details="true"
                    @keypress.enter.stop="searchApi"
                  />
                </v-col>
                <v-col
                  v-if="enablePostalCode"
                  cols="4"
                >
                  <v-text-field

                    v-model="postalCode"
                    label="Postal Code"
                    outlined
                    dense
                    class="mb-4"
                    :hide-details="true"
                    @keypress.enter.stop="searchApi"
                  />
                </v-col>
                <v-col
                  v-if="enableState"
                  cols="3"
                >
                  <v-text-field
                    v-model="state"
                    label="State"
                    outlined
                    dense
                    class="mb-4"
                    :hide-details="true"
                    @keypress.enter.stop="searchApi"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="2">
              <v-btn
                class="primary mx-2"
                @click="searchApi"
              >
                Search
              </v-btn>
            </v-col>
          </v-row>
        </div>
      </v-card-title>
      <div
        v-if="recognizedText"
        class="address-as-recognized"
      >
        <v-row>
          <v-col cols="1">
            <svg
              width="5"
              height="70"
              style="margin-left: 17px"
            >
              <rect
                width="5"
                height="70"
                opacity="0.26"
                left="3"
                top="9"
              />
            </svg>
          </v-col>
          <v-col cols="4">
            <span>
              Address as recognized
            </span>
          </v-col>
          <v-col cols="7">
            <p>{{ recognizedText }}</p>
            <!-- <span v-html="getMatchedAddress(recognizedText)" /> -->
          </v-col>
        </v-row>
      </div>
      <v-data-table
        :headers="headers"
        :items="addressObject"
        item-key:item.id
        :hide-default-header="true"
        :hide-default-footer="true"
        :loading="loading"
        scrollable
      >
        <template
          slot="item"
          slot-scope="props"
        >
          <tr>
            <td class="fullAddress">
              <span class="city mb-3"><strong>{{ props.item.city }}</strong></span><br>
              <span class="managed">Managed by: <a
                href=""
                color="primary"
              >
                {{ props.item.address_line_1 }}</a></span><br>
              <span class="phone">
                <v-icon
                  color="primary"
                  class="mr-3"
                  small
                >
                  mdi-phone-outline
                </v-icon>
                {{ props.item.location_phone }}
              </span><br>
              <span class="email">
                <v-icon
                  color="primary"
                  class="mr-3"
                  small
                >mdi-email-outline</v-icon>
                <a href="">{{ props.item.address_line_1 }}</a>
              </span>
            </td>
            <td class="col__icon">
              <v-icon color="primary">
                mdi-map-marker-outline
              </v-icon>
            </td>
            <td class="col__address">
              <span v-html="getMatchedAddress(props.item)" />
            </td>
            <td>
              <v-btn
                outlined
                color="primary"
                class="float-right"
                @click="() => change({ id: props.item.t_address_id, matchedAddress: getMatchedAddress(props.item), address: props.item })"
              >
                Select
              </v-btn>
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import address, { types } from '@/store/modules/address'
import { formatAddress } from '@/utils/order_form_general_functions'

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
        is_billable_address: false
      })
    },
    enableLocationName: { type: Boolean, required: false, default: false },
    enableCity: { type: Boolean, required: false, default: false },
    enablePostalCode: { type: Boolean, required: false, default: false },
    enableAddress: { type: Boolean, required: false, default: false },
    enableState: { type: Boolean, required: false, default: false }
  },

  data () {
    return {
      ...mapState(address.moduleName, {
        list: state => state.list
      }),
      loading: false,
      loaded: false,
      search: '',
      locationName: '',
      city: '',
      postalCode: '',
      address: '',
      state: '',
      addressObject: [],
      headers: [
        { text: 'addressObject', value: 'city' },
        { text: 'addressObject', value: 'managedname' },
        { text: 'fulladdress', value: 'fulladdress' },
        { text: 'Actions', value: 'actions' }
      ]
    }
  },

  computed: {
    extraSearchFieldsEnabled () {
      return this.enableLocationName ||
        this.enableCity ||
        this.enablePostalCode ||
        this.enableAddress ||
        this.enableState
    }
  },

  watch: {
    async isOpen (newVal) {
      if (!newVal || this.loaded) {
        return
      }

      this.loading = true
      if (!this.extraSearchFieldsEnabled) {
        await this.fetchAddressList(this.filters)
      }

      this.loading = false
      this.loaded = true
      this.addressObject = this.list()
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

  async mounted () {
    this.search = this.filters.rawtext
  },

  methods: {
    ...mapActions(address.moduleName, [types.getSearchAddress]),

    getMatchedAddress (item) {
      return formatAddress(item)
    },

    async searchApi () {
      this.addressObject.splice(0)

      this.loaded = false
      this.loading = true
      const extraFilters = {
        locationName: { enabled: this.enableLocationName, filterParam: 'location_name' },
        city: { enabled: this.enableCity, filterParam: 'city' },
        postalCode: { enabled: this.enablePostalCode, filterParam: 'postal_code' },
        address: { enabled: this.enableAddress, filterParam: 'address' },
        state: { enabled: this.enableState, filterParam: 'state' }
      }
      const filters = { ...this.filters, rawtext: this.search }

      for (const key in extraFilters) {
        if (extraFilters[key].enabled && this[key] !== '') {
          filters[extraFilters[key].filterParam] = this[key]
        }
      }

      await this.fetchAddressList(filters)

      this.loading = false
      this.loaded = true
      this.addressObject = this.list()
    },

    async fetchAddressList (filters) {
      filters.rawtext = this.search
      const status = await this[types.getSearchAddress](filters)

      if (status === reqStatus.success) {
        console.log('success')
      } else {
        console.log('error')
      }
    },

    change (e) {
      this.$emit('change', e)
    }
  }
}
</script>

<style lang="scss" scoped>
.v-data-table {
  td {
    height: rem(100);
    width: rem(180);
    font-size: rem(12);
  }
}
.fullAddress {
  width: 40% !important;
  margin: rem(30) auto;
  span:last-child {
    width: rem(200) !important;
    display: inline-block;
  }
}
.col__icon {
  width: 5% !important;
  padding: 0 !important;
}
.col__address {
  width: 40% !important;
  padding: 0 !important;
}
.address-as-recognized {
  background-color: #F5F6F7;
  span {
    color: var(--v-slate-gray-base);
    font-family: Roboto;
    font-style: normal;
    font-weight: bold;
    font-size: rem(12);
    margin-left: rem(-20)
  }
  p {
    color: var(--v-slate-gray-base);
    font-family: Roboto;
    font-style: normal;
    font-weight: normal;
    font-size: rem(12);
    margin-left: rem(10);
    width: 12.3rem;
  }
}
</style>
