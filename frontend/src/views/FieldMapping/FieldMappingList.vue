<template>
  <ContentLoading
    class="field-mapping__list-container px-3"
    :loaded="!loading"
  >
    <v-list
      nav
      class="px-0 pt-0"
    >
      <template v-for="(fieldMap, key) in computedFieldMaps">
        <v-list-item
          :key="key"
          :value="key"
          :class="{
            'field-mapping__item': true,
            'field-mapping__item-changed': selectedField !== key && hasChanged(key),
            'field-mapping__item-active': selectedField === key,
          }"
          :ripple="false"
          outlined
          @click="event => handleFieldChange(event, key)"
        >
          <template v-slot:default>
            <v-list-item-content>
              <v-list-item-title
                :class="{'white--text': selectedField === key}"
                v-text="key"
              />
              <v-list-item-subtitle
                :class="{'white--text': selectedField === key}"
                v-text="''"
              />
            </v-list-item-content>

            <v-list-item-action class="d-flex flex-row align-center justify-end">
              <span
                v-show="hasChanged(key)"
                :class="{caption: true, 'white--text': selectedField === key}"
              >
                Edited
              </span>
              <v-icon
                small
                :class="{'white--text': selectedField === key}"
              >
                mdi-chevron-right
              </v-icon>
            </v-list-item-action>
          </template>
        </v-list-item>
      </template>
    </v-list>
  </ContentLoading>
</template>

<script>
import ContentLoading from '@/components/ContentLoading'

import { mapActions, mapGetters } from 'vuex'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import fieldMaps, { actionTypes as fieldMapsActionTypes } from '@/store/modules/field_maps'

export default {
  name: 'FieldMappingList',

  components: { ContentLoading },

  props: {
    selectedField: {
      type: String,
      required: false,
      default: null,
    },
    loading: {
      type: Boolean,
      required: true,
      default: false,
    },
    formChanged: {
      type: Boolean,
      required: true,
      default: false,
    },
    query: {
      type: String,
      require: true,
      default: ''
    }
  },

  data: () => ({
  }),

  computed: {
    ...mapGetters(fieldMaps.moduleName, {
      fieldMaps: 'sortedFieldMaps',
      fieldMapsChanges: 'fieldMapsChanges',
    }),

    computedFieldMaps () {
      const queryString = (this.query || '').toLowerCase().trim()
      if (queryString !== '') {
        return Object.keys(this.fieldMaps)
          .filter(k => k.indexOf(queryString) >= 0)
          .reduce((o, k) => ({ ...o, [k]: this.fieldMaps[k] }), {})
      } else {
        return this.fieldMaps
      }
    }
  },

  methods: {
    ...mapActions(fieldMaps.moduleName, [
      fieldMapsActionTypes.getFieldMaps,
      fieldMapsActionTypes.setFieldMap,
      fieldMapsActionTypes.resetFieldMap,
    ]),

    ...mapActions(utils.moduleName, [
      utilsActionTypes.setConfirmationDialog,
    ]),

    hasChanged (key) {
      return this.fieldMapsChanges.filter(change => {
        return change.path.includes(key)
      }).length !== 0
    },

    handleFieldChange (event, key) {
      if (this.formChanged) {
        event.preventDefault()
        this.setConfirmationDialog({
          title: 'Unsaved changes detected',
          text: 'Are you sure you want to leave this page without saving? Your changes will be lost.',
          onConfirm: () => {
            this.$emit('change', key)
          },
          onCancel: () => {
            this.$emit('change', this.selectedField)
          }
        })
        return
      }
      this.$emit('change', key)
    }
  }
}
</script>

<style lang="scss" scoped>
.field-mapping__list-container {
  height: 100%;
  max-height: calc(100vh - #{rem(205)});
  overflow-y: auto;
}

.field-mapping__item {
  box-shadow: 0 3px 1px -2px rgb(0 0 0 / 5%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
}

.field-mapping__item-changed {
  background-color: rgba(var(--v-orange-changes-base-rgb), 0.1);
}

.field-mapping__item-active {
  background-color: var(--v-primary-base);
  color: white;
  transition: all ease 300ms;
}
</style>
