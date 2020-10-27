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
      return item.latest_ocr_request_status.display_status.toLowerCase()
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
  width: rem(10);
  height: rem(10);
  margin-right: rem(10);
  border: rem(1) solid !important;
  border-color: lightgray !important;
  border-radius: 50%;
  background: transparent;

  &.verified {
    background: green;
    border-color: green !important;
  }

  &.rejected,&.exception {
    background: red;
    border-color: red !important;
  }

  &.intake {
    background: grey;
    border-color: grey !important;
  }

  &.processing {
    background: #397E92;
    border-color: #397E92 !important;
  }
}

.status__text {
  text-transform: capitalize;

  &.rejected,&.exception {
    color: red;
    font-weight: 700;
  }
}
</style>
