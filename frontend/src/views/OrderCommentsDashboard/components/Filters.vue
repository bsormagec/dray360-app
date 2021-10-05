<template>
  <v-toolbar
    color="white"
    tile
    dense
    flat
    height="auto"
    class="table__tool-bar"
  >
    <v-container
      fluid
      class="pa-0 py-2"
    >
      <v-row
        align="center"
        dense
      >
        <v-col
          cols="12"
          sm="3"
          md="2"
          class="mb-3 mb-sm-0"
        >
          <v-autocomplete
            v-model="filters.companyId"
            :items="filteredCompanies"
            item-value="id"
            item-text="name"
            label="Company"
            hide-details
            multiple
            clearable
            placeholder="All Companies"
            :menu-props="{
              transition: 'scale-transition',
              offsetY: true,
              nudgeBottom: 12,
            }"
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }}</span>
              <span
                v-if="index === 1"
                class="grey--text text-caption ml-1"
              >
                {{ `(+ ${filters.companyId.length -1 } others)` }}
              </span>
            </template>
          </v-autocomplete>
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <DateRange v-model="filters.dateRange" />
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <v-autocomplete
            v-model="filters.userId"
            :items="users.data"
            :loading="users.loading"
            item-value="id"
            item-text="name"
            label="User"
            placeholder="All Users"
            clearable
            hide-details
            multiple
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }}</span>
              <span
                v-if="index === 1"
                class="grey--text text-caption ml-1"
              >
                {{ `(+ ${filters.userId.length - 1 } others)` }}
              </span>
            </template>
          </v-autocomplete>
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <v-autocomplete
            v-model="filters.roleId"
            :items="roles.data"
            :loading="roles.loading"
            item-value="id"
            item-text="name"
            label="Role"
            placeholder="All roles"
            clearable
            hide-details
            multiple
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="index === 0">{{ item.name }}</span>
              <span
                v-if="index === 1"
                class="grey--text text-caption ml-1"
              >
                {{ `(+ ${filters.roleId.length - 1 } others)` }}
              </span>
            </template>
          </v-autocomplete>
        </v-col>
        <v-col
          cols="12"
          sm="auto"
        >
          <v-text-field
            v-model="input"
            prepend-icon="mdi-magnify"
            label="Search in comments..."
            single-line
            hide-details
            clearable
          />
        </v-col>
        <v-spacer />
        <v-col cols="auto">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                small
                color="primary"
                v-bind="attrs"
                v-on="on"
                @click="resetFilters"
              >
                <v-icon dense>
                  mdi-reload
                </v-icon>
              </v-btn>
            </template>
            <span class="text-caption">Reset Filters</span>
          </v-tooltip>
        </v-col>
        <v-col cols="auto">
          <OptionList
            :value="selectedHeaders"
            :options="computedColumns"
            icon="mdi-view-column"
            title="Show and hide columns"
            @input="newVal => $emit('colChange', newVal)"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-toolbar>
</template>

<script>
import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'
import { getUsers, getRoles } from '@/store/api_calls/users'

import { commentsLabels } from '../enums/comments_labels'

import DateRange from './DateRange'
import OptionList from './OptionList'

export default {
  name: 'Filters',

  components: {
    DateRange,
    OptionList
  },

  mixins: [permissions, allCompanies],

  props: {
    initialFilters: {
      type: Object,
      required: true,
      default: () => ({
        companyId: [],
        dateRange: [],
        userId: [],
        roleId: [],
        comment: ''
      })
    },
    selectedHeaders: {
      type: Array,
      required: true,
      default: () => ([])
    }
  },

  data: (vm) => ({
    filters: {
      companyId: vm.initialFilters.companyId,
      dateRange: vm.initialFilters.dateRange,
      userId: vm.initialFilters.userId,
      roleId: vm.initialFilters.roleId,
      comment: vm.initialFilters.comment
    },
    users: {
      data: [],
      page: 1,
      perPage: 1000,
      sort: 'name',
      loading: false,
    },
    roles: {
      data: [],
      loading: false,
    },
    searchQuery: '',
    timeout: 500
  }),

  computed: {
    filteredCompanies () {
      return this.companies.filter(
        ({ name }) => !name.toLowerCase().includes('onboarding') && !name.toLowerCase().includes('demo')
      )
    },

    computedColumns () {
      return commentsLabels.map(o => ({ name: o.text, value: o.value }))
    },

    input: {
      get () {
        return this.filters.comment === '' ? '' : this.searchQuery
      },
      set (val) {
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          this.searchQuery = val
          this.filters.comment = val
        }, 500)
      }
    }
  },

  watch: {
    filters: {
      handler (newFilters) {
        this.$emit('change', newFilters)
      },
      deep: true
    }
  },

  async beforeMount () {
    if (this.canViewOtherCompanies()) {
      await this.fetchCompanies(true)
      await this.fetchAllUsers()
      await this.fetchRoles()
    }
  },

  methods: {
    resetFilters () {
      this.$emit('resetFilters')
      this.filters = this.initialFilters
    },

    async fetchAllUsers () {
      const [error, data] = await getUsers({
        page: this.users.page,
        sort: this.users.sort,
        perPage: this.users.perPage
      })
      if (error === undefined) this.users.data = data.data
      this.users.loading = false
    },

    async fetchRoles () {
      const [error, data] = await getRoles()
      this.roles.loading = true
      if (error === undefined) {
        this.roles.data = data.data
      }
      this.roles.loading = false
    }
  }
}
</script>

<style lang="scss" scoped>
.table__tool-bar::v-deep {
  position: sticky;
  top: 0;
  background-color: #FFFFFF;
  z-index: 1;
  & > .v-toolbar__content {
    padding: 0;
  }
}
.v-select.v-text-field::v-deep {
  & .v-select__selections span {
    &:first-child {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    &:last-of-type {
      width: fit-content;
    }
    & + input {
      display: none;
    }
  }
}
</style>
