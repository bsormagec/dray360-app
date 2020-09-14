<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation :menu-items="items" />
    </div>
    <div
      class="col-10 mapping__panel"
    >
      <h1>Profit tools mapping admin panel</h1>
      <div
        class="row"
      >
        <div class="col-6">
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">
                    <h2>&nbsp;</h2>
                  </th>
                  <th class="text-left">
                    <h2>Dray360 Field</h2>
                  </th>
                  <th class="text-left">
                    <h2>Enter custom value</h2>
                  </th>
                  <th class="text-left">
                    <h2>Ref Type</h2>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Ref 2</td>
                  <td>
                    <v-select
                      v-model="ds_ref2_text"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                      :clearable="true"
                      @change="changeRef1"
                    />
                  </td>
                  <td>
                    <v-text-field
                      v-if="customRef1"
                      v-model="ds_ref2_text"
                    />
                  </td>
                  <td>
                    <v-select
                      v-model="ds_ref2_type"
                      :items="reftypes"
                      item-text="field_name"
                      item-value="value"
                      :clearable="true"
                    />
                  </td>
                </tr>
                <tr>
                  <td>Ref 3</td>
                  <td>
                    <v-select
                      v-model="ds_ref3_text"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                      :clearable="true"
                      @change="changeRef2"
                    />
                  </td>
                  <td>
                    <v-text-field
                      v-if="customRef2"
                      v-model="ds_ref3_text"
                    />
                  </td>
                  <td>
                    <v-select
                      v-model="ds_ref3_type"
                      :items="reftypes"
                      item-text="field_name"
                      item-value="value"
                      :clearable="true"
                    />
                  </td>
                </tr>
                <tr
                  v-for="(el, index) in custom"
                  :key="(index)"
                >
                  <td>Custom{{ index+1 }}</td>

                  <td>
                    <v-select
                      :key="fieldNames.field_name"
                      v-model="custom[index]"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                      :clearable="true"
                      @change="(value) => changeCustomValues(value, index)"
                    />
                  </td>
                  <td>
                    <v-text-field
                      v-if="customValue[index]"
                      v-model="custom[index]"
                    />
                  </td>
                </tr>
                <tr>
                  <td>Shipment Notes </td>
                  <td>
                    <v-select
                      v-model="shipment_notes"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                      :multiple="true"
                      :clearable="true"
                    />
                  </td>
                </tr>
                <tr>
                  <td>Billing Notes </td>
                  <td>
                    <v-select
                      v-model="billing_notes"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                      :multiple="true"
                      :clearable="true"
                    />
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </div>
      </div>
      <div class="row">
        <div class="col-6 right">
          <v-btn
            class="btn large primary d-block "
            @click="save"
          >
            save
          </v-btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import { mapActions, mapState } from '@/utils/vuex_mappings'
import companies, { types } from '@/store/modules/companies'
import { reqStatus } from '@/enums/req_status'
import utils, { type } from '@/store/modules/utils'

export default {
  components: {
    SidebarNavigation
  },
  data () {
    return {
      ...mapState(companies.moduleName, { company: state => state.company }),
      items: [
        {
          text: 'Mapping Fields',
          path: '/mapping'
        }
      ],
      ds_ref2_text: '',
      ds_ref2_type: '',
      ds_ref3_text: '',
      ds_ref3_type: '',
      objectNameRef1: '',
      objectNameRef2: '',
      objectNameCustom: [],
      customRef1: false,
      customRef2: false,
      customValue: [],
      shipment_notes: [],
      billing_notes: [],
      ref2_field_name: [],
      ref2_type: [],
      custom: [],
      reftypes: [],
      mappings: [
        { field_name: 'BOOKING #', value: 18 },
        { field_name: 'CONTAINER #', value: 20 },
        { field_name: 'LOAD #', value: 29 },
        { field_name: 'CUSTOMER #', value: 27 },
        { field_name: 'MASTER BL #', value: 15 },
        { field_name: 'PO #', value: 3 },
        { field_name: 'REF #', value: 2 },
        { field_name: 'RELEASE #', value: 9 },
        { field_name: 'SEAL #', value: 21 },
        { field_name: 'FWDR REF #', value: 22 }
      ],
      fieldNames: [
        { field_name: 'port_ramp_of_origin_address', value: 'port ramp of origin' },
        { field_name: 'port_ramp_of_destination_address', value: 'port ramp of destination' },
        { field_name: 'order_type', value: 'shipment direction' },
        { field_name: 'master_bol_mawb', value: 'master BOL MAWB' },
        { field_name: 'house_bol_hawb', value: 'house BOL MAWB' },
        { field_name: 'reference_number', value: 'reference number' },
        { field_name: 'ship_comment', value: 'shipment notes' },
        { field_name: 'bill_comment', value: 'billing notes' },
        { field_name: 'line_haul', value: 'line haul' },
        { field_name: 'unit_number', value: 'unit number' },
        { field_name: 'equipment_size', value: 'size' },
        { field_name: 'bill_to_address', value: 'bill to' },
        { field_name: 'equipment', value: 'type' },
        { field_name: 'equipment_type', value: 'equipment' },
        { field_name: 'hazardous', value: 'hazardous' },
        { field_name: 'one_way', value: 'one way' },
        { field_name: 'owner_or_ss_company', value: 'owner or SS company' },
        { field_name: 'rate_quote_number', value: 'rate quote number' },
        { field_name: 'shipment_designation', value: 'shipment designation' },
        { field_name: 'shipment_direction', value: 'shipment direction' },
        { field_name: 'vessel', value: 'vessel' },
        { field_name: 'voyage', value: 'voyage' },
        { field_name: 'yard_pre_pull', value: 'yard pre-pull' },
        { field_name: 'other_value', value: 'Other' }
      ]
    }
  },
  created () {
    this.custom = new Array(10).fill(null)
    this.customValue = new Array(10).fill(null)
  },
  mounted () {
    this.getNames()
    this.getCompanybyId()
  },
  methods: {
    ...mapActions(companies.moduleName, [types.updateCompaniesMappingField, types.getCompany]),
    ...mapActions(utils.moduleName, [type.setSnackbar]),
    getNames () {
      Object.values(this.mappings).forEach(key => {
        this.reftypes.push(key)
      })
    },
    changeRef1 (value) {
      if (value === 'other_value') {
        this.customRef1 = true
        this.ds_ref2_text = ''
        this.objectNameRef1 = 'value'
      } else {
        this.customRef1 = false
        this.objectNameRef1 = 'source'
      }
    },
    changeRef2 (value) {
      if (value === 'other_value') {
        this.customRef2 = true
        this.ds_ref3_text = ''
        this.objectNameRef2 = 'value'
      } else {
        this.customRef2 = false
        this.objectNameRef2 = 'source'
      }
    },
    changeCustomValues (value, index) {
      if (value === 'other_value') {
        this.customValue[index] = true
        this.custom[index] = ''
        this.objectNameCustom[index] = 'value'
      } else {
        this.customValue[index] = false
        this.objectNameCustom[index] = 'source'
      }
    },
    async save () {
      const jsondata = {}
      if (this.ds_ref2_type !== undefined && this.ds_ref2_type !== '') { jsondata.ds_ref2_type = { value: this.ds_ref2_type } }
      if (this.ds_ref3_type !== undefined && this.ds_ref3_type !== '') { jsondata.ds_ref3_type = { value: this.ds_ref3_type } }
      if (this.ds_ref2_text !== undefined && this.ds_ref2_text !== '') { jsondata.ds_ref2_text = { [this.objectNameRef1]: this.ds_ref2_text } }
      if (this.ds_ref3_text !== undefined && this.ds_ref3_text !== '') { jsondata.ds_ref3_text = { [this.objectNameRef2]: this.ds_ref3_text } }
      if (this.billing_notes.length !== 0) { jsondata.bill_comment = { source: this.billing_notes } }
      if (this.shipment_notes.length !== 0) { jsondata.ship_comment = { source: this.shipment_notes } }

      this.custom.forEach((value, i) => {
        if (this.isCustomValue(value) && value !== null && value !== undefined && value !== '') {
          jsondata[`custom${i + 1}`] = { value: value }
        } else if (!this.isCustomValue(value) && value !== null && value !== '') {
          jsondata[`custom${i + 1}`] = { source: value }
        }
      })
      const status = await this[types.updateCompaniesMappingField]({ id: this.$route.params.id, changes: { refs_custom_mapping: jsondata } })
      if (status === reqStatus.success) {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'Mappings updated'
        })
      } else {
        await this[type.setSnackbar]({
          show: false,
          showSpinner: false,
          message: 'Error'
        })
      }
    },

    async getCompanybyId () {
      const status = await this[types.getCompany]({ id: this.$route.params.id })
      if (status === reqStatus.success) {
        if (this.company().refs_custom_mapping !== null) {
          this.getJsonValues(this.company().refs_custom_mapping)
        }
      }
    },
    getJsonValues (val) {
      Object.entries(val).forEach(([key, value]) => {
        switch (key) {
          case 'ds_ref2_text':
            if (this.isCustomValue(value.source)) {
              this.customRef1 = this.isCustomValue(value.source)
              this.objectNameRef1 = 'value'
              this.ds_ref2_text = value.value
            } else {
              this.objectNameRef1 = 'source'
              this.ds_ref2_text = value.source
            }
            break
          case 'ds_ref2_type':
            this.ds_ref2_type = value.value
            break
          case 'ds_ref3_text':
            if (this.isCustomValue(value.source)) {
              this.customRef2 = this.isCustomValue(value.source)
              this.objectNameRef2 = 'value'
              this.ds_ref3_text = value.value
            } else {
              this.objectNameRef2 = 'source'
              this.ds_ref3_text = value.source
            }
            break
          case 'ds_ref3_type':
            this.ds_ref3_type = value.value
            break
          case 'bill_comment':
            this.billing_notes = value.source
            break
          case 'ship_comment':
            this.shipment_notes = value.source
            break
          case key.substr('custom'):
            var customIndex = parseInt(key.match(/\d+/g)) - 1
            if (this.isCustomValue(value.source)) {
              this.$set(this.customValue, customIndex, true)
              this.$set(this.custom, customIndex, value.value)
            } else {
              this.$set(this.custom, customIndex, value.source)
            }
            break
          default:
            break
        }
      })
    },
    isCustomValue (val) {
      if (this.fieldNames.find(element => element.field_name === val) === undefined) { return true } else { return false }
    }
  }
}
</script>
<style lang="scss" scoped>
    .mapping__panel{
        h1{
            text-transform: uppercase;
            text-align: center;
        }
    }
</style>
