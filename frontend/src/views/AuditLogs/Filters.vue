<template>
  <div>
    <v-row>
      <v-col cols="2">
        <v-autocomplete
          v-model="filters.timeRange"
          :items="timeSpans"
          item-value="hours"
          item-text="label"
          name="time_range"
          label="*Time Span"
          class="mb-4"
          outlined
          clearable
          dense
          hide-details
        />
        <DateRange
          v-if="filters.timeRange === -1"
          v-model="filters.dateRange"
          label="*Custom Date Range"
          prepend-icon=""
        />
      </v-col>
      <v-col cols="3">
        <v-autocomplete
          v-model="filters.variantName"
          :items="variants"
          item-value="abbyy_variant_name"
          item-text="abbyy_variant_name"
          name="variant_name"
          label="Variant name"
          outlined
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
        />
      </v-col>
      <v-col
        v-if="canViewOtherCompanies()"
        cols="3"
      >
        <v-autocomplete
          v-model="filters.companyId"
          :items="companies"
          item-value="id"
          item-text="name"
          name="company_id"
          label="Company"
          outlined
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
          hide-details
        />
      </v-col>
      <v-col
        v-if="canViewOtherCompanies()"
        cols="3"
      >
        <v-autocomplete
          v-model="filters.userId"
          :items="users"
          item-value="id"
          item-text="name"
          name="user_id"
          label="User"
          outlined
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
          hide-details
        />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { getUsers } from '@/store/api_calls/users'
import { getVariantList } from '@/store/api_calls/rules_editor'

import DateRange from '@/components/DateRange'

export default {
  name: 'Filters',

  components: {
    DateRange
  },

  mixins: [permissions, allCompanies],

  props: {
    initialFilters: {
      type: Object,
      required: true,
      default: () => ({
        timeRange: null,
        dateRange: [],
        companyId: null,
        userId: null,
        variantName: null,
      }),
    },
  },

  data: (vm) => ({
    users: [],
    variants: [],
    timeSpans: [
      { hours: 1, label: 'Last hour' },
      { hours: 8, label: 'Last eight hours' },
      { hours: 24, label: 'Last 24 hours' },
      { hours: 72, label: 'Last 3 days' },
      { hours: 168, label: 'Last week' },
      { hours: -1, label: 'Custom Range' },
    ],
    filters: {
      dateRange: vm.initialFilters.dateRange,
      companyId: vm.initialFilters.companyId,
      userId: vm.initialFilters.userId,
      variantName: vm.initialFilters.variantName,
    }
  }),

  watch: {
    filters: {
      handler (newFilters) {
        this.$emit('change', newFilters)
      },
      deep: true,
    }
  },

  async beforeMount () {
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies()
    }

    await this.fetchUsers()
    await this.fetchVariants()
  },

  methods: {
    async fetchUsers () {
      const [error, data] = await getUsers({ perPage: 1000, sort: 'name' })

      if (error !== undefined) {
        return
      }

      this.users = data.data
    },
    async fetchVariants () {
      const [error, data] = await getVariantList({
        'fields[t_ocrvariants]': 'abbyy_variant_name'
      })

      if (error !== undefined) {
        return
      }

      this.variants = data
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
