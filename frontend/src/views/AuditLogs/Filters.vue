<template>
  <div>
    <v-row>
      <v-col cols="1 d-flex align-center">
        <label
          for="search"
          class="filter-label"
        >
          Variant Name
        </label>
      </v-col>
      <v-col cols="2">
        <v-text-field
          v-model="filters.variantName"
          name="search"
          hide-details
          prepend-icon="mdi-magnify"
          outlined
          clearable
          dense
        />
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="1 d-flex align-center">
        <label class="filter-label">
          Date Range*
        </label>
      </v-col>
      <v-col cols="2">
        <DateRangeCalendar
          v-model="filters.dateRange"
        />
      </v-col>
    </v-row>
    <v-row v-if="canViewOtherCompanies()">
      <v-col cols="1 d-flex align-center">
        <label
          for="company_id"
          hide-details
          class="filter-label"
        >
          Company
        </label>
      </v-col>
      <v-col cols="2">
        <v-select
          v-model="filters.companyId"
          :items="companies"
          item-value="id"
          item-text="name"
          outlined
          hide-details
          name="company_id"
          clearable
          prepend-icon="mdi-office-building-outline"
          dense
          class="status-selector"
        />
      </v-col>
    </v-row>
    <v-row v-if="canViewOtherCompanies()">
      <v-col cols="1 d-flex align-center">
        <label
          for="user_id"
          hide-details
          class="filter-label"
        >
          User
        </label>
      </v-col>
      <v-col cols="2">
        <v-autocomplete
          v-model="filters.userId"
          :items="users"
          item-value="id"
          item-text="name"
          outlined
          hide-details
          name="user_id"
          clearable
          prepend-icon="mdi-office-building-outline"
          dense
          class="status-selector"
        />
      </v-col>
    </v-row>
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { getUsers } from '@/store/api_calls/users'

import DateRangeCalendar from '@/components/OrderTable/components/DateRange'

export default {
  name: 'Filters',

  components: {
    DateRangeCalendar
  },

  mixins: [permissions, allCompanies],

  props: {
    initialFilters: {
      type: Object,
      required: true,
      default: () => ({
        dateRange: [],
        companyId: null,
        userId: null,
        variantName: null,
      }),
    },
  },

  data: (vm) => ({
    users: [],
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
  },

  methods: {
    async fetchUsers () {
      const [error, data] = await getUsers({ perPage: 1000, sort: 'name' })

      if (error !== undefined) {
        return
      }

      this.users = data.data
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
