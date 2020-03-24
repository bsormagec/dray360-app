<template>
  <v-data-table
    :headers="headers"
    :items="list()"
    :items-per-page="list().length"
    :hide-default-footer="true"
  />
</template>

<script>
import { providerStateName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersList',

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
