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
            :options="formOptions"
            :refresh-lock="refreshLock"
            @order-deleted="$emit(events.orderDeleted)"
            @order-replicated="$emit(events.orderReplicated)"
            @go-back="$emit('go-back')"
            @refresh="fetchFormData"
            @lock-requested="handleClaimLock"
            @lock-released="handleReleaseLock"
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
import statusUpdatesSubscribe from '@/mixins/status_updates_subscribe'
import permissions from '@/mixins/permissions'
import locks from '@/mixins/locks'

import OrderDetailsForm from '@/views/OrderDetails/OrderDetailsForm'
import OrderDetailsDocument from '@/views/OrderDetails/OrderDetailsDocument'
import ContainerNotFound from '@/views/ContainerNotFound'
import { reqStatus } from '@/enums/req_status'
import { objectLocks } from '@/enums/app_objects_types'
import events from '@/enums/events'

import ContentLoading from '@/components/ContentLoading'
import orders, { types } from '@/store/modules/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import requestsList from '@/store/modules/requests-list'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import { mapState, mapActions } from 'vuex'
import { isInAdminReview } from '@/utils/status_helpers'

import get from 'lodash/get'
import cloneDeep from 'lodash/cloneDeep'

const defaultFormOptions = {
  hidden: [],
  address_search: {},
  extra: {},
  labels: {}
}

export default {
  name: 'OrderDetails',

  components: {
    OrderDetailsDocument,
    ContentLoading,
    OrderDetailsForm,
    ContainerNotFound
  },

  mixins: [permissions, locks, statusUpdatesSubscribe],

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
    }
  },

  data: (vm) => ({
    resizeDiff: 35,
    minSize: 30,
    startPos: 0,
    loaded: false,
    redirectBack: false,
    orderIdToLoad: vm.orderId || vm.$route.params.id,
    formOptions: cloneDeep(defaultFormOptions),
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

    events () {
      return events
    },

    companyConfiguration () {
      return get(this.currentOrder, 'company.configuration', {})
    },
    orderInReview () {
      return this.loaded &&
        !this.hasPermission('admin-review-view') &&
        isInAdminReview(this.order?.ocr_request?.latest_ocr_request_status?.status)
    },
    isMobile () {
      return this.$vuetify.breakpoint.mobile
    },
  },
  watch: {
    async orderId (newOrderId) {
      // eslint-disable-next-line eqeqeq
      if (newOrderId == this.orderIdToLoad) {
        return
      }
      this.leaveRequestStatusUpdatesChannel(`-order${this.orderIdToLoad}`)
      this.orderIdToLoad = this.orderId

      await this.fetchFormData()
      this.initializeStateUpdatesListeners()
    }
  },

  async beforeMount () {
    await this.fetchFormData()
    this.initializeStateUpdatesListeners()
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
    this.leaveRequestStatusUpdatesChannel(`-order${this.order.id}`)
    this.removeRootListeners()
  },

  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setConfirmationDialog]),
    ...mapActions(orders.moduleName, [types.getOrderDetail]),
    ...mapActions(orderForm.moduleName, {
      setFormOrder: orderFormTypes.setFormOrder,
      setOrderLock: orderFormTypes.setOrderLock,
      updateOrderStatus: orderFormTypes.updateOrderStatus,
    }),

    async fetchFormData () {
      this.loaded = false
      await this.requestOrderDetail()
      this.initializeFormOptions()
      this.loaded = true
      await this.initializeLock()
    },

    async refreshOrderFromRequest (request) {
      if (this.refreshLock) return

      this.orderIdToLoad = request.first_order_id
      await this.fetchFormData()
    },

    lockReleased (request) {
      this.setOrderLock({ locked: true, ocrRequestLocked: false, lock: null })
    },

    lockClaimed (request) {
      if (request.request_id !== this.order.request_id) {
        return
      }

      this.setOrderLock({ locked: false, ocrRequestLocked: false, lock: null })
    },

    objectLocked (e) {
      const { objectLock: lock = undefined } = e

      if (lock.object_id !== this.order.request_id) {
        return
      }

      this.setOrderLock({ locked: true, ocrRequestLocked: true, lock })
    },

    objectUnlocked (e) {
      const { objectLock: lock = undefined } = e

      if (lock.object_id !== this.order.request_id) {
        return
      }

      this.setOrderLock({ locked: true, ocrRequestLocked: false, lock: null })
    },

    removeRootListeners () {
      this.$root.$off(events.requestsRefreshed, this.refreshOrderFromRequest)
      this.$root.$off(events.lockReleased, this.lockReleased)
      this.$root.$off(events.lockRefreshFailed, this.stopRefreshingLock)
      this.$root.$off(events.lockClaimed, this.lockClaimed)
      this.$root.$off(events.objectLocked, this.objectLocked)
      this.$root.$off(events.objectUnlocked, this.objectUnlocked)
    },

    initializeLockingListeners () {
      this.$root.$on(events.requestsRefreshed, this.refreshOrderFromRequest)
      this.$root.$on(events.lockReleased, this.lockReleased)
      this.$root.$on(events.lockRefreshFailed, this.stopRefreshingLock)
      this.$root.$on(events.lockClaimed, this.lockClaimed)
      this.$root.$on(events.objectLocked, this.objectLocked)
      this.$root.$on(events.objectUnlocked, this.objectUnlocked)

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

          this.setOrderLock({ locked: true, ocrRequestLocked: true, lock })
        })
        .listen(events.objectUnlocked, async (e) => {
          const { objectLock: lock = undefined } = e

          if (!lock || this.userOwnsLock(lock) || lock.object_id !== this.order.request_id) {
            return
          }

          this.setOrderLock({ locked: true, ocrRequestLocked: false, lock: null })
          if (!this.hasPermission('object-locks-create')) {
            return
          }

          await this.setConfirmationDialog({
            title: 'Request Unlocked',
            text: 'Do you want to claim the lock?',
            onConfirm: async () => {
              const [error] = await this.attemptToLockRequest({
                requestId: this.order.request_id,
                lockType: objectLocks.lockTypes.openOrder,
                updateList: false,
              })

              if (error === undefined) {
                this.setOrderLock({ locked: false, ocrRequestLocked: false, lock: null })
              }
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
        this.setOrderLock({ locked: false, ocrRequestLocked: false, lock: null })
      }
    },

    async handleClaimLock (order) {
      await this.setConfirmationDialog({
        title: 'Are you sure you want to take the request edit-lock?',
        noWrap: true,
        onConfirm: async () => {
          this.attemptToLockRequest({
            requestId: order.request_id,
            lockType: objectLocks.lockTypes.claimLock,
            updateList: false,
          })

          this.$root.$emit(events.lockClaimed, order.ocr_request)
        },
        onCancel: () => {}
      })
    },

    async handleReleaseLock (order) {
      await this.setConfirmationDialog({
        title: 'Are you sure you want to release the edit-lock?',
        noWrap: true,
        onConfirm: async () => {
          this.releaseLockRequest({ requestId: order.request_id })
          this.$root.$emit(events.lockReleased, order.ocr_request)
        },
        onCancel: () => {}
      })
    },

    initializeFormOptions () {
      const newFormOptions = cloneDeep(defaultFormOptions)
      for (const key in this.companyConfiguration) {
        if (key.startsWith('address_search_')) {
          const newKey = key.replace('address_search_', '')
          newFormOptions.address_search[newKey] = this.companyConfiguration[key]
        } else {
          newFormOptions.extra[key] = this.companyConfiguration[key]
        }
      }

      newFormOptions.field_maps = this.currentOrder.field_maps
      const { field_maps: fieldMaps } = this.currentOrder

      const getMapForCanonAndDirection = (d3CanonName, shipmentDirection) => {
        shipmentDirection = (shipmentDirection || '').trim() || 'empty'
        const fieldmapByShipdir = {}
        for (const key in fieldMaps) {
          if (fieldMaps[key].d3canon_name === d3CanonName) {
            const shipmentDirectionFilter = (fieldMaps[key].shipment_direction_filter || '').trim() || 'empty'
            fieldmapByShipdir[shipmentDirectionFilter] = fieldMaps[key]
          }
        }
        // get the best fieldmap match for the order's shipmentDirection
        const shipdirKeys = Object.keys(fieldmapByShipdir)
        const index = shipdirKeys.findIndex(item => item.includes(shipmentDirection))
        if (index === -1) {
          return fieldmapByShipdir.empty
        }
        return fieldmapByShipdir[shipdirKeys[index]]
      }

      for (const key in fieldMaps) {
        const d3CanonName = fieldMaps[key].d3canon_name
        const bestFieldMap = getMapForCanonAndDirection(d3CanonName, this.currentOrder.shipment_direction)
        if (bestFieldMap.screen_hide) {
          newFormOptions.hidden.push(d3CanonName)
        }
        if (bestFieldMap.screen_name) {
          newFormOptions.labels[d3CanonName] = bestFieldMap.screen_name
        }
      }

      this.formOptions = newFormOptions
    },

    initializeStateUpdatesListeners () {
      this.listenToRequestStatusUpdates(({ latestStatus } = {}) => {
        if (!latestStatus.order_id || latestStatus.order_id !== this.order.id) {
          return
        }

        this.updateOrderStatus({ latestStatus })
      }, `-order${this.order.id}`)
    },

    async requestOrderDetail () {
      const status = await this[types.getOrderDetail](this.orderIdToLoad)

      if (status === reqStatus.success) {
        this.setFormOrder(this.currentOrder)
        return
      }
      this.has404 = true
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
  height: 100vh;
  display: flex;
  overflow: hidden;

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
