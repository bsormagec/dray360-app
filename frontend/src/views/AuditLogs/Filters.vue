<template>
  <v-container
    fluid
    class="pa-0 pb-4"
  >
    <v-row
      align="start"
      dense
    >
      <v-col cols="2">
        <v-autocomplete
          v-model="filters.timeRange"
          :items="timeSpans"
          item-value="hours"
          item-text="label"
          name="time_range"
          label="*Time Span"
          clearable
          dense
          hide-details
        />
      </v-col>
      <v-col cols="auto">
        <v-expand-x-transition>
          <DateRange
            v-show="filters.timeRange === -1"
            v-model="filters.dateRange"
            label="*Custom Date Range"
            prepend-icon=""
            :input-attributes="{ outlined: false }"
          />
        </v-expand-x-transition>
      </v-col>
      <v-col cols="2">
        <v-autocomplete
          v-model="filters.variantId"
          :items="variants"
          item-value="abbyy_variant_id"
          item-text="abbyy_variant_id"
          name="abbyy_variant_id"
          label="Variant Id"
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
          hide-details
        >
          <template v-slot:selection="{ item, index }">
            <v-chip
              v-if="index === 0"
              close
              small
              @click:close="handleFilterDeletion(item.abbyy_variant_id, 'variantId')"
            >
              <span>{{ item.abbyy_variant_id }}</span>
            </v-chip>
            <span
              v-if="index === 1"
              class="grey--text caption"
            >
              (+{{ filters.variantId.length - 1 }} others)
            </span>
          </template>
        </v-autocomplete>
      </v-col>
      <v-col
        v-if="canViewOtherCompanies()"
        cols="2"
      >
        <v-autocomplete
          v-model="filters.companyId"
          :items="companies"
          item-value="id"
          item-text="name"
          name="company_id"
          label="Company"
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
          hide-details
        >
          <template v-slot:selection="{ item, index }">
            <v-chip
              v-if="index === 0"
              close
              small
              @click:close="handleFilterDeletion(item.id, 'companyId')"
            >
              <span>{{ item.name }}</span>
            </v-chip>
            <span
              v-if="index === 1"
              class="grey--text caption"
            >
              (+{{ filters.companyId.length - 1 }} others)
            </span>
          </template>
        </v-autocomplete>
      </v-col>
      <v-col
        v-if="canViewOtherCompanies()"
        cols="2"
      >
        <v-autocomplete
          v-model="filters.userId"
          :items="users"
          item-value="id"
          item-text="name"
          name="user_id"
          label="User"
          clearable
          dense
          chips
          deletable-chips
          multiple
          small-chips
          hide-details
        >
          <template v-slot:selection="{ item, index }">
            <v-chip
              v-if="index === 0"
              close
              small
              @click:close="handleFilterDeletion(item.id, 'userId')"
            >
              <span>{{ item.name }}</span>
            </v-chip>
            <span
              v-if="index === 1"
              class="grey--text caption"
            >
              (+{{ filters.userId.length - 1 }} others)
            </span>
          </template>
        </v-autocomplete>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { getUsers } from '@/store/api_calls/users'
import { getVariants } from '@/store/api_calls/rules_editor'

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
        variantId: null,
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
      variantId: vm.initialFilters.variantId,
      timeRange: vm.initialFilters.timeRange
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
      const [error, data] = await getVariants({
        'fields[t_ocrvariants]': 'abbyy_variant_id',
        sort: 'abbyy_variant_id'
      })

      if (error !== undefined) {
        return
      }

      this.variants = data
    },

    handleFilterDeletion (item, filter) {
      this.filters[filter] = this.filters[filter].filter(s => s !== item)
    }
  }
}
</script>

<style lang="scss" scoped>
.v-select.v-input--dense::v-deep .v-chip {
  margin: rem(3)
}
</style>
