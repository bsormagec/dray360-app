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

    <div
      class="header__dropdown"
      :style="{width: `${cachedHeaders.length * 8}rem`}"
    >
      <v-select
        v-model="selectedItems"
        label="Columns"
        :items="headerItems"
        solo
        dense
        multiple
        persistent-hint
        @change="handleSelection"
      />
    </div>
  </div>
</template>

<script>
import { providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersListHeader',

  inject: [providerMethodsName],

  props: {
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
    selectedItems: []
  }),

  beforeMount () {
    this.cachedHeaders = this.headers
    this.headerItems = this.cachedHeaders.map(({ text }) => text)
    this.selectedItems = this.headerItems
  },

  methods: {
    handleSelection (items) {
      const newHeaders = this.cachedHeaders.filter(({ text }) => this.selectedItems.find(selected => text === selected))
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

.header__dropdown {
  max-width: 52rem;
}
</style>
