<template>
  <div class="form-field-element-modal-address">
    <AddressBookModal
      :filters="filters"
      @change.capture="(e) => change(e)"
    />
  </div>
</template>

<script>
import { mapState } from '@/utils/vuex_mappings'
import orders from '@/store/modules/orders'

import AddressBookModal from '@/components/Orders/AddressBookModal'

export default {
  name: 'FormFieldElementAddress',

  components: {
    AddressBookModal
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),

    filters: {
      company_id: null,
      tms_provider_id: null,
      rawtext: '',
      is_terminal_address: false,
      is_billable_address: false
    }
  }),

  beforeMount () {
    this.setFilters()
  },

  methods: {
    change (e) {
      this.$emit('change', e)
    },

    setFilters () {
      /* eslint camelcase: 0 */
      const location = this.field.formLocation
      const {
        t_company_id,
        t_tms_provider_id,
        bill_to_address_raw_text,
        port_ramp_of_origin_address_raw_text,
        port_ramp_of_destination_address_raw_text
      } = this.currentOrder()

      const company_id = t_company_id
      const tms_provider_id = t_tms_provider_id

      if (location.includes('bill to')) {
        this.filters = {
          company_id,
          tms_provider_id,
          rawtext: bill_to_address_raw_text,
          is_terminal_address: false,
          is_billable_address: true
        }
      } else if (location.includes('Port Ramp of Origin')) {
        this.filters = {
          company_id,
          tms_provider_id,
          rawtext: port_ramp_of_origin_address_raw_text,
          is_terminal_address: true,
          is_billable_address: false
        }
      } else if (location.includes('Port Ramp of Destination')) {
        this.filters = {
          company_id,
          tms_provider_id,
          rawtext: port_ramp_of_destination_address_raw_text,
          is_terminal_address: true,
          is_billable_address: false
        }
      } else if (location.includes('itinerary')) {
        this.filters = {
          company_id,
          tms_provider_id,
          rawtext: this.field.value,
          is_terminal_address: false,
          is_billable_address: false
        }
      }
    }
  }
}
</script>

<style>
</style>
