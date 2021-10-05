<template>
  <v-toolbar
    class="field-mapping__filters"
    color="white"
    elevation="1"
    tile
    height="auto"
  >
    <v-container
      fluid
      class="px-3 py-0"
    >
      <v-row
        align="center"
        dense
      >
        <v-col cols="5">
          <v-radio-group
            :value="customMapping"
            row
            dense
            hide-details
            @click.prevent=""
          >
            <v-radio
              label="System-wide Mapping"
              :value="false"
              @mousedown="event => onFilterChange(event, false)"
            />
            <v-radio
              label="Custom Mapping"
              :value="true"
              @mousedown="event => onFilterChange(event, true)"
            />
          </v-radio-group>
        </v-col>
        <v-divider vertical />
        <v-col>
          <v-autocomplete
            v-model="tmsProviderId"
            :items="tmsProviders"
            label="TMS Provider"
            placeholder="Select TMS Provider"
            item-value="id"
            item-text="name"
            dense
            clearable
            hide-details
            :disabled="!customMapping"
            @change="getFilteredFieldMaps"
          />
        </v-col>
        <v-col>
          <v-autocomplete
            v-model="variantId"
            :items="variants"
            label="Variant"
            placeholder="Select Variant"
            item-value="id"
            item-text="abbyy_variant_name"
            dense
            clearable
            hide-details
            :disabled="!customMapping"
            @change="getFilteredFieldMaps"
          />
        </v-col>
        <v-col>
          <v-autocomplete
            v-model="companyId"
            :items="companies"
            label="Company"
            placeholder="Select Company"
            item-value="id"
            item-text="name"
            dense
            clearable
            hide-details
            :disabled="!customMapping"
            @change="getFilteredFieldMaps"
          />
        </v-col>
        <v-spacer />
      </v-row>
    </v-container>
  </v-toolbar>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { mapActions, mapState } from 'vuex'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import fieldMaps, { actionTypes as fieldMapsActionTypes } from '@/store/modules/field_maps'
import orders, { types as ordersTypes } from '@/store/modules/orders'

import { getVariants } from '@/store/api_calls/rules_editor'
import { getTmsProviders } from '@/store/api_calls/tms_providers'

export default {
  name: 'FieldMappingFilters',

  mixins: [permissions, allCompanies],

  props: {
    loading: { type: Boolean, required: false, default: false },
    customMapping: { type: Boolean, required: false, default: false },
    formChanged: { type: Boolean, required: false, default: false },
  },

  data: () => ({
    tmsProviderId: null,
    variantId: null,
    companyId: null,
    orderId: null,
    variants: [],
    tmsProviders: [],
  }),

  computed: {
    ...mapState(orders.moduleName, {
      orders: state => state.list,
    }),
  },

  async beforeMount () {
    this.$emit('fetching')
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies()
    }

    await this.fetchOcrVariants()
    await this.fetchTmsProviders()
    this.$emit('done-fetching')
  },

  methods: {
    ...mapActions(fieldMaps.moduleName, [
      fieldMapsActionTypes.getFieldMaps,
      fieldMapsActionTypes.setFieldMapsFilters,
    ]),

    ...mapActions(orders.moduleName, {
      getOrders: ordersTypes.getOrders,
      getOrderDetail: ordersTypes.getOrderDetail,
    }),

    ...mapActions(utils.moduleName, [
      utilsActionTypes.setConfirmationDialog,
    ]),

    async getFilteredFieldMaps () {
      const data = {}

      if (this.companyId) {
        data.companyId = this.companyId
      }
      if (this.tmsProviderId) {
        data.tmsProviderId = this.tmsProviderId
      }
      if (this.variantId) {
        data.variantId = this.variantId
      }
      if (!this.companyId && !this.tmsProviderId && !this.variantId) {
        this.$emit('set-custom-mapping', false)
      }
      this.$emit('fetching')
      await this.getFieldMaps(data)
      this.setFieldMapsFilters({ filters: data })
      this.$emit('done-fetching')
    },

    async fetchOcrVariants () {
      const [error, data] = await getVariants()

      if (error !== undefined) {
        return
      }

      this.variants = data
    },

    async fetchTmsProviders () {
      const [error, data] = await getTmsProviders({
        sort: 'name'
      })

      if (error !== undefined) {
        return
      }

      this.tmsProviders = data.data
    },

    onFilterChange (event, value) {
      const updateValue = (val) => {
        if (!val) {
          this.companyId = null
          this.tmsProviderId = null
          this.variantId = null
          this.getFilteredFieldMaps()
        }
        this.$emit('set-custom-mapping', val)
      }
      if (this.formChanged) {
        event.preventDefault()
        this.setConfirmationDialog({
          title: 'Unsaved changes detected',
          text: 'Are you sure you want to leave this page without saving? Your changes will be lost.',
          onConfirm: () => {
            updateValue(value)
            this.$emit('cancel-edit')
          },
          onCancel: () => {
            this.$emit('set-custom-mapping', this.customMapping)
          }
        })
        return
      }
      updateValue(value)
      this.$emit('cancel-edit')
    }
  }
}
</script>

<style lang="scss" scoped>
.field-mapping__filters::v-deep {
  .v-toolbar__content {
    padding: 0;
    .v-select {
      margin: rem(16) 0 rem(8) rem(8);
    }
  }
}
</style>
