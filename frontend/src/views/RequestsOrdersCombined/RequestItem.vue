<template>
  <div
    :class="{
      'request__item': true,
      'disabled' : disabled,
      'active': active
    }"
    @click="handleClick"
  >
    <div class="d-flex justify-space-between">
      <div class="d-flex align-center">
        <span class="text-body-1 font-weight-bold secondary--text text-uppercase">#{{ request.request_id.substring(0,8) }}</span>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-clipboard:copy="request.request_id"
              icon
              small
              color="primary"
              v-bind="attrs"
              v-on="on"
              @click.stop="() =>{}"
            >
              <v-icon>mdi-link</v-icon>
            </v-btn>
          </template>
          <span>Copy Request ID</span>
        </v-tooltip>
      </div>
      <RequestStatus
        :status="request.latest_ocr_request_status"
      />
    </div>
    <div class="text-body-2">
      <div
        v-if="detailsText !== null"
        class="d-flex "
      >
        <div
          v-if="detailsTitle !== null"
          class="secondary--text .details__title"
        >
          {{ detailsTitle }}
        </div>
        <div class="details__text">
          {{ detailsText }}
        </div>
      </div>
    </div>
    <div class="text-caption pb-1" v-if="!isSuperadmin()">
      {{ request.orders_count }} {{ request.orders_count == 1 ? 'order' : 'orders' }}
    </div>
    <div class="text-caption pb-1" v-else>
      {{ request.orders_count }} {{ request.orders_count == 1 ? 'order' : 'orders' }} for {{ request.company_name }}
    </div>
    <div class="d-flex justify-space-between mt-auto">
      <div />
      <div class="text-caption">
        <span class="secondary--text">
          {{ request.updated_at === null ? 'Created at: ' : 'Updated at: ' }}
        </span>
        <span class="secundary">
          {{ formatDate(request.updated_at || request.created_at) }}
        </span>
      </div>
    </div>
  </div>
</template>
<script>
import RequestStatus from '@/components/RequestStatus'

import { formatDate } from '@/utils/dates'
import permissions from '@/mixins/permissions'

export default {
  name: 'RequestItem',
  components: { RequestStatus },
  mixins: [permissions],
  props: {
    request: {
      type: Object,
      required: true
    },
    active: {
      type: Boolean,
      required: false,
      default: false
    }

  },
  computed: {
    disabled () {
      return this.request.orders_count === 0
    },
    detailsTitle () {
      if (this.request.first_order_bill_to_address_location_name !== null) {
        return null
      } else if (this.request.upload_user_name !== null) {
        return 'Uploaded by:'
      } else if (this.request.email_from_address !== null) {
        return 'Email from:'
      }

      return null
    },
    detailsText () {
      if (this.request.first_order_bill_to_address_location_name !== null) {
        return this.request.first_order_bill_to_address_location_name
      } else if (this.request.upload_user_name !== null) {
        return this.request.upload_user_name
      } else if (this.request.email_from_address !== null) {
        return this.request.email_from_address
      }

      return null
    }
  },
  methods: {
    formatDate,
    handleClick () {
      if (this.disabled) {
        return
      }

      this.$emit('change', this.request)
    }
  }
}
</script>
<style lang="scss" scoped>
.request__item {
  cursor: pointer;
  display: flex;
  flex-flow: column nowrap;
  height: 105px;
  padding: rem(10);
  &:hover {
    background-color:rgba(40, 97, 160, 0.03);
  }
  &.active {
    background-color: #E6ECF1;
  }

  &.disabled {
    cursor: not-allowed;
    background-color: rgba(40, 97, 160, 0.03);
    opacity: 0.75;
  }
  .details__title {
    width: fit-content;
  }
  .details__text {
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 50%;
    overflow: hidden;
  }

  .request__item--footer{
    display: flex;
  }
}
</style>
