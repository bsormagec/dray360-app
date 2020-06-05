<template>
  <div class="status">
    <div
      :class="`status__circle ${getText(item)}`"
    />
    <span :class="`status__text ${getText(item)}`">{{ getText(item) }}</span>
  </div>
</template>

<script>
export default {
  name: 'OrdersListBodyStatus',

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  methods: {
    getText (item) {
      let status = item.ocr_request.latest_ocr_request_status.status.split('-')
      status = status[status.length - 1]

      return status
    }
  }
}
</script>

<style lang="scss" scoped>
.status {
  display: flex;
  align-items: center;
}

.status__circle {
  width: 1rem;
  height: 1rem;
  margin-right: 1rem;
  border: 0.1rem solid !important;
  border-color: lightgray !important;
  border-radius: 50%;
  background: transparent;

  &.complete {
    background: green;
    border-color: green !important;
  }

  &.rejected {
    background: red;
    border-color: red !important;
  }
}

.status__text {
  text-transform: capitalize;

  &.rejected {
    color: red;
    font-weight: bold;
  }
}
</style>
