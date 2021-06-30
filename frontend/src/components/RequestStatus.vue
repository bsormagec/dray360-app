<template>
  <div
    v-if="isRejected"
    :class="{
      'status__indicator': true,
      [statusClass]: true,
      'd-flex': true,
      'align-center': true,
    }"
  >
    <v-tooltip
      attach
      nudge-bottom="36"
    >
      <template v-slot:activator="{on, attrs}">
        <span
          v-bind="attrs"
          v-on="on"
        >
          {{ status.display_status.trim() }}
        </span>
      </template>
      <span class="text-caption">
        {{ status.display_message }}
      </span>
    </v-tooltip>
  </div>
  <div
    v-else
    :class="{
      'status__indicator': true,
      [statusClass]: true,
      'd-flex': true,
      'align-center': true,
    }"
  >
    {{ status.display_status.trim() }}
  </div>
</template>

<script>
import permissions from '@/mixins/permissions'
import { cleanStrForId } from '@/utils/clean_str_for_id'
import { displayStatuses } from '@/enums/app_objects_types'

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
      return cleanStrForId(this.status.display_status.replace('(update)', ''))
    },
    isRejected () {
      return this.status.display_status === displayStatuses.rejected
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
