<template>
  <div
    :class="{
      'request__item': true,
      'disabled' : disabled,
      'active': active
    }"
    @click="handleClick"
  >
    <div class="d-flex">
      <div class="d-flex align-center">
        <span class="text-body-1 font-weight-bold secondary--text text-uppercase">#{{ request.request_id.substring(0,8) }}</span>
      </div>
      <RequestStatus
        class="ml-2 mr-auto caption"
        :status="request.latest_ocr_request_status"
      />
      <RequestItemMenu
        :request="request"
        @request-deleted="() => this.$emit('deleteRequest')"
      />
    </div>
    <div class="text-body-2">
      <div
        v-if="detailsText !== null"
        class="d-flex "
      >
        <div
          v-if="detailsTitle !== null"
          class="secondary--text details__title"
        >
          {{ detailsTitle }}
        </div>
        <div class="details__text">
          {{ detailsText }}
        </div>
      </div>
    </div>
    <div
      v-if="!canViewOtherCompanies()"
      class="text-caption pb-1"
    >
      {{ request.orders_count }} {{ request.orders_count == 1 ? 'order' : 'orders' }}
    </div>
    <div
      v-else
      class="text-caption pb-1"
    >
      {{ request.orders_count }} {{ request.orders_count == 1 ? 'order' : 'orders' }} for {{ request.company_name }}
    </div>
    <div class="d-flex justify-space-between mt-auto">
      <div />
      <div class="text-caption">
        <span class="updated-at">
          {{ formatDate(request.created_at, true) }}
        </span>
      </div>
    </div>
  </div>
</template>
<script>
import RequestStatus from '@/components/RequestStatus'
import RequestItemMenu from '@/components/RequestItemMenu'
import { formatDate } from '@/utils/dates'
import { statuses } from '@/enums/app_objects_types'
import permissions from '@/mixins/permissions'

import get from 'lodash/get'

export default {
  name: 'RequestItem',
  components: {
    RequestStatus,
    RequestItemMenu
  },
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
      return !this.hasPermission('admin-review-view') &&
          get(this.request, 'latest_ocr_request_status.status', '') === statuses.ocrPostProcessingReview
    },
    isLocked () {
      return get(this.request, 'is_locked', false)
    },
    detailsTitle () {
      if (this.request.tms_template_name !== null) {
        return 'Template:'
      } else if (this.request.first_order_bill_to_address_location_name !== null) {
        return null
      } else if (this.request.upload_user_name !== null) {
        return 'Uploaded by:'
      } else if (this.request.email_from_address !== null) {
        return 'Email from:'
      }

      return null
    },
    detailsText () {
      if (this.request.tms_template_name !== null) {
        return this.request.tms_template_name
      } else if (this.request.first_order_bill_to_address_location_name !== null) {
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
    user-select: none;
    background-color: rgba(40, 97, 160, 0.03);
    opacity: 0.75;
  }
  .details__title {
    width: fit-content;
    margin-right: rem(4);
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

  .updated-at{
    text-decoration: underline;
    color: #003C71;
  }
}
</style>
