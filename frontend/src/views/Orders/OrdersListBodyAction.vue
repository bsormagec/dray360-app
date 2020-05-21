<template>
  <v-btn
    color="primary"
    @click="() => item.action(item.id)"
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
