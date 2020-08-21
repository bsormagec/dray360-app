<template>
  <div>
    <v-data-table
      :key="items.id"
      v-model="selected"
      :headers="showHeaders"
      :items="items"
      :search="search"
      show-select
      hide-default-footer

      class=" user__list"
    >
      <template v-slot:top>
        <v-toolbar
          flat
          color="white"
        >
          <v-toolbar-title><h1>{{ tableTitle }} ({{ customItems.length }})</h1></v-toolbar-title>

          <v-spacer />
          <DateRangeCalendar
            @change="handleCalendar"
          />
          <v-text-field
            v-if="hasSearch"
            v-model="search"
            prepend-icon="mdi-magnify"
            label="Search by..."
            single-line
            hide-details
            outlined
            dense
            class="search"
            @input="emitSearchToParent"
          />

          <v-select
            v-if="hasColumnFilters"
            v-model="selectedHeaders"
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
            v-if="hasAddButton.showButton"
            class="user_btn add__user_btn"
            @click="hasAddButton.action"
          >
            Add User
          </v-btn>
        </v-toolbar>
      </template>
      <template v-slot:[`item.email`]="{ item }">
        <a href="">{{ item.email }}</a>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
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
      <template v-slot:no-data>
        <v-btn
          color="primary"
          @click="initialize"
        >
          Reset
        </v-btn>
      </template>
      <template v-slot:footer />
    </v-data-table>
  </div>
</template>
<script>
import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
export default {
  name: 'GeneralTable',
  components: {
    DateRangeCalendar
  },
  props: {
    // Borrowed form OrdersList
    activePage: {
      type: Number,
      required: true
    },
    // Unique to this component
    customheaders: {
      type: Array,
      required: true
    },
    customItems: {
      type: Array,
      required: true
    },
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
      headers: [],
      search: '',
      selected: [],
      selectedHeaders: [],
      itemsPerPageArray: [4, 8, 12],
      itemsPerPage: 4,
      filter: {},
      sortDesc: false
    }
  },
  computed: {
    showHeaders () {
      return this.headers.filter(s => this.selectedHeaders.includes(s))
    }
  },

  created () {
    this.headers = Object.values(this.customheaders)
    this.selectedHeaders = this.headers
    this.initialize()

    const urlSearchQuery = new URLSearchParams(window.location.search).get('searchQuery')
    this.search = urlSearchQuery
  },

  methods: {
    initialize () {
      this.items = this.customItems
    },

    emitSearchToParent (e) {
      this.$emit('searchToParent', this.search)
    },
    handleCalendar (e) {
      this.$emit('change', e)
    }

  }
}
</script>
  <style lang="scss">
  .user__list{
    margin: 0 auto;
    .search{
      max-width: 30rem;
       margin: 0 0.5rem;
    }
    .v-data-table__wrapper table{
      border: 0.2rem solid map-get($colors, grey) !important;
      border-radius: 0.3rem;
    }
    .user__filter{
      max-width: 18rem;
      height: 4rem !important;
      margin: 0 0.5rem;

       .v-input__slot fieldset {
        border: 1px solid map-get($colors, mainblue ) !important;

      }
      .v-input__control > .v-input__slot{
        min-height: 2rem !important;
      }
      .v-label, span{
        color: map-get($colors, mainblue ) !important;
      }
    }
    .user_btn{
        background-color: map-get($colors, mainblue) !important;
        color: map-get($colors, white );
        .add__user_btn{
          width: 12rem;
          height: 4rem !important;
        }
    }

  }
  </style>
