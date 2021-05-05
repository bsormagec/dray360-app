<template>
  <div>
    <v-autocomplete
      v-model="tmsProviderId"
      :items="tmsProviders"
      label="TMS Provider"
      placeholder="Select TMS Provider"
      item-value="id"
      item-text="name"
      dense
      outlined
      clearable
    />
    <v-autocomplete
      v-model="variantId"
      :items="variants"
      label="Variant"
      placeholder="Select Variant"
      item-value="id"
      item-text="abbyy_variant_name"
      dense
      outlined
      clearable
    />
    <v-autocomplete
      v-model="companyId"
      :items="companies"
      label="Company"
      placeholder="Select Company"
      item-value="id"
      item-text="name"
      dense
      outlined
      clearable
    />
    <!-- <v-autocomplete
      v-if="companyId"
      v-model="orderId"
      :items="orders"
      label="Order Id"
      placeholder="Select Order Id"
      item-value="id"
      item-text="id"
      dense
      outlined
      clearable
    /> -->
    <v-btn
      color="primary"
      width="100%"
      :loading="loading"
      @click="getFilteredFieldMaps"
    >
      Show options
    </v-btn>
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { mapActions, mapState } from 'vuex'
import fieldMaps, { types as fieldMapsTypes } from '@/store/modules/field_maps'
import orders, { types as ordersTypes } from '@/store/modules/orders'

import { getVariantList } from '@/store/api_calls/rules_editor'
import { getTmsProviders } from '@/store/api_calls/tms_providers'

export default {
  name: 'FieldMappingFilters',

  mixins: [permissions, allCompanies],

  props: {
    loading: { type: Boolean, required: false, default: false },
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

  watch: {
    // async companyId () {
    //   if (!this.companyId) {
    //     this.orderId = null
    //     return
    //   }
    //   await this.getOrders({ 'filter[company_id]': this.companyId })
    // },
    // async orderId () {
    //   if (!this.orderId) {
    //     this.setCurrentOrder({})
    //   }
    //   this.getOrderDetail(this.orderId)
    // },
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
    ...mapActions(fieldMaps.moduleName, {
      getFieldMaps: fieldMapsTypes.GET_FIELD_MAPS,
      setFieldMapsFilters: fieldMapsTypes.SET_FIELD_MAPS_FILTERS,
    }),
    ...mapActions(orders.moduleName, {
      getOrders: ordersTypes.getOrders,
      getOrderDetail: ordersTypes.getOrderDetail,
    }),

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
      this.$emit('fetching')
      await this.getFieldMaps(data)
      this.setFieldMapsFilters({ filters: data })
      this.$emit('done-fetching')
    },

    async fetchOcrVariants () {
      const [error, data] = await getVariantList()

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
  }
}
</script>
<style lang="scss" scoped>
</style>
