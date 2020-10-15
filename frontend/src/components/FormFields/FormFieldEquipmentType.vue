<template>
  <!--  eslint-disable vue/no-v-html -->
  <div>
    <div>
      <div class="EquipmentType mb-2">
        <div class="field__name">
          SSL Container Type
        </div>

        <div>
          <div
            v-if="!verified && equipmentType === null"
            class="equipment__section mb-2"
          >
            <v-icon color="error">
              mdi-alert-outline
            </v-icon>
            <span
              class="not__found"
            >
              Not Found
            </span>
          </div>
          <div
            v-if="!verified && equipmentType !== null"
            class="equipment__section mb-2"
          >
            <v-icon color="warning">
              mdi-alert-outline
            </v-icon>
            <span
              class="not__verify"
            >
              Verification Needed
            </span>
          </div>

          <div class="selected__equipment">
            {{ equipmentType ? equipmentType.equipment_display : '---' }}
          </div>
          <v-btn
            color="primary"
            outlined
            right
            class="px-5"
            @click="toggledialg"
          >
            {{ equipmentType === null ? 'Select' : 'Select Different' }}
          </v-btn>
        </div>
      </div>
      <v-dialog
        :value="isOpen"
        width="49rem"
        scrollable
        class="equipment__dialog"
        @click:outside="toggledialg"
      >
        <v-card
          height="49rem"
        >
          <v-card-title>
            <v-row>
              <h1 class="title__dialog">
                Select SSL Container Type
              </h1><br>
            </v-row> <p>&nbsp;</p>
            <v-row
              class="center py-0 mt-5 mb-0 header__filters"
            >
              <v-col cols="2">
                <span>
                  Filter by
                </span>
              </v-col>
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
              <v-col cols="2">
                <v-autocomplete
                  v-model="filters.scac"
                  :items="equipmentTypeOptions.scacs"
                  dense
                  clearable
                  outlined
                  label="Scac"
                />
              </v-col>
              <v-col cols="3">
                <v-autocomplete
                  v-model="filters.type_and_size"
                  :items="equipmentTypeOptions.equipment_types_and_sizes"
                  dense
                  clearable
                  outlined
                  label="Type and Length"
                />
              </v-col>
              <v-col cols="1">
                <v-btn
                  text
                  color="primary"
                  @click="clearSelects"
                >
                  Clear
                </v-btn>
              </v-col>
            </v-row>
          </v-card-title>
          <v-divider />
          <v-card-text style="height: 40rem;">
            <v-data-table
              :loading="loading"
              :headers="headers"
              fixed-header
              :items="equipment_matches"
              item-key:item.id
              :hide-default-header="false"
              :hide-default-footer="false"
              scrollable
            >
              <template
                slot="item"
                slot-scope="props"
              >
                <tr>
                  <td>{{ props.item.equipment_owner }}</td>
                  <td>{{ props.item.equipment_type_and_size }}</td>
                  <td>
                    <v-btn
                      class="ma-2"
                      outlined
                      color="indigo"
                      @click="() => selectEquipmentType(props.item)"
                    >
                      Select
                    </v-btn>
                  </td>
                </tr>
              </template>
            </v-data-table>
          </v-card-text>
          <v-divider />
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
    isOpen: false,
    loading: true,
    headers: [
      { text: 'Steamship Line', value: 'equipment_owner', class: 'table__background' },
      { text: 'Length/Type', value: 'equipment_type_and_size', class: 'table__background' },
      { text: ' ', value: ' ', class: 'table__background' }
    ],
    timerId: null
  }),

  computed: {

  },
  watch: {
    filters: {
      handler: function (val) {
        clearTimeout(this.timerId)
        this.timerId = setTimeout(() => {
          this.getMatchingEquipment()
        }, 250)
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
    this.loading = false
  },

  methods: {
    toggledialg () {
      this.isOpen = !this.isOpen
    },
    clearSelects () {
      this.filters = {
        display: null,
        type_and_size: null,
        type: null,
        size: null,
        owner: null,
        scac: null
      }
    },
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

<style lang="scss">
.title__dialog{
  color: map-get($colors, slate-gray );
  font-size: rem(20);
  line-height: rem(23.44);
  letter-spacing: rem(0.15);
  font-weight: 500;
}
.equipment__section{
  font-weight: bold;
  font-size: rem(14) !important;
  letter-spacing: 0.025rem;
  .not__found{
    color: map-get($colors, red);
  }
  .not__verify{
    color: map-get($colors, warning);
  }
}
.field__name{
  font-size: 0.875rem !important;
  font-weight: bold;
  text-transform: capitalize;
}
.header__filters{
  height: rem(60);
  display: flex;
  align-items: baseline;
  span{
    color: var(--v-primary-base);
    display: flex;
    justify-content: center;
  }
}
.selected__equipment{
  display: flex;
  justify-content: flex-end;
}
.table__background{
  background-color: map-get($colors, modal-header-bg) !important;
}
.v-dialog > .v-card > .v-card__text{
  padding: 0 0 rem(20) !important;
}
.v-data-table-header th {
  background-color: #E5E5E5 !important;
}
.EquipmentType {
  display: flex;
  justify-content: space-between;
  align-items: center;
  .equipment__section{
    justify-content: flex-end;
    display: flex;
    align-items: center;
  }
}
</style>
