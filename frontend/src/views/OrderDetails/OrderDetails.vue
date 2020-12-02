<template>
  <div :class="`details ${loaded && 'loaded'} ${isMobile && 'mobile'}`">
    <ContentLoading :loaded="loaded">
      <div :class="`details__content ${isMobile && 'mobile'}`">
        <div
          :class="`details__form ${isMobile && 'mobile'}`"
          :style="{ minWidth: `${resizeDiff}%` }"
        >
          <OrderDetailsForm :back-button="backButton" />

          <div
            class="form__resize"
            @mousedown.prevent="handleResize"
          >
            <div role="presentation" />
          </div>
        </div>

        <OrderDetailsDocument :class="`${isMobile && 'mobile'}`" />
      </div>
    </ContentLoading>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import OrderDetailsForm from '@/views/OrderDetails/OrderDetailsForm'
import OrderDetailsDocument from '@/views/OrderDetails/OrderDetailsDocument'
import { reqStatus } from '@/enums/req_status'

import ContentLoading from '@/components/ContentLoading'
import orders, { types } from '@/store/modules/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import { mapState, mapActions } from 'vuex'
import utils, { type } from '@/store/modules/utils'

export default {
  name: 'OrderDetails',

  components: {
    OrderDetailsDocument,
    ContentLoading,
    OrderDetailsForm
  },

  mixins: [isMobile],

  props: {
    orderId: {
      type: String,
      required: false,
      default: null
    },
    backButton: {
      type: Boolean,
      required: false,
      default: true
    }
  },

  data: (vm) => ({
    resizeDiff: 50,
    startPos: 0,
    loaded: false,
    orderIdToLoad: vm.orderId || vm.$route.params.id
  }),

  computed: {
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    })
  },
  watch: {
    async orderId (newOrderId) {
      if (newOrderId == this.orderIdToLoad) {
        return
      }
      this.loaded = false
      this.orderIdToLoad = this.orderId
      await this.requestOrderDetail()
    }
  },

  async beforeMount () {
    await this.requestOrderDetail()
    this.showSidebar()
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrderDetail]),
    ...mapActions(orderForm.moduleName, {
      setFormOrder: orderFormTypes.setFormOrder
    }),
    ...mapActions(utils.moduleName, [type.setSidebar]),

    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    },
    async requestOrderDetail () {
      const status = await this[types.getOrderDetail](this.orderIdToLoad)

      if (status === reqStatus.success) {
        this.setFormOrder(this.currentOrder)
        this.loaded = true
        return
      }
      console.log('error')
    },

    handleResize (e) {
      this.startPos = e.clientX
      window.onmousemove = this.startDragging
      window.onmouseup = this.stopDragging
    },

    startDragging (e) {
      e.preventDefault()
      const newDiff = this.resizeDiff * e.clientX / this.startPos

      if (newDiff >= 70 || newDiff <= 35) {
        return
      }

      this.resizeDiff = newDiff
      this.startPos = e.clientX
    },

    stopDragging (e) {
      e.preventDefault()
      window.onmousemove = undefined
      window.onmouseup = undefined
    },
    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.details {
  width: 100%;
  height: 100%;
  display: flex;

  &.mobile {
    padding-left: unset;
    overflow-x: hidden;
  }
}

.details__content {
  display: flex;
  width: 100%;

  &.mobile {
    flex-direction: column;
  }
}

.details__form {
  position: relative;
  transition: width 300ms ease;

  &::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: rem(12px);
    background: linear-gradient(90deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.05) 31.77%, rgba(0, 0, 0, 0) 100%);
    transform: translateX(#{rem(12)});
  }

  &.mobile {
    height: 50vh;
  }
}

.form__resize {
  position: absolute;
  top: 50%;
  right: 0;
  height: rem(60);
  width: rem(10);
  transform: translateY(-50%) translateX(100%) translateX(#{rem(8)});
  cursor: col-resize;
  z-index: 2;

  div {
    position: absolute;
    top: 0;
    left: 0;
    height: rem(60);
    width: rem(2);
    font-size: 0;
    background-color: #BFCCD6;

    &::after,
    &::before {
      content: "";
      position: absolute;
      top: 0;
      width: rem(2);
      height: rem(60);
      background-color: #BFCCD6;
    }

    &::after {
      transform: translateX(#{rem(4)});
    }

    &::before {
      transform: translateX(#{rem(8)});
    }
  }
}
</style>
