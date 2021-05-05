<template>
  <div
    :class="{
      'status__indicator': true,
      [statusClass]: true,
      'd-flex': true,
      'align-center': true,
    }"
  >
    {{ status.display_status.trim() }}
  </div>
  <!-- <span
          v-if="item.latest_ocr_request_status.display_status.toLowerCase() === 'processed'"
          class="processed-status"
        >
          {{ item.latest_ocr_request_status.display_status }}
        </span>
        <Chip
          v-else
          x-small
          v-bind="getStatusChip(item)"
        >
          {{ item.latest_ocr_request_status.display_status }}
        </Chip> -->
</template>

<script>
import Chip from '@/components/Chip'

import permissions from '@/mixins/permissions'
import { cleanStrForId } from '@/utils/clean_str_for_id'

export default {
  name: 'RequestStatus',
  components: { Chip },
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
      return cleanStrForId(this.status.display_status.replace('(update)', ''))
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
    margin-left: rem(4);
    border-radius: rem(5);
  }
  @import "@/assets/styles/components/_statuses.scss";
}
</style>
