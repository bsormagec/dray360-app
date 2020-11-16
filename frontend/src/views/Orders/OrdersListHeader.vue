<template>
  <div class="header">
    <h5 class="header__title">
      <v-app-bar-nav-icon
        v-if="isMobile"
        class=""
        @click.stop="toogleSidebar()"
      />Orders
    </h5>

    <div class="header__right">
      <DateRangeCalendar
        @change="handleCalendar"
        @click:clear="handleCalendarInput"
      />
      <SearchBar
        :style="getStyle.searchBar"
        @change="handleSearch"
      />

      <Select
        label="View by Status"
        :style="getStyle.statusSelect"
        :items="statuses"
        :selected-items="selectedItems"
        @change="handleStatuses"
      />

      <Select
        label="Columns"
        :style="getStyle.columnsSelect"
        :items="headerItems"
        @change="handleSelection"
      />
    </div>
  </div>
</template>

<script>
import Select from '@/components/Select'
import SearchBar from '@/components/SearchBar'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'
import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
import utils, { type } from '@/store/modules/utils'
import { mapActions } from 'vuex'
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'OrdersListHeader',

  components: {
    Select,
    SearchBar,
    DateRangeCalendar
  },
  mixins: [isMobile],

  inject: [providerStateName, providerMethodsName],

  props: {
    statuses: {
      type: Array,
      required: true
    },
    headers: {
      type: Array,
      required: true
    },
    setHeaders: {
      type: Function,
      required: true
    },
    selectedItems: {
      type: Array,
      required: true
    }
  },

  data: () => ({
    cachedHeaders: [],
    headerItems: [],
    clientWidth: window.innerWidth
  }),

  computed: {
    getStyle () {
      const mobileTrigger = this.clientWidth <= 380

      const desktopStyles = {
        searchBar: { marginRight: '10px' },
        statusSelect: { marginRight: '10px', minWidth: '140px' },
        columnsSelect: {}
      }

      const mobileStyles = {
        searchBar: { minWidth: '100%', marginBottom: '10px' },
        statusSelect: { minWidth: '100%', marginBottom: '10px' },
        columnsSelect: { minWidth: '100%', marginBottom: '10px' }
      }

      return mobileTrigger ? mobileStyles : desktopStyles
    }
  },

  beforeMount () {
    this.cachedHeaders = this.headers
    this.headerItems = this.cachedHeaders.map(({ text }) => text)
    window.addEventListener('resize', () => (this.clientWidth = window.innerWidth))
  },

  methods: {
    ...mapActions(utils.moduleName, [type.getTenantConfig, type.setSidebar]),
    handleCalendar (e) {
      if (e.length === 2) {
        const filters = {
          'filter[created_between]': e.toString()
        }
        this[providerMethodsName].setSearchFilter(filters)
        this[providerMethodsName].fetchOrdersList({
          page: this[providerStateName].activePage(),
          ...filters
        })
      }
    },
    handleCalendarInput (e) {
      const filters = {
        'filter[created_between]': ' '
      }
      this[providerMethodsName].setSearchFilter(filters)
      this[providerMethodsName].fetchOrdersList({
        page: this[providerStateName].activePage(),
        ...filters
      })
    },
    handleSearch (search) {
      const filters = {
        'filter[query]': search
      }

      this[providerMethodsName].setSearchFilter(filters)
      this[providerMethodsName].fetchOrdersList({
        page: this[providerStateName].activePage(),
        ...filters
      })
    },

    handleStatuses (statuses) {
      this[providerMethodsName].setStatusFilter(statuses)
      this[providerMethodsName].fetchOrdersList({
        page: this[providerStateName].activePage()
      })
    },

    handleSelection (items) {
      const newHeaders = this.cachedHeaders.filter(({ text }) => items.find(selected => text === selected))
      this.setHeaders(newHeaders)
    },
    toogleSidebar () {
      this[type.setSidebar]({ show: !this.showSidebar })
    }
  }
}
</script>

<style lang="scss" scoped>
.header {
  display: flex;
  align-items: center;

  @media screen and (max-width: 580px) {
    height: rem(75);
    position: relative;
    align-items: flex-start;
  }

  @media screen and (max-width: 380px) {
    height: rem(175);
  }
}

.header__right {
  display: flex;
  align-items: center;
  @media screen and (max-width: 580px) {
    position: absolute;
    bottom: 0;
    top:rem(35)
  }

  @media screen and (max-width: 380px) {
    flex-wrap: wrap;
  }
}

.header__title {
  display: flex;
  align-items: center;
  width: 100%;
  font-weight: bold;
  font-size: rem(26);

  button {
    @include media("med") {
      display: none;
    }
  }
}
</style>
