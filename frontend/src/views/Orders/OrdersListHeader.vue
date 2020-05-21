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

    <SearchBar :style="{ marginRight: '1rem' }" />

    <Select
      label="View by Status"
      :style="{ marginRight: '1rem' }"
      :items="statuses "
      @change="() => {}"
    />

    <Select
      label="Columns"
      :items="headerItems"
      @change="handleSelection"
    />
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
    headerItems: []
  }),

  beforeMount () {
    this.cachedHeaders = this.headers
    this.headerItems = this.cachedHeaders.map(({ text }) => text)
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
  align-items: flex-start;
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
