<template>
  <div class="mapping__panel">
    <h6 class="mapping__heading">
      Profit tools mapping admin panel
    </h6>
    <v-row>
      <v-col md="3">
        <v-select
          :v-model="companyId"
          :items="companyList"
          item-text="name"
          item-value="id"
          label="Select Company"
          :clearable="true"
          @change="companySelect"
        />
      </v-col>
    </v-row>
    <v-row>
      <v-col md="8">
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th>
                  <h5>&nbsp;</h5>
                </th>
                <th class="text-left">
                  <h6>Dray360 Field</h6>
                </th>
                <th>
                  <h6>Custom value</h6>
                </th>
                <th>
                  <h6>Ref Type</h6>
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
                <td>Billing Comments </td>
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
              <tr>
                <td>Required Equipment</td>
                <td>
                  <v-select
                    v-model="ds_equip_req"
                    :items="fieldNames"
                    item-text="value"
                    item-value="field_name"
                    :clearable="true"
                  />
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-col>
    </v-row>
    <v-row>
      <v-col
        md="6"
        right
      >
        <v-btn
          class="btn large primary d-block "
          @click="save"
        >
          save
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import companies, { types } from '@/store/modules/companies'
import { reqStatus } from '@/enums/req_status'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import { getCompanies } from '@/store/api_calls/companies'

export default {
  data () {
    return {
      items: [
        {
          text: 'Mapping Fields',
          path: '/mapping'
        }
      ],
      ds_equip_req: '',
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
        { field_name: 'purchase_order_number', value: 'purchase order number' },
        { field_name: 'ship_comment', value: 'shipment notes' },
        { field_name: 'bill_comment', value: 'billing comments' },
        { field_name: 'line_haul', value: 'line haul' },
        { field_name: 'fuel_surcharge', value: 'fsc' },
        { field_name: 'unit_number', value: 'unit number' },
        { field_name: 'equipment_size', value: 'size' },
        { field_name: 'bill_to_address', value: 'bill to' },
        { field_name: 'equipment_type', value: 'type' },
        { field_name: 'hazardous', value: 'hazardous' },
        { field_name: 'one_way', value: 'one way' },
        { field_name: 'shipment_designation', value: 'shipment designation' },
        { field_name: 'shipment_direction', value: 'shipment direction' },
        { field_name: 'vessel', value: 'vessel' },
        { field_name: 'voyage', value: 'voyage' },
        { field_name: 'interchange_count', value: 'interchange count' },
        { field_name: 'total_quantity', value: 'total quantity' },
        { field_name: 'total_weight', value: 'total weight' },
        { field_name: 'customer_number', value: 'customer number' },
        { field_name: 'other_value', value: 'custom value' }
      ],
      companyList: [],
      companyId: undefined
    }
  },
  computed: {
    ...mapState(companies.moduleName, { company: state => state.company })
  },
  created () {
    this.clearFields()
  },
  mounted () {
    this.getNames()
    this.getCompanyList()
  },
  methods: {
    ...mapActions(companies.moduleName, [types.updateCompaniesMappingField, types.getCompany]),
    ...mapActions(utils.moduleName, [utilsActionTypes.setSnackbar]),
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
    companySelect (value) {
      if (value !== undefined) {
        this.companyId = value
        this.getCompanybyId()
      } else {
        this.clearFields()
      }
    },
    clearFields () {
      this.custom = new Array(10).fill(null)
      this.customValue = new Array(10).fill(null)
      this.ds_ref2_text = ''
      this.ds_ref2_type = ''
      this.ds_ref3_text = ''
      this.ds_ref3_type = ''
      this.ds_equip_req = ''
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
      if (this.ds_equip_req !== undefined && this.ds_equip_req !== '') { jsondata.ds_equip_req = { source: this.ds_equip_req } }
      if (this.billing_notes.length !== 0) { jsondata.bill_comment = { source: this.billing_notes } }
      if (this.shipment_notes.length !== 0) { jsondata.ship_comment = { source: this.shipment_notes } }

      this.custom.forEach((value, i) => {
        if (this.isCustomValue(value) && value !== null && value !== undefined && value !== '') {
          jsondata[`custom${i + 1}`] = { value: value }
        } else if (!this.isCustomValue(value) && value !== null && value !== '') {
          jsondata[`custom${i + 1}`] = { source: value }
        }
      })
      const status = await this[types.updateCompaniesMappingField]({ id: this.companyId, changes: { refs_custom_mapping: jsondata } })
      if (status === reqStatus.success) {
        await this.setSnackbar({ message: 'Mappings updated' })
      } else {
        await this.setSnackbar({ message: 'Error' })
      }
    },
    async getCompanybyId () {
      const status = await this[types.getCompany]({ id: this.companyId })
      if (status === reqStatus.success) {
        if (this.company.refs_custom_mapping !== null) {
          this.getJsonValues(this.company.refs_custom_mapping)
        } else {
          this.clearFields()
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
          case 'ds_equip_req':
            this.ds_equip_req = value.source
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
    },
    async getCompanyList () {
      const [error, response] = await getCompanies()
      if (error === undefined) {
        this.clearFields()
        this.companyList = response.data
      }
      this.clearFields()
    }
  }
}
</script>
<style lang="scss" scoped>
.mapping__panel {
  padding: rem(20);
  h6 {
    text-align: left;
  }
}
.mapping__heading {
  display: flex;
  align-items: center;
  margin-bottom: rem(8);
  .v-btn {
    min-width: unset;
    margin-right: rem(8);
  }
}
</style>
