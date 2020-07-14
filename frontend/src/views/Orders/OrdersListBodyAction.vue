<template>
  <v-btn
    v-if="hasPermission('orders-view')"
    color="primary"
    :disabled="item.order.id === undefined"
    @click="() => item.action(item.order.id)"
  >
    View
  </v-btn>
</template>

<script>

import hasPermissions from '@/mixins/permissions'

export default {
  name: 'OrdersListBodyAction',
  mixins: [hasPermissions],
  props: {
    item: {
      type: Object,
      required: true
    }
  }
}
</script>
