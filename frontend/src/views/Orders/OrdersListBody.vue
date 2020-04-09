<template>
  <div class="listbody">
    <v-data-table
      :headers="headers"
      :items="list()"
      :items-per-page="list().length"
      :hide-default-footer="true"
      mobile-breakpoint="319"
    />
  </div>
</template>

<script>
import { providerStateName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersListBody',

  inject: [providerStateName],

  props: {
    headers: {
      type: Array,
      required: true
    }
  },

  data () {
    const { list } = this[providerStateName]

    return {
      list
    }
  }
}
</script>
