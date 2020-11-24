<template>
  <v-dialog
    :value="isOpen"
    max-width="700px"
    scrollable
    @click:outside="() => change(undefined)"
  >
    <v-card>
      <v-card-title>
        <h1>Addresses</h1>
        <v-spacer />
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          outlined
          dense
          class="address__search"
        />
        <v-btn
          class="primary mx-2"
          @click="searchApi"
        >
          Search
        </v-btn>
        <v-btn
          icon
          @click="() => change(undefined)"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
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
                :loading="isLoading"
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
    isLoading: {
      type: Boolean,
      required: true,
      default: false
    }
  },

  data () {
    return {
      ...mapState(address.moduleName, {
        list: state => state.list
      }),
      loading: false,
      loaded: false,
      search: '',
      addressObject: [],
      headers: [
        { text: 'addressObject', value: 'city' },
        { text: 'addressObject', value: 'managedname' },
        { text: 'fulladdress', value: 'fulladdress' },
        { text: 'Actions', value: 'actions' }
      ]
    }
  },

  watch: {
    async isOpen (newVal) {
      if (!newVal || this.loaded) {
        return
      }

      this.loading = true
      await this.fetchAddressList(this.filters)

      this.loading = false
      this.loaded = true
      this.addressObject = this.list()
    }
  },

  async mounted () {
    this.search = this.filters.rawtext
  },

  updated () {
    console.log('addressbook modal is loading: ', this.isLoading)
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
      await this.fetchAddressList({
        ...this.filters,
        rawtext: this.search
      })

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
.address__search fieldset {
  height: rem(55);
}
.v-card__title {
  display: flex;
  align-items: baseline;
  border-bottom: rem(1) solid map-get($colors, grey-11);
  height: rem(80);
  h1 {
    font-size: rem(26);
    font-weight: 700;
    letter-spacing: rem(.15);
    line-height: (20 / 26);
  }
}
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
    color: map-get($colors, slate-gray);
    font-family: Roboto;
    font-style: normal;
    font-weight: bold;
    font-size: rem(12);
    margin-left: rem(-20)
  }
  p {
    color: map-get($colors, slate-gray);
    font-family: Roboto;
    font-style: normal;
    font-weight: normal;
    font-size: rem(12);
    margin-left: rem(10);
    width: 12.3rem;
  }
}
</style>
