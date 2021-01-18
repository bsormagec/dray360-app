<template>
  <div class="user__list">
    <v-data-table
      v-model="selected"
      :headers="showHeaders"
      :items="userList"
      :server-items-length="total"
      :search="search"
      show-select
      hide-default-footer
      class="user__list"
    >
      <template v-slot:top>
        <v-toolbar
          flat
          color="white"
        >
          <v-toolbar-title>
            <h1 class="user-table-title">
              {{ tableTitle }} ({{ userList.length }})
            </h1>
          </v-toolbar-title>

          <v-spacer />
          <DateRangeCalendar
            v-if="hasCalendar"
            @change="handleCalendar"
          />
          <v-text-field
            v-if="hasSearch"
            v-model="search"
            prepend-icon="mdi-magnify"
            data-cy="dashboard-search"
            label="Search by name..."
            single-line
            hide-details
            outlined
            dense
            class="search"
            @input="handleSearch"
          />

          <v-select
            v-if="hasColumnFilters"
            v-model="selectedHeaders"
            data-cy="column-select"
            :items="headers"
            color="primary"
            class="user__filter"
            dense
            outlined
            multiple
            return-object
            :chips="true"
          >
            <template v-slot:selection="{ index }">
              <span
                v-if="index === 2"
                class=""
              > Columns</span>
            </template>
          </v-select>

          <v-select
            v-if="hasBulkActions"
            :items="bulkActions"
            label="Bulk actions"
            color="primary"
            class="user__filter"
            dense
            outlined
          />
          <v-btn
            v-if="hasAddButton.showButton && hasPermission('users-create')"
            class="user_btn add__user_btn"
            @click="addUser()"
          >
            Add User
          </v-btn>
        </v-toolbar>
      </template>
      <template
        v-slot:[`item.email`]="{ item }"
      >
        <span class="text-decoration-underline primary--text">{{ item.email }}</span>
      </template>
      <template v-slot:[`item.deactivated_at`]="{ item }">
        <span>{{ item.deactivated_at === null ? 'Active' : 'Inactive' }}</span>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon
          v-if="hasPermission('users-edit')"
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          v-if="hasPermission('users-remove')"
          small
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
        <v-btn
          v-if="hasActionButton.showButton"
          class="user_btn table__action_btn ml-3"
          @click="hasActionButton.action"
        >
          View
        </v-btn>
      </template>
      <template v-slot:footer>
        <Pagination
          :loading="loading"
          :page-data="meta"
          :links="links"
          @pageIndexChange="onPageIndexChange"
        />
      </template>
    </v-data-table>
  </div>
</template>
<script>
import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
import permissions from '@/mixins/permissions'
import Pagination from '@/components/OrderTable/components/Pagination'
import { getUsers } from '@/store/api_calls/users'

export default {
  name: 'UserTable',

  components: {
    DateRangeCalendar,
    Pagination
  },

  mixins: [permissions],

  props: {
    hasColumnFilters: {
      type: Boolean,
      required: true
    },
    hasBulkActions: {
      type: Boolean,
      required: true
    },
    hasAddButton: {
      type: Object,
      required: true
    },
    hasSearch: {
      type: Boolean,
      required: true
    },
    hasCalendar: {
      type: Boolean,
      required: true
    },
    hasActionButton: {
      type: Object,
      required: true
    },
    tableTitle: {
      type: String,
      required: true
    },
    bulkActions: {
      type: Array,
      required: false,
      default: () => ([])
    }
  },

  data () {
    return {
      dialog: false,
      page: 1,
      search: '',
      searchQuery: '',
      dateRange: [],
      userList: [],
      selected: [],
      headers: [
        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Org', value: 'company.name' },
        { text: 'Permission', value: 'roles[0].name' },
        { text: 'Status', value: 'deactivated_at' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      selectedHeaders: [],
      loading: false,
      // pagination links
      total: 1,
      meta: null,
      links: null
    }
  },

  computed: {
    showHeaders () {
      return this.headers.filter(s => this.selectedHeaders.includes(s))
    }
  },
  created () {
    if (!this.isSuperadmin()) {
      this.headers.splice(2, 1)
    }
    this.selectedHeaders = this.headers
    this.fetchUsers()
  },

  methods: {

    handleSearch (e) {
      this.searchQuery = this.search || e
      // cancel pending call
      clearTimeout(this._timerId)

      // delay new call 500ms
      this._timerId = setTimeout(() => {
        this.fetchUsers()
      }, 500)
    },
    handleLocationUrl () {
      const newUrl = `?searchQuery=${this.searchQuery}`

      if (location.search !== newUrl) {
        this.$router.replace(newUrl).catch(err => { console.log(err) })
      }
    },

    handleCalendar (e) {
      if (e.length === 2) { this.$emit('dateToParent', e) }
    },

    deleteItem (e) {
      this.$emit('delete-item', e.id)
    },

    addUser () {
      this.$router.push('/user/dashboard/add-user')
    },

    editItem (item) {
      this.$router.push(`/user/dashboard/edit-user/${item.id}`)
    },
    onPageIndexChange (pageIndex) {
      this.page = pageIndex
      this.fetchUsers()
    },
    async fetchUsers () {
      const [error, data] = await getUsers({ page: this.page, 'filter[name]': this.search })
      if (error === undefined) {
        this.userList = data.data
        this.links = data.links
        this.meta = data.meta
        this.total = data.meta.total
        console.log('success')
      }
    }

  }
}
</script>
  <style lang="scss">
  .user__list {
    padding: rem(10);
    .search{
      max-width: rem(300);
       margin: 0 rem(5);
       font-size: rem(12);
    }
    .search {
      label { font-size: rem(12); }
    }
    .v-data-table__wrapper table{
      td {
        font-size: rem(12);
      }
      border: rem(2) solid map-get($colors, grey) !important;
      border-radius: rem(3);
    }
    .user__filter{
      max-width: rem(180);
      height: rem(40) !important;
      margin: 0 rem(5);
      span, label { font-size: rem(12); }
       .v-input__slot fieldset {
        border: 1px solid map-get($colors, mainblue ) !important;

      }
      .v-input__control > .v-input__slot{
        min-height: rem(20) !important;
      }
      .v-label, span{
        color: map-get($colors, mainblue ) !important;
      }
    }
    .user_btn{
        background-color: map-get($colors, mainblue) !important;
        color: map-get($colors, white );
        // .add__user_btn{
        //   width: rem(120);
        //   height: rem(40) !important;
        //   font-size: rem(12);
        // }
        .v-btn__content {
          font-size: rem(12);
        }
    }

  }

  .user-table-title {
    font-size: rem(26);
    font-weight: 700;
    letter-spacing: 0;
  }
  </style>
