<template>
  <div :class="`details ${loaded && 'loaded'} ${isMobile && 'mobile'}`">
    <ContentLoading :loaded="loaded">
      <div :class="`details__content ${isMobile && 'mobile'}`">
        <DetailsSidebar />

        <div
          :class="`details__form ${isMobile && 'mobile'}`"
          :style="{ minWidth: `${resizeDiff}%` }"
        >
          <OrderDetailsForm />

          <div
            class="form__resize"
            @mousedown.prevent="handleResize"
          >
            <div />
            <div />
            <div />
          </div>
        </div>

        <OrderDetailsDocument :class="`${isMobile && 'mobile'}`" />
      </div>
    </ContentLoading>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import DetailsSidebar from '@/views/OrderDetails/DetailsSidebar'
import OrderDetailsForm from '@/views/OrderDetails/OrderDetailsForm'
import OrderDetailsDocument from '@/views/OrderDetails/OrderDetailsDocument'
import { reqStatus } from '@/enums/req_status'

import ContentLoading from '@/components/ContentLoading'
import orders, { types } from '@/store/modules/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import { mapState, mapActions } from 'vuex'

export default {
  name: 'OrderDetails',

  components: {
    DetailsSidebar,
    OrderDetailsDocument,
    ContentLoading,
    OrderDetailsForm
  },

  mixins: [isMobile],

  data: () => ({
    resizeDiff: 50,
    startPos: 0,
    loaded: false,
    types
  }),

  computed: {
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    })
  },

  async beforeMount () {
    await this.requestOrderDetail()
    this.loaded = true
  },

  methods: {
    ...mapActions(orders.moduleName, [types.getOrderDetail]),
    ...mapActions(orderForm.moduleName, {
      setFormOrder: orderFormTypes.setFormOrder
    }),

    async requestOrderDetail () {
      const status = await this[types.getOrderDetail](this.$route.params.id)

      if (status === reqStatus.success) {
        this.setFormOrder(this.currentOrder)
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
    }
  }
}
</script>

<style lang="scss" scoped>
.details {
  width: 100%;
  height: 100%;
  display: flex;

  &.loaded {
    padding-left: rem(map-get($sizes, sidebar-desktop-width));
  }

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

  &.mobile {
    height: 50vh;
  }
}

.form__resize {
  z-index: 2;
  cursor: col-resize;
  position: absolute;
  top: 50%;
  right: rem(15);
  transform: translateY(-50%);
  transition: transform 200ms ease-in-out;
  display: flex;

  &:active {
    transform: translateY(-50%) scale(0.8);
  }

  div {
    width: rem(2);
    height: rem(60);
    background: white;

    &:not(:last-child) {
      margin-right: rem(2);
    }
  }
}
</style>
