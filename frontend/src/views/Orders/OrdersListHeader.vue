<template>
  <div class="header">
    <h1 class="header__title">
      <v-icon
        :style="{ marginRight: '1.5rem' }"
        @click="toggleMobileSidebar"
      >
        mdi-menu
      </v-icon>
      Orders
    </h1>

    <div class="header__right">
      <SearchBar :style="getStyle.searchBar" />

      <Select
        label="View by Status"
        :style="getStyle.statusSelect"
        :items="statuses "
        @change="() => {}"
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
import { providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersListHeader',

  inject: [providerMethodsName],

  components: {
    Select,
    SearchBar
  },

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
        searchBar: { marginRight: '1rem' },
        statusSelect: { marginRight: '1rem' },
        columnsSelect: {}
      }

      const mobileStyles = {
        searchBar: { minWidth: '100%', marginBottom: '1rem' },
        statusSelect: { minWidth: '100%', marginBottom: '1rem' },
        columnsSelect: { minWidth: '100%', marginBottom: '1rem' }
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
    handleSelection (items) {
      const newHeaders = this.cachedHeaders.filter(({ text }) => items.find(selected => text === selected))
      this.setHeaders(newHeaders)
    },

    toggleMobileSidebar () {
      this[providerMethodsName].toggleMobileSidebar()
    }
  }
}
</script>

<style lang="scss" scoped>
.header {
  display: flex;
  align-items: center;

  @media screen and (max-width: 580px) {
    height: 7.5rem;
    position: relative;
    align-items: flex-start;
  }

  @media screen and (max-width: 380px) {
    height: 17.5rem;
  }
}

.header__right {
  display: flex;

  @media screen and (max-width: 580px) {
    position: absolute;
    bottom: 0;
  }

  @media screen and (max-width: 380px) {
    flex-wrap: wrap;
  }
}

.header__title {
  display: flex;
  align-items: center;
  width: 100%;

  button {
    @media screen and (min-width: map-get($breakpoints, med)) {
      display: none;
    }
  }
}
</style>
