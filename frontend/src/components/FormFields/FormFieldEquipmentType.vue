<template>
  <!--  eslint-disable vue/no-v-html -->
  <FormFieldPresentation
    :references="references"
    value=""
    label=""
    :edit-mode="false"
    only-hover
    :managed-by-template="managedByTemplate"
  >
    <div class="pa-2">
      <div class="d-flex align-center">
        <div class="form-field__label">
          {{ label }}
        </div>

        <div class="ml-auto text-right">
          <div v-if="!verified && equipmentType === null">
            <v-icon color="error">
              mdi-alert-outline
            </v-icon>
            <span
              class="verification__status error--text"
            >
              Not Found
            </span>
          </div>
          <div v-if="!verified && equipmentType !== null">
            <v-icon color="warning">
              mdi-alert-outline
            </v-icon>
            <span
              class="verification__status warning--text"
            >
              Verification Needed
            </span>
          </div>

          <div class="equipment-type__value">
            {{ equipmentType ? equipmentType.equipment_display : '---' }}
          </div>
        </div>
      </div>
      <div class="d-flex w-full justify-end my-2">
        <v-btn
          v-if="!verified && equipmentType !== null"
          :disabled="isLocked || readonly"
          color="primary"
          class="mr-2"
          outlined
          small
          :loading="isLoading"
          @click="() => selectEquipmentType(equipmentType)"
        >
          Verify Closest Match
        </v-btn>
        <v-btn
          color="primary"
          :disabled="isLocked || readonly"
          outlined
          small
          @click="toggledialg"
        >
          {{ equipmentType === null ? 'Select' : 'Select Different' }}
        </v-btn>
      </div>
      <v-dialog
        :value="isOpen"
        max-width="56rem"
        scrollable
        @click:outside="toggledialg"
        @keydown.esc="toggledialg"
      >
        <v-card>
          <v-card-title class="pa-0">
            <div class="d-flex flex-column">
              <v-row
                align="center"
                no-gutters
                class="px-8 py-4"
              >
                <h5 class="secondary--text">
                  Select SSL Container Type
                </h5>
                <v-spacer />
                <v-btn
                  text
                  icon
                  color="primary"
                  @click="toggledialg"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-row>
              <v-divider />
              <v-row
                align="center"
                no-gutters
                class="px-8 py-4"
              >
                <v-col
                  class="subtitle-1"
                  cols="1"
                >
                  Filter by
                </v-col>
                <v-col cols="8">
                  <v-row dense>
                    <v-col cols="4">
                      <v-autocomplete
                        v-model="filters.owner"
                        :items="equipmentTypeOptions.equipment_owners"
                        outlined
                        dense
                        clearable
                        hide-details
                        placeholder=" "
                        label="Steamship Line"
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-autocomplete
                        v-model="filters.scac"
                        :items="equipmentTypeOptions.scacs"
                        dense
                        clearable
                        outlined
                        hide-details
                        placeholder=" "
                        label="Scac"
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-autocomplete
                        v-model="filters.prefix"
                        :items="equipmentTypeOptions.prefix_list"
                        dense
                        clearable
                        outlined
                        hide-details
                        placeholder=" "
                        label="Prefix"
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-autocomplete
                        v-model="filters.type"
                        :items="equipmentTypeOptions.equipment_types"
                        dense
                        clearable
                        hide-details
                        outlined
                        placeholder=" "
                        label="Type"
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-autocomplete
                        v-model="filters.size"
                        :items="equipmentTypeOptions.equipment_sizes"
                        dense
                        clearable
                        outlined
                        hide-details
                        placeholder=" "
                        label="Size"
                      />
                    </v-col>
                  </v-row>
                </v-col>
                <v-col
                  cols="2"
                  class="ml-auto"
                >
                  <v-btn
                    text
                    color="primary"
                    @click="clearSelects"
                  >
                    Clear
                  </v-btn>
                </v-col>
              </v-row>
            </div>
          </v-card-title>
          <v-divider />
          <v-card-text
            style="height: 50vh;"
            class="pa-0"
          >
            <div class="recognized-equipment">
              <span class="d-flex align-center secondary--text font-weight-bold body-2 mr-16">Equipment as recognized</span>
              <span class="d-flex align-center secondary--text ml-16">{{ concatenatedRecognizedText }}</span>
            </div>
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
                      :class="{
                        'ma-1': hasPermission('all-orders-edit'),
                        'ma-2': !hasPermission('all-orders-edit')
                      }"
                      outlined
                      color="primary"
                      @click="() => selectEquipmentType(props.item)"
                    >
                      Select
                    </v-btn>
                    <v-btn
                      v-if="isMultiOrderRequest && hasPermission('all-orders-edit')"
                      class="ma-1"
                      outlined
                      color="primary"
                      @click="() => selectEquipmentType(props.item, true)"
                    >
                      Select For All
                    </v-btn>
                  </td>
                </tr>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </FormFieldPresentation>
</template>

<script>
/* eslint-disable vue/no-v-html */

import FormFieldPresentation from './FormFieldPresentation'

import { getEquipmentTypeOptions, getEquipmentTypes } from '@/store/api_calls/equipment'
import { mapState, mapGetters } from 'vuex'
import orderForm from '@/store/modules/order-form'
import permissions from '@/mixins/permissions'

export default {
  name: 'FormFieldEquipmentType',

  components: { FormFieldPresentation },

  mixins: [permissions],

  props: {
    companyId: { type: Number, required: true },
    tmsProviderId: { type: Number, required: true },
    carrier: { type: String, required: false, default: '' },
    label: { type: String, required: false, default: 'SSL Container Type' },
    equipmentSize: { type: String, required: false, default: '' },
    equipmentType: { type: Object, required: false, default: () => ({}) },
    unitNumber: { type: String, required: false, default: '' },
    recognizedText: { type: String, required: false, default: '--' },
    verified: { type: Boolean, required: false, default: false },
    references: { type: String, default: null },
    managedByTemplate: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
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
      scac: null,
      prefix: null
    },
    isOpen: false,
    loading: true,
    headers: [
      { text: 'Steamship Line', value: 'equipment_owner' },
      { text: 'Length/Type', value: 'equipment_type_and_size' },
      { text: ' ', value: ' ' }
    ],
    timerId: null
  }),

  computed: {
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),
    concatenatedRecognizedText () {
      const scac = (this.unitNumber || '').substring(0, 4)
      const string = `${this.carrier || ''} ${this.equipmentSize || ''} ${this.recognizedText || ''} ${scac}`

      return string.trim() === '' ? '--' : string.trim()
    },
    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights
    }),
    isLoading () {
      return this.allHighlights[this.references]?.loading || false
    }
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
        scac: null,
        prefix: null
      }
    },
    async getMatchingEquipment () {
      this.loading = true
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
        this.loading = false
      }
    },
    selectEquipmentType (e, saveAll = false) {
      this.isOpen = false
      this.$emit('change', { value: e.id, saveAll })
    }
  }
}
</script>

<style lang="scss" scoped>
.verification__status {
    padding-left: rem(6);
    font-weight: 700;
    font-size: rem(14.4) !important;
}

.equipment-type__value {
  font-size: rem(14);
}

.recognized-equipment {
  display: flex;
  padding: rem(16) rem(32);
  margin: 0;
  background-color: rgba(var(--v-primary-base-rgb), 0.05);
  &::before{
    content: '';
    display: block;
    height: rem(48);
    width: rem(5);
    margin-right: rem(8);
    background-color: rgba(var(--v-secondary-base-rgb), 0.25);
  }
}
</style>
