<template>
  <div
    id="order-form"
    :class="`form ${isMobile && 'mobile'}`"
  >
    <div class="order__title">
      <h2>Order   #{{ order.id }}</h2>
      <OutlinedButtonGroup
        v-if="!editMode"
        :main-action="{
          title: 'Send to TMS',
          action: postSendToTms,
          path:'',
          disabled: sendToTmsDisabled
        }"
        :options="[
          { title: 'Edit Order' , action: toggleEdit, hasPermission: true },
          { title: 'Download Order', action: () => downloadPDF(order.id), hasPermission: true },
          { title: 'Delete Order', action: () => deleteOrder(order.id), hasPermission: hasPermission('orders-remove') }
        ]"
        :loading="loading"
      />
      <div v-else>
        <v-btn
          color="primary"
          :outlined="!editMode && !isMobile"
          :style="{ marginBottom: '10px' }"
          width="115px"
          text
          @click="toggleEdit"
        >
          Cancel
        </v-btn>
        <v-btn
          color="primary"
          :outlined="!editMode && !isMobile"
          :style="{ marginBottom: '10px' }"
          width="115px"
          @click="toggleEdit"
        >
          {{ editMode ? 'Save' : 'Edit Order' }}
        </v-btn>
      </div>
    </div>

    <div class="form__section">
      <h1
        :id="sections.shipment.id"
        class="section__title"
      >
        {{ sections.shipment.label }}
      </h1>

      <div class="section__rootfields">
        <!-- <FormFieldInput
          references="shipment_designation"
          label="Shipment designation"
          :value="order.shipment_designation"
          :edit-mode="editMode"
          @change="value => handleChange('shipment_designation', value)"
        /> -->
        <div class="divisionCodeSection">
          <FormFieldSelectDivisionCodes
            references="division_code"
            label="Division"
            :value="order.division_code"
            :edit-mode="editMode"
            :t-company-id="order.t_company_id"
            :t-tms-provider-id="order.t_tms_provider_id"
            :division-code="order.division_code"
            @change="value => handleChange('division_code', value)"
          />
        </div>
        <FormFieldInput
          references="shipment_direction"
          label="Shipment direction"
          :value="order.shipment_direction"
          :edit-mode="editMode"
          @change="value => handleChange('shipment_direction', value)"
        />
        
        <FormFieldSwitch
          references="expedite"
          label="Expedite"
          :value="order.expedite"
          :edit-mode="editMode"
          @change="value => handleChange('expedite', value)"
        />
        <FormFieldSwitch
          references="hazardous"
          label="Hazardous"
          :value="order.hazardous"
          :edit-mode="editMode"
          @change="value => handleChange('hazardous', value)"
        />
      </div>

      <div
        class="section__sub"
      >
        <div
          class="sub__title"
        >
          <h2 :id="sections.equipment.id">
            {{ sections.equipment.label }}
          </h2>
        </div>

        <FormFieldEquipmentType
          label="Equipment Type"
          :company-id="order.t_company_id"
          :tms-provider-id="order.t_tms_provider_id"
          :carrier="order.carrier"
          :equipment-size="order.equipment_size"
          :equipment-type="order.equipment_type"
          :unit-number="order.unit_number"
          :verified="order.equipment_type_verified"
          @change="(e) => handleChange('t_equipment_type_id', e)"
        />
        <FormFieldInput
          references="unit_number"
          label="Unit number"
          :value="order.unit_number"
          :edit-mode="editMode"
          @change="value => handleChange('unit_number', value)"
        />
        <FormFieldInput
          references="seal_number"
          label="Seal number"
          :value="order.seal_number"
          :edit-mode="editMode"
          @change="value => handleChange('seal_number', value)"
        />
        <!-- <FormFieldInput
          references="equipment_size"
          label="Size"
          :value="order.equipment_size"
          :edit-mode="editMode"
          @change="value => handleChange('equipment_size', value)"
        /> -->
        <!-- <FormFieldSwitch
          references="has_chassis"
          label="Has chassis"
          :value="order.has_chassis"
          :edit-mode="editMode"
          @change="value => handleChange('has_chassis', value)"
        /> -->
        <!-- <FormFieldInput
          references="carrier"
          label="SSL"
          :value="order.carrier"
          :edit-mode="editMode"
          @change="value => handleChange('carrier', value)"
        /> -->
      </div>
     
      <div
        class="section__sub"
      >
        <div
          class="sub__title"
        >
          <h2 :id="sections.origin.id">
            {{ sections.origin.label }}
          </h2>
        </div>
        <FormFieldInput
          references="reference_number"
          label="Reference number"
          :value="order.reference_number"
          :edit-mode="editMode"
          @change="value => handleChange('reference_number', value)"
        />
        <!-- <FormFieldInput
          references="pickup_number"
          label="Pickup number"
          :value="order.pickup_number"
          :edit-mode="editMode"
          @change="value => handleChange('pickup_number', value)"
        /> -->
        <FormFieldInput
          references="customer_number"
          label="Customer Number"
          :value="order.customer_number === null ? '---' : order.customer_number"
          :edit-mode="editMode"
          @change="value => handleChange('customer_number', value)"
        />
        <FormFieldInput
          references="load_number"
          label="Load number"
          :value="order.load_number"
          :edit-mode="editMode"
          @change="value => handleChange('load_number', value)"
        />
        <FormFieldInput
          references="purchase_order_number"
          label="Purchase Order Number"
          :value="order.purchase_order_number"
          :edit-mode="editMode"
          @change="value => handleChange('purchase_order_number', value)"
        />
        <FormFieldInput
          references="release_number"
          label="Release Number"
          :value="order.release_number"
          :edit-mode="editMode"
          @change="value => handleChange('release_number', value)"
        />
        <FormFieldInput
          references="vessel"
          label="Vessel"
          :value="order.vessel"
          :edit-mode="editMode"
          @change="value => handleChange('vessel', value)"
        />
        <FormFieldInput
          references="voyage"
          label="Voyage"
          :value="order.voyage"
          :edit-mode="editMode"
          @change="value => handleChange('voyage', value)"
        />
        <FormFieldInput
          references="booking_number"
          label="Booking Number"
          :value="order.booking_number === null ? '---' : order.booking_number"
          :edit-mode="editMode"
          @change="value => handleChange('booking_number', value)"
        />
        <FormFieldInput
          references="master_bol_mawb"
          label="Master BOL MAWB"
          :value="order.master_bol_mawb"
          :edit-mode="editMode"
          @change="value => handleChange('master_bol_mawb', value)"
        />
        <FormFieldInput
          references="house_bol_hawb"
          label="House BOL MAWB"
          :value="order.house_bol_hawb"
          :edit-mode="editMode"
          @change="value => handleChange('house_bol_hawb', value)"
        />
        <!-- <FormFieldInput
          references="actual_origin"
          label="Actual Origin"
          :value="order.actual_origin"
          :edit-mode="editMode"
          @change="value => handleChange('actual_origin', value)"
        />
        <FormFieldInput
          references="actual_destination"
          label="Actual Destination"
          :value="order.actual_destination"
          :edit-mode="editMode"
          @change="value => handleChange('actual_destination', value)"
        /> -->
      </div>
      <div
        class="section__sub"
      >
        <div
          class="sub__title"
        >
          <h2 :id="sections.bill_to.id">
            {{ sections.bill_to.label }}
          </h2>
        </div>
        <FormFieldAddressSwitchVerify
          :recognized-text="order.bill_to_address_raw_text"
          :verified="order.bill_to_address_verified"
          :matched-address="order.bill_to_address"
          billable
          @change="(e) => handleChange('bill_to_address', e)"
        />
        <FormFieldTextArea
          references="bill_comment"
          label="Billing comments"
          :value="order.bill_comment"
          :edit-mode="editMode"
          @change="value => handleChange('bill_comment', value)"
        />
        <div
          class="sub__title"
        >
          <h2 :id="sections.charges.id">
            {{ sections.charges.label }}
          </h2>
        </div>
        <FormFieldTextArea
          references="line_haul"
          label="Line Haul"
          :value="order.line_haul"
          :edit-mode="editMode"
          @change="value => handleChange('line_haul', value)"
        />
        <FormFieldTextArea
          references="fuel_surcharge"
          label="FSC"
          :value="order.fuel_surcharge"
          :edit-mode="editMode"
          @change="value => handleChange('fuel_surcharge', value)"
        />
      </div>
    </div>
    <!-- <div class="form__section">
      <h1
        :id="sections.pickup.id"
        class="section__title"
      >
        {{ sections.pickup.label }}
      </h1>

      <div class="section__rootfields">
        <FormFieldDate
          references="pickup_by_date"
          label="Pickup date"
          :value="order.pickup_by_date"
          :edit-mode="editMode"
          @change="value => handleChange('pickup_by_date', value)"
        />
        <FormFieldTime
          references="pickup_by_time"
          label="Pickup time"
          :value="order.pickup_by_time"
          :edit-mode="editMode"
          @change="value => handleChange('pickup_by_time', value)"
        />
      </div>
    </div> -->

    <div class="form__section">
      <h1
        :id="sections.itinerary.id"
        class="section__title"
      >
        {{ sections.itinerary.label }}
      </h1>

      <div class="section__rootfields">
        <Fragment
          v-for="(orderAddressEvent, index) in order.order_address_events"
          :key="orderAddressEvent.id"
        >
          <FormFieldAddressSwitchVerify
            :label="`${orderAddressEvent.event_number}: ${orderAddressEvent.unparsed_event_type}`"
            :recognized-text="orderAddressEvent.t_address_raw_text"
            :verified="orderAddressEvent.t_address_verified || false"
            :matched-address="orderAddressEvent.address"
            @change="(e) => handleChange(`order_address_events.${index}`, e)"
          />
        </Fragment>
      </div>
    </div>
    <div class="form__section">
      <h1 class="section__title">
        {{ sections.notes.label }}
      </h1>

      <div class="section__rootfields">
        <FormFieldTextArea
          references="ship_comment"
          label="Shipment comments"
          :value="order.ship_comment"
          :edit-mode="editMode"
          @change="value => handleChange('ship_comment', value)"
        />
      </div>
    </div>
    <div class="form__section">
      <h1
        :id="sections.inventory.id"
        class="section__title"
      >
        {{ sections.inventory.label }}
      </h1>

      <div
        v-for="(item, index) in availableLineItems"
        :key="index"
        class="section__sub"
      >
        <div
          class="sub__title"
        >
          <h2>Item {{ index + 1 }}</h2>
        </div>
        <FormFieldTextArea
          :references="`order_line_items.${item.real_index}`"
          label="Description"
          :value="item.contents"
          :edit-mode="editMode"
          @change="value => handleChange(`order_line_items.${item.real_index}`, value)"
        />
      </div>
    </div>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import permissions from '@/mixins/permissions'
import { mapState, mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'
import get from 'lodash/get'

import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import utils, { type } from '@/store/modules/utils'
import orders, { types } from '@/store/modules/orders'

import { Fragment } from 'vue-fragment'
// import FormFieldDate from '@/components/FormFields/FormFieldDate'
// import FormFieldTime from '@/components/FormFields/FormFieldTime'
import FormFieldInput from '@/components/FormFields/FormFieldInput'
import FormFieldSwitch from '@/components/FormFields/FormFieldSwitch'
import FormFieldTextArea from '@/components/FormFields/FormFieldTextArea'
import FormFieldAddressSwitchVerify from '@/components/FormFields/FormFieldAddressSwitchVerify'
import FormFieldEquipmentType from '@/components/FormFields/FormFieldEquipmentType'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'
import FormFieldSelectDivisionCodes from '@/components/FormFields/FormFieldSelectDivisionCodes'
import { delDeleteOrder } from '@/store/api_calls/orders'

export default {
  name: 'OrderDetailsForm',
  components: {
    Fragment,
    // FormFieldDate,
    // FormFieldTime,
    FormFieldInput,
    FormFieldSwitch,
    FormFieldTextArea,
    FormFieldAddressSwitchVerify,
    FormFieldEquipmentType,
    OutlinedButtonGroup,
    FormFieldSelectDivisionCodes
  },
  mixins: [isMobile, permissions],
  data () {
    return {
      loading: false,
      divisionCodes: [],
      sentToTms: false
    }
  },

  computed: {
    ...mapState(orderForm.moduleName, {
      order: state => state.order,
      editMode: state => state.editMode,
      highlights: state => state.highlights,
      sections: state => state.sections
    }),
    availableLineItems () {
      return this.order.order_line_items
        .map((item, index) => ({ ...item, real_index: index }))
        .filter(item => !item.deleted_at)
    },
    saveBtnStyles () {
      if (this.isMobile) return 'secondary'
      if (this.editMode) return 'success'
      return 'primary'
    },
    sendToTmsDisabled () {
      if (this.sentToTms) {
        return true
      }

      if (this.isSuperadmin()) {
        return false
      }

      return (this.order.tms_shipment_id !== null && this.order.tms_shipment_id !== undefined) ||
        (get(this.order, 'ocr_request.latest_ocr_request_status.status') === 'sending-to-wint')
    }
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      toggleEdit: orderFormTypes.toggleEdit
    }),
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setConfirmationDialog]),
    ...mapActions(orders.moduleName, [types.postSendToTms, types.getDownloadPDFURL]),
    ...mapActions(orders.moduleName, [types.getDownloadPDFURL]),
    ...mapActions(orderForm.moduleName, {
      updateOrder: orderFormTypes.updateOrder,
      startHover: orderFormTypes.startHover,
      stopHover: orderFormTypes.stopHover
    }),

    async handleChange (path, value) {
      await this.updateOrder({ path, value })
    },
    async postSendToTms () {
      const status = await this[types.postSendToTms]({ order_id: this.order.id, status: 'sending-to-wint' })
      if (status === reqStatus.success) {
        this.sentToTms = true
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'Processing'
        })
      } else {
        this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: status.request.status === 422
            ? 'Some addresses are not verified'
            : status.request.status === 403
              ? 'You are not authorized' : 'An error has occurred, please contact to technical support'
        })
      }
    },
    async downloadPDF (orderId) {
      this.loading = true
      const request = await this[types.getDownloadPDFURL](orderId)

      if (request.status === reqStatus.success) {
        const link = document.createElement('a')
        link.href = request.data.data
        link.download = `order-${orderId}.pdf`
        link.click()
        link.remove()
      } else {
        console.log('error')
      }
      this.loading = false
    },
    async deleteOrder (orderId) {
      this.loading = true

      await this[type.setConfirmationDialog]({
        title: 'Are you sure you want to delete this order?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await delDeleteOrder(this.order.id)

          if (!error) {
            await this[type.setSnackbar]({
              show: true,
              showSpinner: false,
              message: 'Order deleted'
            })
            this.$router.push('/dashboard')
          } else {
            await this[type.setSnackbar]({
              show: true,
              showSpinner: false,
              message: 'Error trying to delete the order'
            })
          }
          this.loading = false
        }
      })
    }

  }
}
</script>

<style lang="scss" scoped>
.order__title {
  position: relative;
  margin: rem(14) auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #dadddd;
  padding-bottom: 20px;

  h2{
    font-size: rem(20);
    color: var(--v-primary-base);
    font-weight: 500;
    line-height: rem(23);
  }}
.form {
  width: 100%;
  height: 100vh;
  overflow-y: auto;
  padding: rem(36) rem(65);
  padding-top: rem(10);
  scroll-behavior: smooth;

  &.mobile {
    height: 50vh;
    padding-bottom: rem(70);
    padding: rem(16);
  }
}

.section__title {
  text-transform: uppercase;
  font-size: rem(17);
  line-height: rem(30);
  background: map-get($colors, grey-6);
  padding: 0 rem(6.5);
  margin-bottom: rem(20);
  color: map-get($colors, grey-7);
}

.section__rootfields {
  margin-bottom: rem(36);
}

.section__field {
  display: flex;
  justify-content: space-between;
  margin-bottom: rem(11);
}

.field__name {
  font-size: rem(14) !important;
  font-weight: 700;
  text-transform: capitalize;
}

.field__value {
  font-size: rem(14.4) !important;
  text-transform: capitalize;
}

.section__sub {
  margin-bottom: rem(36);
}

.sub__title {
  display: flex;
  align-items: center;
  justify-content: space-between;

  h2 {
    width: 100%;
    font-size: rem(16);
    line-height: rem(36);
    color: map-get($colors, grey-4);
    border-bottom: rem(1) solid map-get($colors, grey-3);
    margin-bottom: rem(14);
    text-transform: capitalize;
  }

  button {
    margin-left: auto;
  }
}
</style>
