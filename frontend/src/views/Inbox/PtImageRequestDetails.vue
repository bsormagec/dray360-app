<template>
  <ContentLoading :loaded="loaded">
    <v-btn
      v-if="isMobile"
      color="primary"
      outlined
      x-small
      width="32"
      height="32"
      class="px-0 mr-4"
      title="Go back to Orders List"
      @click="$emit('go-back')"
    >
      <v-icon>
        mdi-chevron-left
      </v-icon>
    </v-btn>
    <div
      v-if="requestDetails === null"
      class="empty__order"
    >
      <img
        src="../../assets/images/container.png"
        width="20%"
      >
      <span class="primary--text font-weight-light h6 mt-8">
        There was an error loading the data
      </span>
    </div>
    <div
      v-else
      class="requests__details"
    >
      <ul
        style="list-style-type: none;"
        class="requests__details-list"
      >
        <li class="item">
          <span class="item-title">Image Type</span>
          <span class="item-detail">{{ requestDetails.status_metadata.event_info.pt_image_type }}</span>
        </li>
        <li class="item">
          <span class="item-title">Filename</span>
          <span class="item-detail">{{ requestDetails.status_metadata.event_info.original_filename }}</span>
        </li>
        <li class="item">
          <span class="item-title">Uploaded</span>
          <span class="item-detail">{{ formatDate(requestDetails.status_metadata.datetime_utciso, { timeZone: true }) }}</span>
        </li>
        <li class="item">
          <span class="item-title">Uploaded by</span>
          <span class="item-detail">{{ requestDetails.user.name }}</span>
        </li>
        <li class="item">
          <span class="item-title">TMS Shipment ID</span>
          <span class="item-detail">{{ requestDetails.status_metadata.event_info.tms_shipment_id }}</span>
        </li>
        <li
          v-if="requestDetails.status_metadata.event_info.order_id !== ' '"
          class="item"
        >
          <span class="item-title">Order ID</span>
          <span class="item-detail">{{ requestDetails.status_metadata.event_info.order_id }}</span>
        </li>
      </ul>
      <div class="image-loader">
        <img
          v-if="!imageLoaded"
          src="@/assets/images/loading-animation.gif"
        >
      </div>
      <img
        v-show="imageLoaded"
        :src="requestDetails.presigned_image_url"
        width="60%"
        @load="handleLoad"
      >
    </div>
  </ContentLoading>
</template>

<script>
import isMobile from '@/mixins/is_mobile'

import { formatDate } from '@/utils/dates'
import { getPtImageRequestDetails } from '@/store/api_calls/requests'

import ContentLoading from '@/components/ContentLoading'

export default {
  name: 'PtImageRequestDetails',

  components: { ContentLoading },

  mixins: [isMobile],

  props: {
    request: {
      type: Object,
      required: true,
      default: () => ({ request_id: null })
    }
  },

  data: () => ({
    loaded: false,
    imageLoaded: false,
    requestDetails: null
  }),

  watch: {
    request: {
      handler () {
        this.loadRequestDetails()
      },
      deep: true,
    }
  },

  beforeMount () {
    this.loadRequestDetails()
  },

  beforeDestroy () {
    this.requestDetails = null
    this.loaded = false
    this.imageLoaded = false
  },

  methods: {
    formatDate,

    handleLoad (e) {
      this.imageLoaded = true
    },

    async loadRequestDetails () {
      this.loaded = false
      const [error, data] = await getPtImageRequestDetails(this.request.request_id)

      if (error === undefined) {
        this.requestDetails = data.data
      } else {
        this.requestDetails = null
      }

      this.loaded = true
    }
  }
}
</script>

<style lang="scss" scoped>
.empty__order{
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.requests__details{
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;

  .image-loader {
    height: 80%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .requests__details-list {
    padding: rem(8);
    margin: rem(16) 0 0 rem(16);
    min-width: rem(310);
    align-self: flex-start;
    background-color: #E6ECF1;
  }

  .item {
    display: flex;
    justify-content: space-between;
    .item-title {
      font-weight: 700 !important;
      color: var(--v-black-base);
    }
    .item-detail {
      color: var(--v-black-base);
      font-size: 0.875rem;
    }
  }

}
</style>
