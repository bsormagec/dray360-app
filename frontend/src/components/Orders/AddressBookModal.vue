<template>
  <div>
    <v-dialog
      v-model="dialog"
      max-width="70rem"
      scrollable
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
            @click="dialog = false"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-data-table
          :headers="headers"
          :items="addressObject"
          item-key:item.id
          :hide-default-header="true"
          :hide-default-footer="true"
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
              <td>
                {{ `
                ${props.item.location_name}
                ${props.item.address_line_1}
                ${props.item.address_line_2}
                ${props.item.city},
                ${props.item.state}
                ${props.item.postal_code}
                ${props.item.t_address_id}` }}
              </td>
              <td>
                <v-btn
                  outlined
                  color="primary"
                  class="float-right"
                  @click="() => change(props.item.t_address_id)"
                >
                  Select
                </v-btn>
              </td>
            </tr>
          </template>
        </v-data-table>
      </v-card>

      <template v-slot:activator="{ on }">
        <p>
          <a v-on="on">
            Addres Book Modal
          </a>
        </p>
      </template>
    </v-dialog>
  </div>
</template>

<script>
import { mapState, mapActions } from '@/utils/vuex_mappings'
import { reqStatus } from '@/enums/req_status'
import address, { types } from '@/store/modules/address'

export default {
  props: {
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
    }
  },

  data () {
    return {
      ...mapState(address.moduleName, {
        list: state => state.list
      }),
      dialog: false,
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

  async mounted () {
    this.search = this.filters.rawtext
    await this.fetchAddressList(this.filters)

    this.loaded = true
    this.addressObject = this.list()
  },

  methods: {
    ...mapActions(address.moduleName, [types.getSearchAddress]),

    async searchApi () {
      this.addressObject.splice(0)

      await this.fetchAddressList({
        ...this.filters,
        rawtext: this.search
      })

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
      this.dialog = false
    }
  }
}
</script>

<style lang="scss" scoped>
.address__search fieldset {
  height: 5.5rem;
}
.v-card__title {
  display: flex;
  align-items: baseline;
  border-bottom: 0.1rem solid map-get($colors, grey-11);
  height: 8rem;
}
.v-data-table td {
  height: 10rem;
  width: 18rem;
}
.fullAddress {
  width: 25rem;
  margin: 3rem auto;
  span:last-child {
    width: 20rem !important;
    display: inline-block;
  }
}
.col__icon {
  width: 2rem !important;
  padding: 0rem !important;
}
</style>
