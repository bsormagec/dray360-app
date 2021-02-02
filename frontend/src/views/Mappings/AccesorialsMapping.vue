<template>
  <div class="">
    <div class="col-2">
      <v-select
        v-model="variant"
        item-text="abbyy_variant_name"
        item-value="id"
        :items="variant_list()"
        label="Select Variant"
        clearable
        @click:clear="createJson"
        @change="getAccesorialMapping"
      />
    </div>

    <div class="col-7">
      <v-simple-table>
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">
                <h2>supported charges</h2>
              </th>
              <th class="text-left">
                <h2>Enter value</h2>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(el, index) in json"
              :key="index"
            >
              <td> {{ index }} </td>
              <td>
                <v-text-field
                  v-model="json[index].amount_type_id"
                  label="amount_type_id"
                  clearable
                  :rules="[onlyNumbers]"
                  @click:clear="clear(index)"
                />
              </td>
            </tr>
            <tr>
              <td>
                <v-btn
                  class="btn large primary d-block "
                  @click="save"
                >
                  save
                </v-btn>
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </div>
  </div>
</template>
<script>

import { mapActions, mapState } from 'vuex'
import rulesLibrary, { types } from '@/store/modules/rules_editor'
import accesorialmapping, { accesorialmappingtype } from '@/store/modules/accesorialmapping'
import utils, { type } from '@/store/modules/utils'

export default {
  data () {
    return {
      variant: '',
      ...mapState(rulesLibrary.moduleName, {
        variant_list: state => state.variant_list
      }),
      ...mapState(accesorialmapping.moduleName, {
        mappingData: state => state.AccesorialMapping.mapping
      }),
      onlyNumbers: v => {
        if (!v.trim()) return true
        if (!isNaN(parseFloat(v))) return true
        return 'Only Numbers'
      },
      json: {},
      lineItems: {},
      mapping: {}
    }
  },
  created () {
    this.createJson()
  },
  mounted () {
    this.fetchVariantList()
    this.showSidebar()
  },
  methods: {
    ...mapActions(rulesLibrary.moduleName, [types.getVariantList]),
    ...mapActions(accesorialmapping.moduleName, [accesorialmappingtype.updateAccesorialMapping, accesorialmappingtype.getAccesorialMapping]),
    ...mapActions(utils.moduleName, [type.setSnackbar, type.setSidebar]),

    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    },
    createJson () {
      this.json =
        {
          line_haul: { amount_type_id: '' },
          sc: { amount_type_id: '' },
          chassis_rental: { amount_type_id: '' },
          hazmat: { amount_type_id: '' },
          chassis_split: { amount_type_id: '' },
          'overweight-tri/axle': { amount_type_id: '' },
          tolls: { amount_type_id: '' },
          pre_pull: { amount_type_id: '' },
          reefer: { amount_type_id: '' }
        }
    },
    clear (index) {
      if (isNaN(index)) {
        delete this.mapping[index]
      } else {
        this.createJson()
      }
    },
    async fetchVariantList () {
      await this[types.getVariantList]()
    },
    async getAccesorialMapping () {
      this.createJson()
      if (this.variant !== undefined) {
        await this[accesorialmappingtype.getAccesorialMapping](
          {
            id: this.$route.params.id,
            variant: this.variant
          }
        )
        if (this.mappingData() !== undefined) {
          Object.entries(this.mappingData()).forEach((element, index) => {
            this.json[element[0]].amount_type_id = element[1].amount_type_id
          })
        }
      }
    },
    async save (key) {
      if (this.variant !== '') {
        Object.entries(this.json).forEach(([index, value]) => {
          if (value.amount_type_id !== '' && value.amount_type_id !== null) {
            this.mapping[index] = {
              amount_type_id: value.amount_type_id
            }
          }
        })
        if (Object.keys(this.mapping).length !== 0) {
          const status = await this[accesorialmappingtype.updateAccesorialMapping](
            {
              id: this.$route.params.id,
              variant: this.variant,
              mapping: this.mapping
            })
          if (status.status === 'success') {
            await this[type.setSnackbar]({
              show: true,
              showSpinner: false,
              message: 'Updated'
            })
          }
        } else {
          await this[accesorialmappingtype.updateAccesorialMapping](
            {
              id: this.$route.params.id,
              variant: this.variant,
              mapping: {}
            })
        }
      } else {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'You need to select a variant'
        })
      }
    }
  }
}
</script>
