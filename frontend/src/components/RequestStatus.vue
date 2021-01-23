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
    border-radius: rem(5);
  }
  @import "@/assets/styles/components/_statuses.scss";
}
</style>
