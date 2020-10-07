<template>
  <!--  eslint-disable vue/no-v-html -->
  <div>
    <div>
      <span><strong> Equipment Type</strong></span>
      <div>Current Matched Equipment Type {{ equipmentType ? equipmentType.equipment_display : '--' }} </div>
      <div v-if="!verified">
        <span>
          Not Verified!
        </span>
        <v-icon color="warning">
          mdi-alert
        </v-icon>
      </div>
      <v-btn
        color="primary"
        outlined

        @click="isOpen=true"
      >
        Choose Different Equipment Type
      </v-btn>
      <v-dialog
        :value="isOpen"
        width="900"
      >
        <v-card>
          <v-container>
            <v-row align="center">
              <v-col cols="3">
                <v-autocomplete
                  v-model="filters.owner"
                  :items="equipmentTypeOptions.equipment_owners"
                  outlined
                  dense
                  clearable
                  label="Steamship Line"
                />
              </v-col>

              <v-col cols="3">
                <v-autocomplete
                  v-model="filters.size"
                  :items="equipmentTypeOptions.equipment_sizes"
                  dense
                  clearable
                  outlined
                  label="Length"
                />
              </v-col>
              <v-col cols="3">
                <v-autocomplete
                  v-model="filters.type"
                  :items="equipmentTypeOptions.equipment_types"
                  dense
                  clearable
                  outlined
                  label="Type"
                />
              </v-col>
              <v-col cols="3">
                <v-autocomplete
                  v-model="filters.scac"
                  :items="equipmentTypeOptions.scacs"
                  dense
                  clearable
                  outlined
                  label="Scac"
                />
              </v-col>
            </v-row>
            <div>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Steamship Line
                      </th>
                      <th class="text-left">
                        Length/Type
                      </th>
                      <th class="text-left" />
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="item in equipment_matches"
                      :key="item.id"
                    >
                      <td>{{ item.equipment_owner }}</td>
                      <td>{{ item.equipment_type_and_size }}</td>
                      <td>
                        <v-btn
                          class="ma-2"
                          outlined
                          color="indigo"
                          @click="() => selectEquipmentType(item)"
                        >
                          Select
                        </v-btn>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </div>
          </v-container>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<script>
/* eslint-disable vue/no-v-html */

import { getEquipmentTypeOptions, getEquipmentTypes } from '@/store/api_calls/equipment'

export default {
  name: 'FormFieldEquipmentType',

  components: {

  },

  props: {
    companyId: { type: Number, required: true },
    tmsProviderId: { type: Number, required: true },
    carrier: { type: String, required: false },
    equipmentSize: { type: String, required: false },
    equipmentType: { type: Object, required: false },
    unitNumber: { type: String, required: false },
    verified: { type: Boolean, required: false }
  },

  data: (vm) => ({

    equipmentTypeOptions: {
      equipment_owners: [],
      equipment_sizes: [],
      equipment_types: [],
      scacs: []
    },
    equipment_matches: [],
    filters: {
      display: null,
      type_and_size: null,
      type: null,
      size: null,
      owner: null,
      scac: null
    },
    isOpen: false
  }),

  computed: {

  },
  watch: {
    filters: {
      handler: function (val) {
        this.getMatchingEquipment()
      },
      deep: true
    }
  },
  async beforeMount () {
    const [error, response] = await getEquipmentTypeOptions(this.companyId, this.tmsProviderId)
    if (!error) {
      this.equipmentTypeOptions = response.data
    }
    this.getMatchingEquipment()
  },

  methods: {
    async getMatchingEquipment () {
      const apiFilters = {}

      for (const key in this.filters) {
        if (this.filters[key] === null || this.filters[key] === '') {
          continue
        }

        apiFilters[`filter[${key}]`] = this.filters[key]
      }
      const [error, response] = await getEquipmentTypes(this.companyId, this.tmsProviderId, apiFilters)
      if (!error) {
        this.equipment_matches = response.data?.data
      }
    },
    selectEquipmentType (e) {
      this.isOpen = false

      this.$emit('change', e.id)
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
