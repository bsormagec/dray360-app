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
                    <h2>Dry360 Field</h2>
                  </th>
                  <th class="text-left">
                    <h2>Ref Type</h2>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Ref 1</td>
                  <td>
                    <v-select
                      v-model="ds_ref2_text"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                    />
                  </td>
                  <td>
                    <v-select
                      v-model="ds_ref2_type"
                      :items="reftypes"
                      item-text="field_name"
                      item-value="value"
                    />
                  </td>
                </tr>
                <tr>
                  <td>Ref 2</td>
                  <td>
                    <v-select
                      v-model="ds_ref3_text"
                      :items="fieldNames"
                      item-text="value"
                      item-value="field_name"
                    />
                  </td>
                  <td>
                    <v-select
                      v-model="ds_ref3_type"
                      :items="reftypes"
                      item-text="field_name"
                      item-value="value"
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
                    />
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
          <div
            v-if="dialog"
          >
            <ErrorHandling
              label="snackbar"
              :message="message"
              :dialog="dialog"
              type="modal"
            />
          </div>
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
import ErrorHandling from '@/components/General/ErrorHandling'
import SidebarNavigation from '@/components/General/SidebarNavigation'
import { mapActions, mapState } from '@/utils/vuex_mappings'
import companies, { types } from '@/store/modules/companies'
import { reqStatus } from '@/enums/req_status'

export default {
  components: {
    SidebarNavigation,
    ErrorHandling
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
      dialog: false,
      message: '',
      ds_ref2_text: '',
      ds_ref2_type: '',
      ds_ref3_text: '',
      ds_ref3_type: '',
      ref_2: '',
      shipment_notes: [],
      billing_notes: [],
      localCompany: {},
      ref2_field_name: [],
      ref2_type: [],
      custom: [],
      customArray: [],
      reftypes: [],
      mappings: [
        { field_name: 'BOOKING #', value: 18 },
        { field_name: 'CONTAINER #', value: 27 },
        { field_name: 'LOAD #', value: 29 },
        { field_name: 'CUSTOMER #', value: 18 },
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
        { field_name: 'yard_pre_pull', value: 'yard pre-pull' }
      ]
    }
  },
  created () {
    this.custom = new Array(10).fill(null)
  },
  mounted () {
    this.getNames()
    this.getCompanybyId()
  },
  methods: {
    ...mapActions(companies.moduleName, [types.updateCompaniesMappingField, types.getCompany]),
    getNames () {
      Object.values(this.mappings).forEach(key => {
        this.reftypes.push(key)
      })
    },
    async save () {
      const jsondata = {
        ds_ref2_text: { source: this.ds_ref2_text },
        ds_ref2_type: { value: this.ds_ref2_type },
        ds_ref3_text: { source: this.ds_ref3_text },
        ds_ref3_type: { value: this.ds_ref3_type },
        bill_comment: { source: this.billing_notes },
        ship_comment: { source: this.shipment_notes }
      }
      this.custom.forEach((value, i) => {
        jsondata[`Custom${i}`] = { source: value }
      })
      this.dialog = false
      const status = await this[types.updateCompaniesMappingField]({ id: this.$route.params.id, changes: { refs_custom_mapping: JSON.stringify(jsondata) } })
      this.dialog = true
      if (status === reqStatus.success) {
        this.message = 'Success'
      } else {
        this.message = 'Error'
      }
    },

    async getCompanybyId () {
      const status = await this[types.getCompany]({ id: this.$route.params.id })
      if (status === reqStatus.success) {
        if (this.company().refs_custom_mapping !== null) {
          this.fieldNames.forEach((key, index) => {
            if (key.field_name === JSON.parse(this.company().refs_custom_mapping).ds_ref2_text.source) {
              this.ds_ref2_text = key.field_name
            } else if (key.field_name === JSON.parse(this.company().refs_custom_mapping).ds_ref3_text.source) {
              this.ds_ref3_text = key.field_name
            }
          })
          if ('ds_ref2_type' in JSON.parse(this.company().refs_custom_mapping)) {
            this.mappings.forEach((key) => {
              if (key.value === JSON.parse(this.company().refs_custom_mapping).ds_ref2_type.value) {
                this.ds_ref2_type = key.value
              } else if (key.value === JSON.parse(this.company().refs_custom_mapping).ds_ref3_type.value) {
                this.ds_ref3_type = key.value
              }
            })
          }

          for (let i = 0; i < 10; i++) {
            if ('Custom' in JSON.parse(this.company().refs_custom_mapping)) {
              this.customArray.push(JSON.parse(this.company().refs_custom_mapping)['Custom' + i].source)
              this.custom = this.customArray
            }
          }
          if ('bill_comment' in JSON.parse(this.company().refs_custom_mapping)) {
            this.billing_notes = JSON.parse(this.company().refs_custom_mapping).bill_comment.source
          }
          if ('ship_comment' in JSON.parse(this.company().refs_custom_mapping)) {
            this.shipment_notes = JSON.parse(this.company().refs_custom_mapping).ship_comment.source
          }

          this.message = 'Success'
        }
      } else {
        this.message = 'Error'
      }
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
