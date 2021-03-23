<template>
  <div
    id="order-form"
    ref="orderForm"
    :class="`form ${isMobile && 'mobile'}`"
  >
    <div
      ref="orderHeading"
      class="order__title-group"
    >
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
      <v-btn
        v-if="virtualBackButton"
        color="primary"
        outlined
        small
        class="px-0"
        title="Go back to Orders List"
        @click="$emit('go-back')"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
      </v-btn>
      <div>
        <div class="order__title mr-4 d-flex justify-space-between align-center">
          <v-tooltip
            v-if="isLocked"
            bottom
          >
            <template v-slot:activator="{ on, attrs }">
              <v-icon
                small
                color="slate-gray"
                v-bind="attrs"
                v-on="on"
              >
                mdi-lock
              </v-icon>
            </template>
            <span>Locked by {{ order.lock.user.name }}</span>
          </v-tooltip>
          Order #{{ order.id }}
          <v-btn
            outlined
            dense
            x-small
            icon
            color="primary"
            class="ml-2"
            :loading="loading"
            @click="$emit('refresh')"
          >
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </div>
        <div class="secondary--text caption">
          <RequestStatus :status="order.ocr_request.latest_ocr_request_status" />
        </div>
        <div
          v-show="order.tms_shipment_id"
          class="order__tms"
        >
          <strong>TMS ID: </strong> {{ order.tms_shipment_id }}
          <v-tooltip top>
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
        :main-action="splitButtonMainAction"
        :options="[
          { title: 'Edit Order' , action: toggleEdit, hasPermission: !isLocked },
          { title: 'Download Source File', action: () => downloadSourceFile(order.request_id), hasPermission: true },
          { title: 'Replicate Order', action: () => replicateOrder(order.id), hidden: !hasPermission('admin-review-edit') || isLocked },
          { title: 'Delete Order', action: () => deleteOrder(order.id), hasPermission: hasPermission('orders-remove') && !isLocked },
          { title: 'Add TMS ID', action: () => addTMSId(order.id), hasPermission: hasPermission('ocr-requests-edit') && isInProcessedState && !isLocked},
          { title: 'View audit info', action: () => openAuditDialog = true, hidden: !hasPermission('audit-logs-view')}
        ]"
        :loading="loading"
      />
      <div
        v-else
        class="order__title-btn-group"
      >
        <v-btn
          color="primary"
          small
          text
          @click="cancelEdit"
        >
          Cancel
        </v-btn>
        <v-btn
          color="primary"
          small
          @click="toggleEdit"
        >
          Save
        </v-btn>
        <v-btn
          v-if="isMultiOrderRequest && hasPermission('all-orders-edit')"
          color="primary"
          small
          outlined
          @click="() => toggleEdit({saveAll: true})"
        >
          Save For All
        </v-btn>
      </div>
    </div>
    <OrderAuditDialog
      v-if="hasPermission('audit-logs-view')"
      :open="openAuditDialog"
      :order="order"
      @close="openAuditDialog = false"
    />

    <div class="order__changelog">
      <!-- <div class="order__chip-container">
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
            small
          >
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
      </div> -->
      <p class="mb-2 body-2">
        Request created <span
          class="order__changelog_date"
          @click="openStatusHistoryDialog = true"
        >{{ formatDate(order.submitted_date, true) }}</span>
        <br>
        {{ `by ${userWhoUploadedTheRequest ? userWhoUploadedTheRequest :''}` }}
      </p>
      <p
        v-if="order.ocr_request.sent_to_tms"
        class="mb-2 body-2"
      >
        Submitted to TMS <span
          class="order__changelog_date"
          @click="openStatusHistoryDialog = true"
        >{{ formatDate(order.ocr_request.sent_to_tms.created_at, true) }}</span>
        <br>
        {{ `by ${order.ocr_request.sent_to_tms.user.name}` }}
      </p>
      <p class="mb-2 body-2">
        Last updated <span
          class="order__changelog_date"
          @click="openStatusHistoryDialog = true"
        >{{ formatDate(order.updated_at, true) }}</span>
      </p>
      <a
        class="caption text-uppercase text-decoration-underline slate-gray--text"
        @click.prevent="openStatusHistoryDialog = true"
      >
        History
      </a>
      <StatusHistoryDialog
        :order="order"
        :open="openStatusHistoryDialog"
        @close="openStatusHistoryDialog = false"
      />
    </div>

    <div class="form__section">
      <div
        :id="sections.shipment.id"
        class="form__section-title"
      >
        <h3>{{ sections.shipment.label }}</h3>
      </div>

      <div class="section__rootfields">
        <FormFieldInputAutocomplete
          v-if="!!options.extra.profit_tools_enable_templates"
          references="tms_template_dictid"
          :label="options.labels.tms_template_dictid || 'TMS Template'"
          :value="order.tms_template_dictid"
          :autocomplete-items="tmsTemplates"
          item-text="item_display_name"
          item-value="id"
          :display-value="value => dictionaryItemDisplayValue(value, tmsTemplates)"
          :edit-mode="editMode"
          @change="event => handleChange({ path:'tms_template_dictid', ...event })"
        />
        <FormFieldManaged
          v-if="isManagedByTemplate"
          references="division_code"
          :label="options.labels.division_code || 'Division'"
        />
        <FormFieldSelectDivisionCodes
          v-else-if="!!options.extra.enable_divisions"
          references="division_code"
          :label="options.labels.division_code || 'Division'"
          :value="order.division_code"
          :edit-mode="editMode"
          :t-company-id="order.t_company_id"
          :t-tms-provider-id="order.t_tms_provider_id"
          :division-code="order.division_code"
          @change="event => handleChange({ path:'division_code', ...event})"
        />
        <FormFieldSelect
          v-if="fieldShouldBeShown('shipment_direction')"
          references="shipment_direction"
          :label="options.labels.shipment_direction || 'Shipment direction'"
          :value="order.shipment_direction"
          :items="shipmentDirection"
          item-text="name"
          item-value="id"
          :edit-mode="editMode"
          @change="event => handleChange({ path:'shipment_direction', ...event})"
        />
        <FormFieldSwitch
          v-if="fieldShouldBeShown('expedite')"
          references="expedite"
          :label="options.labels.expedite || 'Expedite'"
          :value="order.expedite"
          :edit-mode="editMode"
          @change="event => handleChange({ path:'expedite', ...event})"
        />
        <FormFieldSwitch
          v-if="fieldShouldBeShown('hazardous')"
          references="hazardous"
          :label="options.labels.hazardous || 'Hazardous'"
          :value="order.hazardous"
          :edit-mode="editMode"
          @change="event => handleChange({ path:'hazardous', ...event})"
        />
      </div>

      <div class="form__sub-section">
        <div class="form__section-title">
          <h3 :id="sections.equipment.id">
            {{ sections.equipment.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldInputAutocomplete
            v-if="!!options.extra.itg_enable_containers"
            references="container_dictid"
            :label="options.labels.container_dictid || 'ITG Container Size/Type'"
            :value="order.container_dictid"
            :autocomplete-items="itgContainers"
            :item-text="item => `${item.item_display_name} (${item.item_key})`"
            item-value="id"
            :display-value="(value)=> dictionaryItemDisplayKeyValue(value, itgContainers)"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'container_dictid', ...event})"
          />
          <FormFieldEquipmentType
            v-else
            :label="options.labels.t_equipment_type_id || 'Equipment Type'"
            references="t_equipment_type_id"
            :company-id="order.t_company_id"
            :tms-provider-id="order.t_tms_provider_id"
            :carrier="order.carrier"
            :equipment-size="order.equipment_size"
            :equipment-type="order.equipment_type"
            :recognized-text="order.equipment_type_raw_text"
            :unit-number="order.unit_number"
            :verified="order.equipment_type_verified"
            @change="event => handleChange({ path:'t_equipment_type_id', ...event})"
          />

          <FormFieldInput
            v-if="fieldShouldBeShown('unit_number')"
            references="unit_number"
            :label="options.labels.unit_number || 'Unit number'"
            :value="order.unit_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'unit_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('seal_number')"
            references="seal_number"
            :label="options.labels.seal_number || 'Seal number'"
            :value="order.seal_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'seal_number', ...event})"
          />
        </div>
      </div>

      <div class="form__sub-section">
        <div class="form__section-title">
          <h3 :id="sections.origin.id">
            {{ sections.origin.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldInput
            v-if="fieldShouldBeShown('reference_number')"
            references="reference_number"
            :label="options.labels.reference_number || 'Reference number'"
            :value="order.reference_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'reference_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('customer_number')"
            references="customer_number"
            :label="options.labels.customer_number || 'Customer number'"
            :value="order.customer_number === null ? '---' : order.customer_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'customer_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('load_number')"
            references="load_number"
            :label="options.labels.load_number || 'Load number'"
            :value="order.load_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'load_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('purchase_order_number')"
            references="purchase_order_number"
            :label="options.labels.purchase_order_number || 'Purchase Order number'"
            :value="order.purchase_order_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'purchase_order_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('release_number')"
            references="release_number"
            :label="options.labels.release_number || 'Release number'"
            :value="order.release_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'release_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('pickup_number')"
            references="pickup_number"
            :label="options.labels.pickup_number || 'Pickup number'"
            :value="order.pickup_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'pickup_number', ...event})"
          />
          <FormFieldInputAutocomplete
            v-if="!!options.extra.enable_dictionary_items_carrier"
            references="carrier_dictid"
            :label="options.labels.carrier_dictid || 'SSL'"
            :value="order.carrier_dictid"
            :autocomplete-items="carrierItems"
            :item-text="item => `${item.item_display_name} (${item.item_key})`"
            item-value="id"
            :display-value="(value)=> dictionaryItemDisplayKeyValue(value, carrierItems)"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'carrier_dictid', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('vessel') && !options.extra.enable_dictionary_items_vessel"
            references="vessel"
            :label="options.labels.vessel || 'Vessel'"
            :value="order.vessel"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'vessel', ...event})"
          />
          <FormFieldInputAutocomplete
            v-if="!!options.extra.enable_dictionary_items_vessel"
            references="vessel_dictid"
            :label="options.labels.vessel || 'Vessel'"
            :value="order.vessel_dictid"
            :autocomplete-items="vesselItems"
            item-text="item_display_name"
            item-value="id"
            :display-value="(value)=> dictionaryItemDisplayValue(value, vesselItems)"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'vessel_dictid', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('voyage')"
            references="voyage"
            :label="options.labels.voyage || 'Voyage'"
            :value="order.voyage"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'voyage', ...event})"
          />
          <FormFieldDate
            v-if="fieldShouldBeShown('pickup_by_date')"
            references="pickup_by_date"
            :label="options.labels.pickup_by_date || 'Pickup by date'"
            :value="order.pickup_by_date"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'pickup_by_date', ...event})"
          />
          <!-- <FormFieldTime
            v-if="fieldShouldBeShown('pickup_by_time')"
            references="pickup_by_time"
            :label="options.labels.pickup_by_time || 'Pickup by time'"
            :value="order.pickup_by_time"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'pickup_by_time', ...event})"
          /> -->
          <FormFieldTimeMask
            v-if="fieldShouldBeShown('pickup_by_time')"
            references="pickup_by_time"
            :label="options.labels.pickup_by_time || 'Pickup by time'"
            :value="order.pickup_by_time"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'pickup_by_time', ...event})"
          />
          <FormFieldDate
            v-if="fieldShouldBeShown('cutoff_date')"
            references="cutoff_date"
            :label="options.labels.cutoff_date || 'Cutoff Date'"
            :value="order.cutoff_date"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'cutoff_date', ...event})"
          />
          <!-- <FormFieldTime
            v-if="fieldShouldBeShown('cutoff_time')"
            references="cutoff_time"
            :label="options.labels.cutoff_time || 'Cutoff Time'"
            :value="order.cutoff_time"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'cutoff_time', ...event})"
          /> -->
          <FormFieldTimeMask
            v-if="fieldShouldBeShown('cutoff_time')"
            references="cutoff_time"
            :label="options.labels.cutoff_time || 'Cutoff Time'"
            :value="order.cutoff_time"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'cutoff_time', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('booking_number')"
            references="booking_number"
            :label="options.labels.booking_number || 'Booking number'"
            :value="order.booking_number === null ? '---' : order.booking_number"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'booking_number', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('master_bol_mawb')"
            references="master_bol_mawb"
            :label="options.labels.master_bol_mawb || 'Master BOL MAWB'"
            :value="order.master_bol_mawb"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'master_bol_mawb', ...event})"
          />
          <FormFieldInput
            v-if="fieldShouldBeShown('house_bol_hawb')"
            references="house_bol_hawb"
            :label="options.labels.house_bol_hawb || 'House BOL MAWB'"
            :value="order.house_bol_hawb"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'house_bol_hawb', ...event})"
          />
        </div>
      </div>
      <div
        v-if="!isManagedByTemplate"
        class="form__sub-section"
      >
        <div class="form__section-title">
          <h3 :id="sections.bill_to.id">
            {{ sections.bill_to.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldAddressSwitchVerify
            :recognized-text="order.bill_to_address_raw_text || 'Address not recognized'"
            :verified="order.bill_to_address_verified"
            :matched-address="order.bill_to_address"
            references="bill_to_address"
            :edit-mode="false"
            billable
            v-bind="{...addressSearchProps}"
            @change="event => handleChange({ path:'bill_to_address', value: event, saveAll: event.saveAll })"
          />
          <FormFieldTextArea
            v-if="fieldShouldBeShown('bill_comment')"
            references="bill_comment"
            :label="options.labels.bill_comment || 'Billing comments'"
            :value="order.bill_comment"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'bill_comment', ...event})"
          />
        </div>
        <div class="form__section-title">
          <h3 :id="sections.charges.id">
            {{ sections.charges.label }}
          </h3>
        </div>
        <div class="section__rootfields">
          <FormFieldTextArea
            v-if="fieldShouldBeShown('line_haul')"
            references="line_haul"
            :label="options.labels.line_haul || 'Line Haul'"
            :value="order.line_haul"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'line_haul', ...event})"
          />
          <FormFieldTextArea
            v-if="fieldShouldBeShown('fuel_surcharge')"
            references="fuel_surcharge"
            :label="options.labels.fuel_surcharge || 'FSC'"
            :value="order.fuel_surcharge"
            :edit-mode="editMode"
            @change="event => handleChange({ path:'fuel_surcharge', ...event})"
          />
        </div>
      </div>
      <div v-else>
        <div
          :id="sections.bill_to.id"
          class="form__section-title form__section-title--managed d-flex align-center justify-center mb-2"
        >
          <h3>
            {{ sections.bill_to.label }} managed by template
          </h3>
        </div>
      </div>

      <div
        v-if="!isManagedByTemplate"
        class="form__section"
      >
        <div
          :id="sections.itinerary.id"
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
            :disabled="isLocked"
            @click="handleItinerayEdit(sections.itinerary.id)"
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
              v-bind="{...addressSearchProps}"
              @change="(event) => handleChange({path: `order_address_events.${index}`, value: event, saveAll: event.saveAll})"
              @delete="(e) => handleDelete(index)"
              @sort="(e) => moveObjectPositionInArray(order.order_address_events[index].id, e)"
            />
          </div>
        </div>
      </div>
      <div v-else>
        <div
          :id="sections.itinerary.id"
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
            v-if="fieldShouldBeShown('ship_comment')"
            references="ship_comment"
            :label="options.labels.ship_comment || 'Shipment comments'"
            :value="order.ship_comment"
            :edit-mode="editMode"
            @change="event => handleChange({path: 'ship_comment', ...event})"
          />
        </div>
      </div>
      <div
        v-if="!isManagedByTemplate"
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
          <div class="form__section-title">
            <h3>Item {{ index + 1 }}</h3>
          </div>
          <div class="section__rootfields">
            <FormFieldTextArea
              :references="`order_line_items.${item.real_index}.contents`"
              :label="options.labels.order_line_item_contents || 'Contents'"
              :value="item.contents"
              :edit-mode="editMode"
              @change="event => handleChange({ path:`order_line_items.${item.real_index}.contents`, ...event})"
            />
            <FormFieldInput
              :references="`order_line_items.${item.real_index}.quantity`"
              :label="options.labels.order_line_item_quantity || 'Quantity'"
              type="number"
              :value="item.quantity"
              :edit-mode="editMode"
              @change="event => handleChange({ path:`order_line_items.${item.real_index}.quantity`, ...event})"
            />
            <FormFieldInput
              :references="`order_line_items.${item.real_index}.weight`"
              :label="options.labels.order_line_item_weight || 'Weight'"
              type="number"
              :value="item.weight"
              :edit-mode="editMode"
              @change="event => handleChange({ path:`order_line_items.${item.real_index}.weight`, ...event})"
            />
          </div>
        </div>
      </div>
      <div v-else>
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
import { scrollTo } from '@/utils/scroll_to'
import permissions from '@/mixins/permissions'
import { mapState, mapActions, mapGetters } from 'vuex'
import get from 'lodash/get'
import { statuses } from '@/enums/app_objects_types'

import { getOrderDetail, postSendToTms, delDeleteOrder, postSendToClient, replicateOrder } from '@/store/api_calls/orders'
import { getSourceFileDownloadURL } from '@/store/api_calls/requests'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import utils, { type } from '@/store/modules/utils'
import { downloadFile } from '@/utils/download_file'

import FormFieldDate from '@/components/FormFields/FormFieldDate'
// import FormFieldTime from '@/components/FormFields/FormFieldTime'
import FormFieldTimeMask from '@/components/FormFields/FormFieldTimeMask'
import FormFieldInput from '@/components/FormFields/FormFieldInput'
import FormFieldSwitch from '@/components/FormFields/FormFieldSwitch'
import FormFieldTextArea from '@/components/FormFields/FormFieldTextArea'
import FormFieldAddressSwitchVerify from '@/components/FormFields/FormFieldAddressSwitchVerify'
import FormFieldItineraryEdit from '@/components/FormFields/FormFieldItineraryEdit'
import FormFieldEquipmentType from '@/components/FormFields/FormFieldEquipmentType'
import OutlinedButtonGroup from '@/components/General/OutlinedButtonGroup'
import FormFieldSelectDivisionCodes from '@/components/FormFields/FormFieldSelectDivisionCodes'
import FormFieldSelect from '@/components/FormFields/FormFieldSelect'
import FormFieldInputAutocomplete from '@/components/FormFields/FormFieldInputAutocomplete'
import FormFieldManaged from '@/components/FormFields/FormFieldManaged'
import RequestStatus from '@/components/RequestStatus'
import StatusHistoryDialog from './StatusHistoryDialog'
import OrderAuditDialog from './OrderAuditDialog'
import { formatDate } from '@/utils/dates'

export default {
  name: 'OrderDetailsForm',
  components: {
    FormFieldDate,
    // FormFieldTime,
    FormFieldTimeMask,
    FormFieldInput,
    OrderAuditDialog,
    FormFieldSwitch,
    FormFieldTextArea,
    FormFieldAddressSwitchVerify,
    FormFieldItineraryEdit,
    FormFieldEquipmentType,
    OutlinedButtonGroup,
    FormFieldSelect,
    FormFieldInputAutocomplete,
    FormFieldSelectDivisionCodes,
    FormFieldManaged,
    RequestStatus,
    StatusHistoryDialog
  },
  mixins: [isMobile, permissions],
  props: {
    backButton: {
      type: Boolean,
      required: false,
      default: true
    },
    virtualBackButton: {
      type: Boolean,
      required: false,
      default: true
    },
    redirectBack: {
      type: Boolean,
      required: false,
      default: false
    },
    options: {
      type: Object,
      required: false,
      default: () => ({ hidden: [], extra: {}, address_search: {}, labels: {} })
    },
    tmsTemplates: {
      type: Array,
      required: false,
      default: () => []
    },
    itgContainers: {
      type: Array,
      required: false,
      default: () => []
    },
    carrierItems: {
      type: Array,
      required: false,
      default: () => []
    },
    vesselItems: {
      type: Array,
      required: false,
      default: () => []
    }
  },
  data () {
    return {
      loading: false,
      divisionCodes: [],
      sentToTms: false,
      openStatusHistoryDialog: false,
      openAuditDialog: false,
      shipmentDirection: [
        { id: 'import', name: 'Import' },
        { id: 'export', name: 'Export' },
        { id: 'oneway', name: 'One way' },
        { id: 'crosstown', name: 'Crosstown' }
      ]
    }
  },
  computed: {
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),
    ...mapState(orderForm.moduleName, {
      order: state => state.order,
      editMode: state => state.editMode,
      highlights: state => state.highlights,
      sections: state => state.sections
    }),

    isManagedByTemplate () {
      return this.order.tms_template_dictid !== null && !!this.options.extra.profit_tools_enable_templates
    },

    addressSearchProps () {
      return {
        'enable-address-filters': get(this.options, 'address_search.address_filters', true),
        'enable-search': get(this.options, 'address_search.search', false)
      }
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
      if (this.sentToTms || (!this.hasPermission('tms-resubmit') && !this.hasPermission('tms-submit'))) {
        return true
      }

      if (this.hasPermission('tms-resubmit')) {
        return false
      }

      const alreadySentToTmsStatuses = [
        statuses.sendingToWint,
        statuses.successSendingToWint,
        statuses.shipmentCreatedByWint,
        statuses.updatingToWint,
        statuses.failureUpdatingToWint,
        statuses.successUpdatingToWint,
        statuses.shipmentUpdatedByWint,
        statuses.shipmentNotUpdatedByWint,
        statuses.updatedBySubsequentOrder,
        statuses.successImageuplodingToBlackfl,
        statuses.failureImageuplodingToBlackfl,
        statuses.untriedImageuplodingToBlackfl
      ]

      return (this.order.tms_shipment_id !== null && this.order.tms_shipment_id !== undefined) ||
          (alreadySentToTmsStatuses.includes(this.orderSystemStatus))
    },

    orderSystemStatus () {
      return get(this.order, 'ocr_request.latest_ocr_request_status.status', '')
    },

    isInProcessedState () {
      const validStatuses = [
        statuses.processOcrOutputFileComplete,
        statuses.ocrPostProcessingComplete
      ]
      return validStatuses.includes(this.orderSystemStatus)
    },
    splitButtonMainAction () {
      if (this.orderSystemStatus === statuses.processOcrOutputFileReview) {
        return {
          title: 'Send to Client',
          action: this.postSendToClient,
          disabled: this.sentToTms || this.isLocked || !this.hasPermission('admin-review-edit')
        }
      }

      return {
        title: 'Send to TMS',
        action: this.postSendToTms,
        disabled: this.sendToTmsDisabled || this.isLocked
      }
    },
    userWhoUploadedTheRequest () {
      return this.order.upload_user_name ? this.order.upload_user_name : this.order.email_from_address
    }
  },

  watch: {
    isMobile (newValue) {
      if (!newValue && this.backButton) {
        return this[type.setSidebar]({ show: true })
      }
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
      setFormOrder: orderFormTypes.setFormOrder,
      startHover: orderFormTypes.startHover,
      stopHover: orderFormTypes.stopHover,
      toggleEdit: orderFormTypes.toggleEdit,
      cancelEdit: orderFormTypes.cancelEdit,
      addHighlight: orderFormTypes.addHighlight,
    }),

    formatDate,

    fieldShouldBeShown (fieldName) {
      return !this.options.hidden.includes(fieldName)
    },

    async handleChange (event) {
      const [error, data] = await this.updateOrder(event)

      if (error !== undefined) {
        this[type.setSnackbar]({
          show: true,
          message: get(error, 'response.data.message') || 'There was an error saving the information'
        })
      }
    },

    async refreshOrder () {
      const [error, data] = await getOrderDetail(this.order.id)

      if (error !== undefined) {
        return
      }
      this.setFormOrder(data)
    },

    async postSendToTms () {
      const onConfirm = async () => {
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
          await this.refreshOrder()
        }

        this[type.setSnackbar]({ show: true, message })
      }

      if (this.isSuperadmin()) {
        await this[type.setConfirmationDialog]({
          title: 'Are you sure you want to send this order to the TMS?',
          onConfirm,
          onCancel: () => { this.loading = false }
        })
        return
      }

      await onConfirm()
    },

    async postSendToClient () {
      const [error] = await postSendToClient(this.order.id)
      let message = ''

      if (error !== undefined) {
        switch (error.response.status) {
          case 403:
            message = 'You are not authorized'
            break
          default:
            message = 'An error has occurred, please contact to technical support'
            break
        }
      } else {
        message = 'Sending the order to the client is in progress'
        this.sentToTms = true
        await this.refreshOrder()
      }

      this[type.setSnackbar]({ show: true, message })
    },

    async downloadSourceFile (requestId) {
      this.loading = true
      const [error, data] = await getSourceFileDownloadURL(requestId)

      if (error === undefined) {
        downloadFile(data.data)
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

    async replicateOrder (orderId) {
      this.loading = true
      await this[type.setConfirmationDialog]({
        title: 'Are you sure you want to replicate this order?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await replicateOrder(orderId)
          let message = ''

          if (!error) {
            this.loading = false
            message = 'Order replicated'
          } else {
            message = 'Error trying to replicate the order'
          }
          await this[type.setSnackbar]({ show: true, message })
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    goToOrdersList () {
      this.redirectBack ? this.$router.back() : this.$router.push('/inbox')
    },

    handleItinerayEdit (elemetToScrollID) {
      this.toggleEdit()
      setTimeout(() => {
        scrollTo(elemetToScrollID, '#order-form', this.$refs.orderHeading.scrollHeight)
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
      this.handleChange({
        path: 'order_address_events',
        value: [
          ...this.order.order_address_events,
          newEvent
        ]
      })
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
      this.handleChange({ path: 'order_address_events', value: arr })
    },

    handleDelete (index) {
      const arr = this.order.order_address_events
      arr[index].deleted_at = true
      this.handleChange({ path: 'order_address_events', value: arr })
    },

    async addTMSId () {
      await this[type.setConfirmationDialog]({
        title: 'Please type the desired TMS ID',
        hasInputValue: true,
        onConfirm: async (value) => {
          this.handleChange({ path: 'tms_shipment_id', value })

          await this[type.setSnackbar]({ show: true, message: 'TMS ID added' })
        },
        onCancel: () => { this.loading = false }
      })
    },
    dictionaryItemDisplayKeyValue (value, items) {
      const result = items.filter(el => el.id === value)
      return result.length > 0 ? `${result[0].item_display_name} (${result[0].item_key})` : value
    },
    dictionaryItemDisplayValue (value, items) {
      const result = items.filter(el => el.id === value)
      return result.length > 0 ? result[0].item_display_name : value
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
    background: linear-gradient(
      180deg,
      rgba(0, 60, 113, 0.1) 0%,
      rgba(0, 60, 113, 0.05) 31.77%,
      rgba(0, 60, 113, 0) 100%
    );
  }

  .order__title {
    font-size: rem(20);
    font-weight: 500;
    line-height: (23.4 / 20);
    letter-spacing: rem(0.15);
    color: var(--v-slate-gray-base);
  }

  .order__tms {
    display: flex;
    align-items: center;
    font-size: rem(12);
    line-height: (18 /12);
    letter-spacing: rem(0.25);
    color: var(--v-slate-gray-base);

    strong {
      font-weight: 700;
      margin-right: rem(4);
    }

    i {
      font-size: rem(14);
      color: var(--v-slate-gray-base);
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
  background-color: var(--v-slate-gray-base);
  h3 {
    text-transform: uppercase;
    font-size: rem(13);
    font-weight: 700;
    line-height: (24 / 13);
    letter-spacing: rem(0.75);
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
    border-bottom: 1px solid rgba(var(--v-slate-gray-base-rgb), 50%);

    h3 {
      color: map-get($colors, mainblue);
    }
  }
}

.section__rootfields {
  margin-bottom: rem(18);

  .form-field:nth-child(even),
  & > div:nth-child(even) .form-field-presentation {
    background-color: #f5f6f7;
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
    letter-spacing: rem(0.25);
  }

  .field__value {
    max-width: 50%;
    align-items: center;
    display: flex;
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
    background-color: var(--v-slate-gray-base);
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
  .order__changelog_date {
    color: var(--v-primary-base);
    text-decoration: underline;
    cursor: pointer;
  }
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
