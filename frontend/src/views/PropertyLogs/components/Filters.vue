<template>
  <v-toolbar
    color="white"
    tile
    dense
    flat
    height="auto"
    class="table__tool-bar"
  >
    <v-container
      fluid
      class="pa-0 py-2"
    >
      <v-row
        align="center"
        dense
      >
        <v-col
          cols="12"
          sm="auto"
        >
          <v-autocomplete
            v-model="filters.property"
            :items="properties"
            label="Property"
            placeholder="Property"
            name="property"
            clearable
            hide-details
          />
        </v-col>
        <v-col
          cols="12"
          sm="3"
          md="2"
          class="mb-3 mb-sm-0"
        >
          <Companies
            v-if="canViewOtherCompanies()"
            :value="filters.companyId"
            onboarding
            @change="newVal => filters.companyId = newVal"
          />
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <DateRange v-model="filters.dateRange" />
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <Users
            v-if="canViewOtherCompanies()"
            :value="filters.userId"
            @change="newVal => filters.userId = newVal"
          />
        </v-col>
        <v-col
          cols="12"
          sm="auto"
          class="mb-2 mb-sm-0"
        >
          <Variants
            :value="filters.variantId"
            @change="newValue => filters.variantId = newValue"
          />
        </v-col>
        <v-spacer />
        <v-col cols="auto">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                icon
                small
                color="primary"
                v-bind="attrs"
                v-on="on"
                @click="resetFilters"
              >
                <v-icon dense>
                  mdi-reload
                </v-icon>
              </v-btn>
            </template>
            <span class="text-caption">Reset Filters</span>
          </v-tooltip>
        </v-col>
        <v-col cols="auto">
          <OptionList
            :value="selectedHeaders"
            :options="columnsList"
            icon="mdi-view-column"
            title="Show and hide columns"
            @input="newVal => $emit('colChange', newVal)"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-toolbar>
</template>

<script>
import permissions from '@/mixins/permissions'

import DateRange from '@/components/Inputs/DateRange'
import OptionList from '@/components/Tables/OptionList'
import Users from '@/components/Inputs/Autocompletes/Users'
import Variants from '@/components/Inputs/Autocompletes/Variants'
import Companies from '@/components/Inputs/Autocompletes/Companies'

export default {
  name: 'Filters',

  components: {
    Users,
    Variants,
    Companies,
    DateRange,
    OptionList,
  },

  mixins: [permissions],

  props: {
    initialFilters: {
      type: Object,
      required: true,
      default: () => ({
        property: '',
        companyId: [],
        dateRange: [],
        userId: [],
        variantId: [],
      })
    },
    headers: {
      type: Array,
      required: true,
    },
    selectedHeaders: {
      type: Array,
      required: true,
      default: () => ([])
    }
  },

  data: (vm) => ({
    filters: {
      property: vm.initialFilters.property,
      companyId: vm.initialFilters.companyId,
      dateRange: vm.initialFilters.dateRange,
      userId: vm.initialFilters.userId,
      variantId: vm.initialFilters.variantId
    },
    properties: [
      'actual_destination',
      'actual_origin',
      'bill_charge',
      'bill_comment',
      'bill_of_lading',
      'bill_to_address_id',
      'booking_number',
      'carrier',
      'carrier_dictid',
      'cc_containersize',
      'cc_containersize_dictid',
      'cc_containertype',
      'cc_containertype_dictid',
      'cc_haulclass',
      'cc_haulclass_dictid',
      'cc_loadedempty',
      'cc_loadedempty_dictid',
      'cc_loadtype',
      'cc_loadtype_dictid',
      'cc_orderclass',
      'cc_orderclass_dictid',
      'cc_orderstatus',
      'chassis_equipment_type_id',
      'created_at',
      'custom1',
      'custom10',
      'custom2',
      'custom3',
      'custom4',
      'custom5',
      'custom6',
      'custom7',
      'custom8',
      'custom9',
      'customer_number',
      'cutoff_date',
      'cutoff_time',
      'deleted_at',
      'division_code',
      'equipment_available_date',
      'equipment_available_time',
      'equipment_provider',
      'equipment_size',
      'eta_date',
      'eta_time',
      'expedite',
      'fuel_surcharge',
      'hazardous',
      'house_bol_hawb',
      'id',
      'interchange_count',
      'interchange_err_count',
      'is_hidden',
      'itgcontainer_dictid',
      'line_haul',
      'load_number',
      'master_bol_mawb',
      'one_way',
      'pickup_by_date',
      'pickup_by_time',
      'pickup_number',
      'port_ramp_of_destination_address_id',
      'port_ramp_of_origin_address_id',
      'preceded_by_order_id',
      'pt_equipmenttype_chassis_dictid',
      'pt_equipmenttype_container_dictid',
      'pt_ref1_text',
      'pt_ref1_type',
      'pt_ref2_text',
      'pt_ref2_type',
      'pt_ref3_text',
      'pt_ref3_type',
      'purchase_order_number',
      'rate_box',
      'reference_number',
      'release_number',
      'request_id',
      'required_equipment',
      'seal_number',
      'ship_comment',
      'shipment_designation',
      'shipment_direction',
      'ssrr_location_address_id',
      'succeded_by_order_id',
      't_company_id',
      't_equipment_type_id',
      't_tms_provider_id',
      'temperature',
      'temperature_uom',
      'termdiv',
      'termdiv_dictid',
      'tms_cancelled_datetime',
      'tms_shipment_id',
      'tms_submission_datetime',
      'tms_template_dictid',
      'tms_template_id',
      'total_accessorial_charges',
      'trailer_number',
      'unit_number',
      'updated_at',
      'variant_id',
      'variant_name',
      'vessel',
      'vessel_dictid',
      'voyage',
    ],
  }),

  computed: {
    columnsList () {
      return this.headers.map(o => ({ name: o.text, value: o.value }))
    },
  },

  watch: {
    filters: {
      handler (newFilters) {
        this.$emit('change', newFilters)
      },
      deep: true
    },
  },

  methods: {
    resetFilters () {
      this.$emit('reset-filters')
      this.filters = this.initialFilters
    },
  }
}
</script>

<style lang="scss" scoped>
.table__tool-bar::v-deep {
  position: sticky;
  top: 0;
  background-color: #FFFFFF;
  z-index: 1;
  & > .v-toolbar__content {
    padding: 0;
  }
}
.v-select.v-text-field::v-deep {
  & .v-select__selections span {
    &:first-child {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    &:last-of-type {
      width: fit-content;
    }
    & + input {
      display: none;
    }
  }
}
</style>
