<template>
  <div
    v-if="!has404 && !orderInReview"
    :class="`details ${loaded && 'loaded'} ${isMobile && 'mobile'}`"
  >
    <ContentLoading :loaded="loaded">
      <div :class="`details__content ${isMobile && 'mobile'}`">
        <div
          :class="`details__form ${isMobile && 'mobile'}`"
          :style="{ minWidth: `${resizeDiff}%` }"
        >
          <OrderDetailsForm
            :back-button="backButton"
            :virtual-back-button="isMobile && !backButton"
            :redirect-back="redirectBack"
            :tms-templates="tmsTemplates"
            :itg-containers="itgContainers"
            :carrier-items="carrierItems"
            :vessel-items="vesselItems"
            :options="{...formOptions}"
            @order-deleted="$emit('order-deleted')"
            @go-back="$emit('go-back')"
            @refresh="fetchFormData"
          />
          <div
            class="form__resize"
            @mousedown.prevent="handleResize"
          >
            <div role="presentation" />
          </div>
        </div>

        <OrderDetailsDocument
          v-if="!has404"
          :class="`${isMobile && 'mobile'}`"
        />
      </div>
    </ContentLoading>
  </div>
  <div
    v-else-if="!has404 && orderInReview"
    class="d-flex justify-center align-center flex-column"
    style="height: 100%"
  >
    <img
      src="../../assets/images/loading-animation.gif"
      alt="loading"
      height="132"
    >
    <h4 class="primary--text">
      Order Processing
    </h4>
  </div>
  <ContainerNotFound
    v-else
    class="container-not-found"
    container-type="ORDER"
  />
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import permissions from '@/mixins/permissions'
import locks from '@/mixins/locks'

import OrderDetailsForm from '@/views/OrderDetails/OrderDetailsForm'
import OrderDetailsDocument from '@/views/OrderDetails/OrderDetailsDocument'
import ContainerNotFound from '@/views/ContainerNotFound'
import { reqStatus } from '@/enums/req_status'
import { dictionaryItemsTypes, objectLocks } from '@/enums/app_objects_types'
import events from '@/enums/events'

import { getDictionaryItems } from '@/store/api_calls/dictionary_items'

import ContentLoading from '@/components/ContentLoading'
import orders, { types } from '@/store/modules/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import requestsList from '@/store/modules/requests-list'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import { mapState, mapActions } from 'vuex'
import { isInAdminReview } from '@/utils/status_helpers'

import get from 'lodash/get'

export default {
  name: 'OrderDetails',

  components: {
    OrderDetailsDocument,
    ContentLoading,
    OrderDetailsForm,
    ContainerNotFound
  },

  mixins: [isMobile, permissions, locks],

  props: {
    orderId: {
      type: Number,
      required: false,
      default: null
    },
    backButton: {
      type: Boolean,
      required: false,
      default: true
    },
    refreshLock: {
      type: Boolean,
      required: false,
      default: true
    },
    startingSize: {
      type: Number,
      required: false,
      default: 50
    }
  },

  data: (vm) => ({
    resizeDiff: vm.startingSize,
    minSize: 30,
    startPos: 0,
    loaded: false,
    redirectBack: false,
    tmsTemplates: [],
    itgContainers: [],
    carrierItems: [],
    vesselItems: [],
    orderIdToLoad: vm.orderId || vm.$route.params.id,
    formOptions: {
      hidden: [],
      address_search: {},
      extra: {},
      labels: {}
    },
    has404: false
  }),

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.redirectBack = from.path !== '/' // from.path.includes('/search') || from.path.includes('/inbox')
      next()
    })
  },

  computed: {
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    ...mapState(requestsList.moduleName, {
      supervise: state => state.supervise,
    }),
    ...mapState(orderForm.moduleName, {
      order: state => state.order
    }),
    companyConfiguration () {
      return get(this.currentOrder, 'company.configuration', {})
    },
    orderInReview () {
      return this.loaded &&
        !this.hasPermission('admin-review-view') &&
        isInAdminReview(this.order?.ocr_request?.latest_ocr_request_status?.status)
    }
  },
  watch: {
    async orderId (newOrderId) {
      if (newOrderId == this.orderIdToLoad) {
        return
      }
      this.loaded = false
      this.orderIdToLoad = this.orderId

      await this.fetchFormData()
    },
    startingSize: function (newVal, oldVal) {
      this.resizeDiff = newVal
    }

  },

  async beforeMount () {
    if (!this.isMobile) {
      this.setSidebar({ show: true })
    }

    await this.fetchFormData()
  },

  mounted () {
    this.initializeLockingListeners()
  },

  async beforeDestroy () {
    if (this.refreshLock) {
      this.stopRefreshingLock()
      this.releaseLockRequest({ requestId: this.order.request_id })
      this.$echo.leave('object-locking')
    }
  },

  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setConfirmationDialog, utilsActionTypes.setSidebar]),
    ...mapActions(orders.moduleName, [types.getOrderDetail]),
    ...mapActions(orderForm.moduleName, {
      setFormOrder: orderFormTypes.setFormOrder,
      setOrderLock: orderFormTypes.setOrderLock,
    }),

    async fetchFormData () {
      await this.requestOrderDetail()
      this.initializeFormOptions()
      await this.initializeLock()

      if (this.formOptions.extra.profit_tools_enable_templates) {
        await this.fetchTmsTemplates(this.currentOrder.company.id)
      }
      if (this.formOptions.extra.itg_enable_containers) {
        await this.fetchItgContainers(this.currentOrder.company.id)
      }
      if (this.formOptions.extra.enable_dictionary_items_carrier) {
        await this.fetchCarrierItems(this.currentOrder.company.id)
      }
      if (this.formOptions.extra.enable_dictionary_items_vessel) {
        await this.getchVesselItems(this.currentOrder.company.id)
      }
    },

    initializeLockingListeners () {
      this.$root.$on(events.requestsRefreshed, () => !this.refreshLock && this.fetchFormData())
      this.$root.$on(events.lockReleased, request => this.setOrderLock({ locked: true, lock: null }))
      this.$root.$on(events.lockRefreshFailed, () => this.stopRefreshingLock())
      this.$root.$on(events.lockClaimed, request => {
        if (request.request_id !== this.order.request_id) {
          return
        }

        this.setOrderLock({ locked: false, lock: null })
      })
      this.$root.$on(events.objectLocked, e => {
        const { objectLock: lock = undefined } = e

        if (lock.object_id !== this.order.request_id) {
          return
        }

        this.setOrderLock({ locked: true, lock })
      })
      this.$root.$on(events.objectUnlocked, e => {
        const { objectLock: lock = undefined } = e

        if (lock.object_id !== this.order.request_id) {
          return
        }

        this.setOrderLock({ locked: false, lock: null })
      })

      if (!this.refreshLock || this.supervise || !this.hasPermission('object-locks-create')) {
        return
      }

      this.$echo.private('object-locking')
        .listen(events.objectLocked, (e) => {
          const { objectLock: lock = undefined } = e

          if (!lock || this.userOwnsLock(lock) || lock.object_id !== this.order.request_id) {
            return
          }

          if (
            lock.lock_type === objectLocks.lockTypes.claimLock &&
            lock.object_id === this.order.request_id &&
            !this.order.is_locked
          ) {
            this.setConfirmationDialog({
              title: 'Edit-lock taken for this request',
              text: `${lock.user.name} took the edit-lock for this request`,
              confirmText: 'Ok',
              cancelText: '',
              onConfirm: () => {},
              onCancel: () => {}
            })
          }

          this.setOrderLock({ locked: true, lock })
        })
        .listen(events.objectUnlocked, async (e) => {
          const { objectLock: lock = undefined } = e

          if (!lock || this.userOwnsLock(lock) || lock.object_id !== this.order.request_id) {
            return
          }

          this.setOrderLock({ locked: false, lock: null })
          if (!this.hasPermission('object-locks-create')) {
            return
          }

          await this.setConfirmationDialog({
            title: 'Request Unlocked',
            text: 'Do you want to claim the lock?',
            onConfirm: () => {
              this.initializeLock()
            },
            onCancel: () => {}
          })
        })
    },

    async initializeLock () {
      if (
        !this.refreshLock ||
        this.shouldOmitAutolocking({ ...(this.order?.parent_ocr_request), is_locked: this.order.ocr_request_is_locked })
      ) {
        return
      }

      if (this.userOwnsLock(this.order.lock)) {
        this.refreshCurrentLock(this.order.request_id)
        this.startRefreshingLock(this.order.request_id)
        return
      }

      const [error] = await this.attemptToLockRequest({
        requestId: this.order.request_id,
        lockType: objectLocks.lockTypes.openOrder,
        updateList: false,
      })

      if (error === undefined && this.order.is_locked && !this.order.ocr_request_is_locked) {
        this.setOrderLock({ locked: false, lock: null })
      }
    },

    async fetchTmsTemplates (companyId) {
      const [error, data] = await getDictionaryItems({
        'filter[company_id]': companyId,
        'filter[item_type]': dictionaryItemsTypes.template
      })

      if (error !== undefined) {
        this.tmsTemplates = []
      }

      this.tmsTemplates = data.data
    },

    async fetchItgContainers (companyId) {
      const [error, data] = await getDictionaryItems({
        'filter[company_id]': companyId,
        'filter[item_type]': dictionaryItemsTypes.itgContainer
      })

      if (error !== undefined) {
        this.itgContainers = []
      }

      this.itgContainers = data.data
    },
    async fetchCarrierItems (companyId) {
      const [error, data] = await getDictionaryItems({
        'filter[company_id]': companyId,
        'filter[item_type]': dictionaryItemsTypes.carrier
      })

      if (error !== undefined) {
        this.carrierItems = []
      }

      this.carrierItems = data.data
    },
    async getchVesselItems (companyId) {
      const [error, data] = await getDictionaryItems({
        'filter[company_id]': companyId,
        'filter[item_type]': dictionaryItemsTypes.vessel
      })

      if (error !== undefined) {
        this.vesselItems = []
      }

      this.vesselItems = data.data
    },

    initializeFormOptions () {
      for (const key in this.companyConfiguration) {
        if (key.startsWith('hide_field_name_') && this.companyConfiguration[key]) {
          this.formOptions.hidden.push(key.replace('hide_field_name_', ''))
        } else if (key.startsWith('label_field_name_') && this.companyConfiguration[key]) {
          const newKey = key.replace('label_field_name_', '')
          this.formOptions.labels[newKey] = this.companyConfiguration[key]
        } else if (key.startsWith('address_search_')) {
          const newKey = key.replace('address_search_', '')
          this.formOptions.address_search[newKey] = this.companyConfiguration[key]
        } else {
          this.formOptions.extra[key] = this.companyConfiguration[key]
        }

        this.formOptions.field_maps = this.currentOrder.field_maps
      }
    },

    async requestOrderDetail () {
      const status = await this[types.getOrderDetail](this.orderIdToLoad)

      if (status === reqStatus.success) {
        this.setFormOrder(this.currentOrder)
        this.loaded = true
        return
      }
      this.has404 = true
      this.loaded = true
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
      if (newDiff >= 70 || newDiff <= this.minSize) {
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
  flex: 1;
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
