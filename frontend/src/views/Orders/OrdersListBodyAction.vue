<template>
  <v-btn
    color="primary"
    @click="() => item.action(item.order.id)"
    :disabled="item.order.id === undefined"
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
