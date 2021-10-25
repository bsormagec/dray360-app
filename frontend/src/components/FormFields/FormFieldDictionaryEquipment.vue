<template>
  <!--  eslint-disable vue/no-v-html -->
  <FormFieldPresentation
    :references="references"
    value=""
    label=""
    :edit-mode="false"
    only-hover
    :managed-by-template="managedByTemplate"
    :admin-notes="adminNotes"
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
            {{ equipmentType ? equipmentType.item_display_name : '---' }}
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
          :loading="isLoading"
          @click="toggleDialog"
        >
          {{ equipmentType === null ? 'Select' : 'Select Different' }}
        </v-btn>
      </div>
      <v-dialog
        :value="isOpen"
        max-width="56rem"
        scrollable
        @click:outside="toggleDialog"
        @keydown.esc="toggleDialog"
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
                  @click="toggleDialog"
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
                        v-model="filters.line"
                        :items="getOptionsFor('line')"
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
                        :items="getOptionsFor('scac')"
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
                        v-model="filters.lineprefix"
                        :items="getOptionsFor('lineprefix')"
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
                        :items="getOptionsFor('type')"
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
                        v-model="filters.equipmentlength"
                        :items="getOptionsFor('equipmentlength')"
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
                    @click="resetFilters"
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
              :headers="[
                { text: 'Steamship Line', value: 'item_value.line' },
                { text: 'Length/Type', value: 'item_value.type' },
                { text: ' ', value: 'action' }
              ]"
              fixed-header
              :items="fitleredDictionaryItems"
              item-key:item.id
              :hide-default-header="false"
              :hide-default-footer="false"
              scrollable
            >
              <template v-slot:[`item.action`]="{ item }">
                <v-btn
                  :class="{
                    'ma-1': hasPermission('all-orders-edit'),
                    'ma-2': !hasPermission('all-orders-edit')
                  }"
                  outlined
                  color="primary"
                  @click="() => selectEquipmentType(item)"
                >
                  Select
                </v-btn>
                <v-btn
                  v-if="isMultiOrderRequest && hasPermission('all-orders-edit')"
                  class="ma-1"
                  outlined
                  color="primary"
                  @click="() => selectEquipmentType(item, true)"
                >
                  Select For All
                </v-btn>
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

import { getDictionaryItems } from '@/store/api_calls/dictionary_items'
import { mapState, mapGetters } from 'vuex'
import orderForm from '@/store/modules/order-form'
import permissions from '@/mixins/permissions'
import { dictionaryItemsTypes } from '@/enums/app_objects_types'

import uniq from 'lodash/uniq'
import flatten from 'lodash/flatten'

export default {
  name: 'FormFieldDictionaryEquipment',

  components: { FormFieldPresentation },

  mixins: [permissions],

  props: {
    companyId: { type: Number, required: true },
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
    adminNotes: { type: String, required: false, default: '' },
  },

  data: () => ({
    filters: {
      line: null,
      scac: null,
      lineprefix: null,
      type: null,
      equipmentlength: null,
    },
    dictionaryItems: [],
    isOpen: false,
    loading: true,
  }),

  computed: {
    ...mapGetters(orderForm.moduleName, ['isMultiOrderRequest', 'isLocked']),

    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights
    }),

    concatenatedRecognizedText () {
      const scac = (this.unitNumber || '').substring(0, 4)
      const string = `${this.carrier || ''} ${this.equipmentSize || ''} ${this.recognizedText || ''} ${scac}`

      return string.trim() === '' ? '--' : string.trim()
    },

    isLoading () {
      return this.allHighlights[this.references]?.loading || false
    },

    fitleredDictionaryItems () {
      return this.dictionaryItems.filter(item => {
        let pass = true
        for (const key in this.filters) {
          if (!this.filters[key]) {
            continue
          }

          if (key === 'lineprefix') {
            pass = item.item_value[key].includes(this.filters[key])
            continue
          }

          if (item.item_value[key] !== this.filters[key]) {
            pass = false
          }
        }

        return pass
      })
    }
  },

  methods: {
    async fetchDictionaryItems () {
      this.loading = true
      const [error, response] = await getDictionaryItems({
        'filter[item_type]': dictionaryItemsTypes.ptEquipmenttype,
        'filter[company_id]': this.companyId,
      })

      if (!error) {
        this.dictionaryItems = response.data
      }
      this.loading = false
    },

    getOptionsFor (key) {
      return uniq(
        flatten(this.dictionaryItems.map(item => item.item_value[key]))
      )
        .filter(item => !!item)
        .sort()
    },

    async toggleDialog () {
      this.isOpen = !this.isOpen
      if (this.dictionaryItems.length === 0 && this.isOpen) {
        await this.fetchDictionaryItems()
      }
    },

    resetFilters () {
      this.filters = {
        line: null,
        scac: null,
        lineprefix: null,
        type: null,
        equipmentlength: null,
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
