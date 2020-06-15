<template>
  <v-btn
    color="primary"
    :disabled="item.order.id === undefined"
    @click="() => item.action(item.order.id)"
  >
    View
  </v-btn>
</template>

<script>
export default {
  name: 'OrdersListBodyAction',

  props: {
    item: {
      type: Object,
      required: true
    }
  }
}
</script>
