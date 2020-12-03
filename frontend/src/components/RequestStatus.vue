<template>
  <div
    :class="{
      'status__indicator': true,
      [statusClass]: true,
    }"
  >
    {{ status.display_status.trim() }}
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import { cleanStrForId } from '@/utils/clean_str_for_id'

export default {
  name: 'RequestStatus',
  mixins: [permissions],

  props: {
    status: {
      type: Object,
      required: true,
      default: () => ({ status: '', display_status: '' })
    }
  },
  computed: {
    statusClass () {
      return cleanStrForId(this.status.display_status)
    }
  }
}
</script>

<style lang="scss" scoped>
.status__indicator {
  &::after{
    content: '';
    display: inline-block;
    width: rem(10);
    height: rem(10);
    -moz-border-radius: rem(5);
    -webkit-border-radius: rem(5);
    border-radius: rem(5);
  }
  &.processed,
  &.sendingtotms,
  &.acceptedbytms,
  &.senttotms {
    &::after{
      background-color: #77C19A;
    }
  }
  &.intake::after{
    border: 2px solid #B0BBC4;
  }
  &.processing,
  &.update {
    &::after{
      background-color: #7BAFD4;
    }
  }
  &.exception,
  &.rejected,
  &.tmserror {
    &::after {
      background-color: #FB7660;
    }
  }
  &.tmswarning,
  &.replaced {
    &::after {
      background-color: #FDAA63;
    }
  }
}
</style>
