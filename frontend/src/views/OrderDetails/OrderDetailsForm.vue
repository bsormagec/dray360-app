<template>
  <div
    id="order-form"
    ref="orderForm"
    :class="`form ${isMobile && 'mobile'}`"
  >
    <div class="order__title-group">
      <v-btn
        v-if="backButton"
        color="primary"
        outlined
        small
        class="px-0"
        title="Go back to Orders List"
        @click="goToOrdersList()"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
      </v-btn>
      <div>
        <div class="order__title mr-4">
          Order #{{ order.id }}
        </div>
        <div
          class="secondary--text caption"
        >
          <RequestStatus
            :status="order.ocr_request.latest_ocr_request_status"
          />
        </div>
        <div
          v-show="order.tms_shipment_id"
          class="order__tms"
        >
          <strong>TMS ID: </strong> {{ order.tms_shipment_id }}
          <v-tooltip
            top
          >
            <template v-slot:activator="{ on }">
              <v-icon
                v-clipboard:copy="order.tms_shipment_id"
                small
                color="secondary"
                v-on="on"
                @click.stop="() =>{}"
              >
                mdi-content-paste
              </v-icon>
            </template>
            <span>Copy TMS ID</span>
          </v-tooltip>
        </div>
      </div>
      <OutlinedButtonGroup
        v-if="!editMode"
        :main-action="{
          title: 'Send to TMS',
          action: postSendToTms,
          disabled: sendToTmsDisabled
        }"
        :options="[
          { title: 'Edit Order' , action: toggleEdit, hasPermission: true },
          { title: 'Download Source File', action: () => downloadSourceFile(order.id), hasPermission: true },
          { title: 'Delete Order', action: () => deleteOrder(order.id), hasPermission: hasPermission('orders-remove') }
        ]"
        :loading="loading"
      />
      <div
        v-else
        class="order__title-btn-group"
      >
        <v-btn
          color="primary"
          :outlined="!editMode && !isMobile"
          small
          text
          @click="cancelEdit"
        >
          Cancel
        </v-btn>
        <v-btn
          color="primary"
          :outlined="!editMode && !isMobile"
          small
          @click="toggleEdit"
        >
          {{ editMode ? 'Save' : 'Edit Order' }}
        </v-btn>
      </div>
    </div>

    <!-- <div class="order__changelog">
      <div class="order__chip-container">
        <v-chip
          class="mr-1 mb-3"
          outlined
          small
        >
          Current
        </v-chip>
        <v-chip
          class="mr-1 mb-3"
          outlined
          small
        >
          <v-avatar
            left
          >
            <v-icon>mdi-toggle-switch-outline</v-icon>
          </v-avatar>
          Changes
        </v-chip>
        <v-chip
          class="mr-1 mb-3"
          outlined
          small
        >
          <v-avatar
            left
            small>
            <v-icon>mdi-alert-outline</v-icon>
          </v-avatar>
          13 Warning
        </v-chip>
        <v-chip
          class="mr-1 mb-3"
          outlined
          small
        >
          <v-avatar
            left
            small
          >
            <v-icon>mdi-alert-circle-outline</v-icon>
          </v-avatar>
          1 Error
        </v-chip>
      </div>
      <p>Last Updated <span>{{ lastChandedAt }}</span> by John Doe</p>
      <a href="#">History</a>
    </div> -->

    <div class="form__section">
      <div
        :id="sections.shipment.id"
        class="form__section-title"
      >
        <h3>{{ sections.shipment.label }}</h3>
      </div>

      <div class="section__rootfields">
        <!-- <FormFieldInput
          references="shipment_designation"
          label="Shipment designation"
          :value="order.shipment_designation"
          :edit-mode="editMode"
          @change="value => handleChange('shipment_designation', value)"
        /> -->
        <FormFieldSelect
          v-if="shouldSelectProfitToolsTemplateId"
          references="tms_template_id"
          label="TMS Template Name"
          :value="order.tms_template_id"
          :items="profitToolsTemplatesSelectItems"
          item-text="tms_template_name"
          item-value="tms_template_id"
          :display-value="displayName"
          :edit-mode="editMode"
          @change="value => handleChange('tms_template_id', value)"
        />
        <FormFieldManaged
          v-if="order.tms_template_id"
          references="division_code"
          label="Division"
        />
        <FormFieldSelectDivisionCodes
          v-else
          references="division_code"
          label="Division"
          :value="order.division_code"
          :edit-mode="editMode"
          :t-company-id="order.t_company_id"
          :t-tms-provider-id="order.t_tms_provider_id"
          :division-code="order.division_code"
          @change="value => handleChange('division_code', value)"
        />
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
        class="form__sub-section"
      >
        <div
          class="form__section-title"
        >
          <h3 :id="sections.equipment.id">
            {{ sections.equipment.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldEquipmentType
            label="Equipment Type"
            :company-id="order.t_company_id"
            :tms-provider-id="order.t_tms_provider_id"
            :carrier="order.carrier"
            :equipment-size="order.equipment_size"
            :equipment-type="order.equipment_type"
            :recognized-text="order.equipment_type_raw_text"
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
        </div>
      </div>

      <div
        class="form__sub-section"
      >
        <div
          class="form__section-title"
        >
          <h3 :id="sections.origin.id">
            {{ sections.origin.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldInput
            references="reference_number"
            label="Reference number"
            :value="order.reference_number"
            :edit-mode="editMode"
            @change="value => handleChange('reference_number', value)"
          />
          <FormFieldInput
            references="customer_number"
            label="Customer number"
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
            label="Purchase Order number"
            :value="order.purchase_order_number"
            :edit-mode="editMode"
            @change="value => handleChange('purchase_order_number', value)"
          />
          <FormFieldInput
            references="release_number"
            label="Release number"
            :value="order.release_number"
            :edit-mode="editMode"
            @change="value => handleChange('release_number', value)"
          />
          <FormFieldInput
            references="pickup_number"
            label="Pickup number"
            :value="order.pickup_number"
            :edit-mode="editMode"
            @change="value => handleChange('pickup_number', value)"
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
          <FormFieldDate
            references="cutoff_date"
            label="Cutoff Date"
            :value="order.cutoff_date"
            :edit-mode="editMode"
            @change="value => handleChange('cutoff_date', value)"
          />
          <FormFieldTime
            references="cutoff_time"
            label="Cutoff Time"
            :value="order.cutoff_time"
            :edit-mode="editMode"
            @change="value => handleChange('cutoff_time', value)"
          />
          <FormFieldInput
            references="booking_number"
            label="Booking number"
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
        </div>
      </div>
      <div
        v-if="!order.tms_template_id"
        class="form__sub-section"
      >
        <div
          class="form__section-title"
        >
          <h3 :id="sections.bill_to.id">
            {{ sections.bill_to.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldAddressSwitchVerify
            :recognized-text="order.bill_to_address_raw_text || 'Addres not recognized'"
            :verified="order.bill_to_address_verified"
            :matched-address="order.bill_to_address"
            references="bill_to_address"
            :edit-mode="false"
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
        </div>
        <div
          class="form__section-title"
        >
          <h3 :id="sections.charges.id">
            {{ sections.charges.label }}
          </h3>
        </div>
        <div class="section__rootfields">
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
      <div
        v-else
      >
        <div
          :id="sections.bill_to.id"
          ref="itineraryLabel"
          class="form__section-title form__section-title--managed d-flex align-center justify-center mb-2"
        >
          <h3>
            {{ sections.bill_to.label }} managed by template
          </h3>
        </div>
      </div>

      <div
        v-if="!order.tms_template_id"
        class="form__section"
      >
        <div
          :id="sections.itinerary.id"
          ref="itineraryLabel"
          class="form__section-title d-flex align-center"
        >
          <h3>
            {{ sections.itinerary.label }}
          </h3>
          <v-btn
            v-if="!editMode"
            class="ml-auto"
            small
            outlined
            color="white"
            @click="handleItinerayEdit"
          >
            Edit itinerary
          </v-btn>
          <v-btn
            v-else
            class="ml-auto"
            small
            outlined
            color="white"
            @click="handleNewEvent"
          >
            Add Event
          </v-btn>
        </div>

        <div class="section__rootfields">
          <div
            v-for="(orderAddressEvent, index) in order.order_address_events"
            :key="`${index}${orderAddressEvent.id}`"
          >
            <FormFieldItineraryEdit
              :order-address-event="orderAddressEvent"
              :current-index="index+1"
              :references="`order_address_events.${index}`"
              :edit-mode="editMode"
              :is-first="index === 0"
              :is-last="index == order.order_address_events.length - 1"
              @change="(e) => handleChange(`order_address_events.${index}`, e)"
              @delete="(e) => handleDelete(index)"
              @sort="(e) => moveObjectPositionInArray(order.order_address_events[index].id, e)"
            />
          </div>
        </div>
      </div>
      <div
        v-else
      >
        <div
          :id="sections.itinerary.id"
          ref="itineraryLabel"
          class="form__section-title form__section-title--managed d-flex align-center justify-center mb-2"
        >
          <h3>
            {{ sections.itinerary.label }} managed by template
          </h3>
        </div>
      </div>
      <div class="form__section">
        <div class="form__section-title">
          <h3>
            {{ sections.notes.label }}
          </h3>
        </div>

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
      <div
        v-if="!order.tms_template_id"
        class="form__section"
      >
        <div
          :id="sections.inventory.id"
          class="form__section-title"
        >
          <h3>
            {{ sections.inventory.label }}
          </h3>
        </div>

        <div
          v-for="(item, index) in availableLineItems"
          :key="index"
          class="form__sub-section"
        >
          <div
            class="form__section-title"
          >
            <h3>Item {{ index + 1 }}</h3>
          </div>
          <div class="section__rootfields">
            <FormFieldTextArea
              :references="`order_line_items.${item.real_index}.contents`"
              label="Contents"
              :value="item.contents"
              :edit-mode="editMode"
              @change="value => handleChange(`order_line_items.${item.real_index}.contents`, value)"
            />
            <FormFieldInput
              :references="`order_line_items.${item.real_index}.quantity`"
              label="Quantity"
              type="number"
              :value="item.quantity"
              :edit-mode="editMode"
              @change="value => handleChange(`order_line_items.${item.real_index}.quantity`, value)"
            />
            <FormFieldInput
              :references="`order_line_items.${item.real_index}.weight`"
              label="Weight"
              type="number"
              :value="item.weight"
              :edit-mode="editMode"
              @change="value => handleChange(`order_line_items.${item.real_index}.weight`, value)"
            />
          </div>
        </div>
      </div>
      <div
        v-else
      >
        <div
          :id="sections.inventory.id"
          class="form__section-title form__section-title--managed d-flex align-center justify-center"
        >
          <h3>
            {{ sections.inventory.label }} managed by template
          </h3>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import permissions from '@/mixins/permissions'
import { mapState, mapActions } from 'vuex'
import get from 'lodash/get'

import { getSourceFileDownloadURL, postSendToTms, delDeleteOrder } from '@/store/api_calls/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import utils, { type } from '@/store/modules/utils'

import FormFieldDate from '@/components/FormFields/FormFieldDate'
import FormFieldTime from '@/components/FormFields/FormFieldTime'
import FormFieldInput from '@/components/FormFields/FormFieldInput'
import FormFieldSwitch from '@/components/FormFields/FormFieldSwitch'
import FormFieldTextArea from '@/components/FormFields/FormFieldTextArea'
import FormFieldAddressSwitchVerify from '@/components/FormFields/FormFieldAddressSwitchVerify'
import FormFieldItineraryEdit from '@/components/FormFields/FormFieldItineraryEdit'
import FormFieldEquipmentType from '@/components/FormFields/FormFieldEquipmentType'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'
import FormFieldSelectDivisionCodes from '@/components/FormFields/FormFieldSelectDivisionCodes'
import FormFieldSelect from '@/components/FormFields/FormFieldSelect'
import FormFieldManaged from '@/components/FormFields/FormFieldManaged'
import RequestStatus from '@/components/RequestStatus'

export default {
  name: 'OrderDetailsForm',
  components: {
    FormFieldDate,
    FormFieldTime,
    FormFieldInput,
    FormFieldSwitch,
    FormFieldTextArea,
    FormFieldAddressSwitchVerify,
    FormFieldItineraryEdit,
    FormFieldEquipmentType,
    OutlinedButtonGroup,
    FormFieldSelect,
    FormFieldSelectDivisionCodes,
    FormFieldManaged,
    RequestStatus
  },
  mixins: [isMobile, permissions],
  props: {
    backButton: {
      type: Boolean,
      required: false,
      default: true
    },
    redirectBack: {
      type: Boolean,
      required: false,
      default: false
    }
  },
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

    profitToolsTemplatesSelectItems () {
      return get(this.order, 'company.configuration.profit_tools_template_list', [])
    },
    shouldSelectProfitToolsTemplateId () {
      return get(this.order, 'company.configuration.profit_tools_enable_templates', false)
    },

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

      if (this.hasPermission('tms-resubmit')) {
        return false
      }

      return (this.order.tms_shipment_id !== null && this.order.tms_shipment_id !== undefined) ||
        (get(this.order, 'ocr_request.latest_ocr_request_status.status') === 'sending-to-wint')
    },
    lastChandedAt () {
      Number.prototype.padLeft = function (base, chr) {
        const len = (String(base || 10).length - String(this).length) + 1
        return len > 0 ? new Array(len).join(chr || '0') + this : this
      }

      const lastUpdatedAt = new Date(this.order.updated_at)

      const date = [
        (lastUpdatedAt.getMonth() + 1).padLeft(),
        lastUpdatedAt.getDate().padLeft(),
        lastUpdatedAt.getFullYear()
      ].join('-')

      const time = [
        lastUpdatedAt.getHours().padLeft(),
        lastUpdatedAt.getMinutes().padLeft()
      ].join(':')

      return `${date} ${time}`
    }
  },
  beforeMount () {
    if (!this.isMobile) {
      return this[type.setSidebar]({ show: true })
    }
  },
  mounted () {
    if (this.editMode) this.toggleEdit()
  },
  methods: {
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setConfirmationDialog, type.setSidebar]),
    ...mapActions(orderForm.moduleName, {
      updateOrder: orderFormTypes.updateOrder,
      startHover: orderFormTypes.startHover,
      stopHover: orderFormTypes.stopHover,
      toggleEdit: orderFormTypes.toggleEdit,
      cancelEdit: orderFormTypes.cancelEdit,
      addHighlight: orderFormTypes.addHighlight
    }),

    async handleChange (path, value) {
      await this.updateOrder({ path, value })
    },

    async postSendToTms () {
      const [error] = await postSendToTms(this.order.id)
      let message = ''

      if (error !== undefined) {
        switch (error.response.status) {
          case 422:
            message = 'Some addresses are not verified'
            break
          case 403:
            message = 'You are not authorized'
            break
          default:
            message = 'An error has occurred, please contact to technical support'
            break
        }
      } else {
        message = 'Sending the order to the TMS is in progress'
        this.sentToTms = true
      }

      this[type.setSnackbar]({ show: true, message })
    },

    async downloadSourceFile (orderId) {
      this.loading = true
      const [error, data] = await getSourceFileDownloadURL(orderId)

      if (error === undefined) {
        const link = document.createElement('a')
        link.href = data.data
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
          let message = ''

          if (!error) {
            message = 'Order deleted'
            this.loading = false
            if (!this.backButton) {
              this.$emit('order-deleted')
            } else {
              this.goToOrdersList()
            }
          } else {
            message = 'Error trying to delete the order'
          }
          await this[type.setSnackbar]({ show: true, message })
        },
        onCancel: () => { this.loading = false }
      })
    },

    goToOrdersList () {
      this.redirectBack ? this.$router.back() : this.$router.push('/dashboard')
    },

    handleItinerayEdit () {
      this.toggleEdit()
      setTimeout(() => {
        this.$refs.orderForm.scrollTop = this.$refs.itineraryLabel.offsetTop
      }, 50)
    },

    handleNewEvent () {
      const newEvent = {
        id: null,
        t_address_id: null,
        t_order_id: this.order.id,
        t_address_verified: false,
        address: null,
        t_address_raw_text: '',
        deleted_at: null
      }
      this.addHighlight(`order_address_events.${this.order.order_address_events.length}`)
      this.handleChange('order_address_events',
        [
          ...this.order.order_address_events,
          newEvent
        ]
      )
    },

    moveObjectPositionInArray (id, direction) {
      const arr = this.order.order_address_events
      if (direction === 'up') {
        const index = arr.findIndex(e => e.id == id)
        if (index > 0) {
          const el = arr[index]
          arr[index] = arr[index - 1]
          arr[index - 1] = el
        }
      } else if (direction === 'down') {
        const index = arr.findIndex(e => e.id == id)
        if (index !== -1 && index < arr.length - 1) {
          const el = arr[index]
          arr[index] = arr[index + 1]
          arr[index + 1] = el
        }
      }
      this.handleChange('order_address_events', arr)
    },

    handleDelete (index) {
      const arr = this.order.order_address_events
      arr[index].deleted_at = true
      this.handleChange('order_address_events', arr)
    },
    displayName (value) {
      const result = this.profitToolsTemplatesSelectItems.filter(el => el.tms_template_id === value)
      return result.length > 0 ? result[0].tms_template_name : value
    }
  }
}
</script>

<style lang="scss" scoped>
.form {
  width: 100%;
  height: 100vh;
  overflow-y: auto;
  padding: 0 rem(15) rem(15) rem(15);
  scroll-behavior: smooth;

  &.mobile {
    height: 50vh;
    padding-bottom: rem(70);
    padding: 0 rem(16) rem(16) rem(16);
  }
}

.order__title-group {
  position: sticky;
  top: 0;
  display: flex;
  align-items: flex-start;
  padding: rem(15);
  margin: 0 rem(-15) rem(15) rem(-15);
  background-color: white;
  z-index: 1;

  &::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: rem(-15);
    display: block;
    height: rem(15);
    background: linear-gradient(180deg, rgba(0, 60, 113, 0.1) 0%, rgba(0, 60, 113, 0.05) 31.77%, rgba(0, 60, 113, 0) 100%);
  }

  .order__title {
    font-size: rem(20);
    font-weight: 500;
    line-height: (23.4 / 20);
    letter-spacing: rem(.15);
    color: map-get($colors, slate-gray);
  }

  .order__tms {
    display: flex;
    align-items: center;
    font-size: rem(12);
    line-height: (18 /12);
    letter-spacing: rem(.25);
    color: map-get($colors, slate-gray);

    strong {
      font-weight: 700;
      margin-right: rem(4);
    }

    i {
      font-size: rem(14);
      color: map-get($colors, slate-gray);
      margin-left: rem(4);
    }
  }

  .v-btn {
    min-width: unset;
    margin-right: rem(8);
  }

  &::v-deep .split-btn {
    margin-left: auto;
  }
}

.order__title-btn-group {
  margin-left: auto;
}

.form__section-title {
  padding: rem(4) rem(10) rem(3);
  background-color: map-get($colors, slate-gray);
  h3 {
    text-transform: uppercase;
    font-size: rem(13);
    font-weight: 700;
    line-height: (24 / 13);
    letter-spacing: rem(.75);
    color: map-get($colors, white);
  }
  &.form__section-title--managed h3 {
    font-weight: 500;
    font-size: rem(10);
    line-height: (15 / 10);
    letter-spacing: rem(1.5);
  }
}

.form__sub-section {
  .form__section-title {
    margin-bottom: rem(5);
    background-color: transparent;
    border-bottom: 1px solid rgba(map-get($colors, slate-gray), 50%);

    h3 {
      color: map-get($colors, mainblue);
    }
  }
}

.section__rootfields {
  margin-bottom: rem(18);

  .form-field:nth-child(even),
  & > div:nth-child(even) .form-field-presentation {
    background-color: #F5F6F7;
  }
}

.section__rootfields::v-deep {
  .equipment__section,
  .selected__equipment,
  .field__value,
  .block__right,
  .address-book-modal__body__status {
    font-size: rem(14);
    font-weight: 400;
    line-height: (20 / 14);
    letter-spacing: rem(.25);
  }

  .field__value {
    max-width: 50%;
  }

  .equipment__section {
    font-weight: 700;
  }
}

.order__changelog {
  position: relative;
  padding: 0 rem(22) rem(12);
  overflow: hidden;

  &::after,
  &::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    background-color: map-get($colors, slate-gray);
  }

  &::after {
    height: rem(16);
    width: rem(16);
    border-radius: 50%;
  }

  &::before {
    height: 100%;
    width: rem(2);
    left: rem(7);
  }
}

.order__changelog p {
  margin-bottom: rem(6);
  font-size: rem(14);
  line-height: (18 / 14);
  letter-spacing: rem(.25);

  span {
    font-weight: 700;
    text-decoration: underline;
    color: map-get($colors, mainblue );
  }
}

.order__changelog a {
  font-size: rem(10);
  line-height: (16 / 10);
  letter-spacing: rem(1.5);
  font-weight: 500;
  text-transform: uppercase;
  color: map-get($colors, slate-gray);
}

.order__chip-container {
  .v-chip {
    border-style: none;
    .v-avatar {
      margin-right: rem(3);
    }
  }
}

.order__tms .v-icon {
  margin-left: rem(4);
  vertical-align: baseline;
}
</style>
