<template>
  <ContentLoading
    class="field-mapping__list-container px-3"
    :loaded="!loading"
  >
    <v-list
      nav
      class="px-0 pt-0"
    >
      <v-list-item-group
        :value="selectedField"
        @change="newValue => $emit('change', newValue)"
      >
        <template v-for="(fieldMap, key) in fieldMaps">
          <v-list-item
            :key="key"
            :value="key"
            :class="{
              'field-mapping__item': true,
              'field-mapping__item-changed': selectedField !== key && hasChanged(key),
            }"
            active-class="primary white--text"
            :ripple="false"
            outlined
          >
            <template v-slot:default="{active}">
              <v-list-item-content>
                <v-list-item-title v-text="key" />
                <v-list-item-subtitle
                  :class="{'white--text':active}"
                  v-text="''"
                />
              </v-list-item-content>

              <v-list-item-action class="d-flex flex-row align-center justify-end">
                <span
                  v-show="hasChanged(key)"
                  :class="{caption: true, 'white--text':active}"
                >
                  Edited
                </span>
                <v-icon small>
                  mdi-chevron-right
                </v-icon>
              </v-list-item-action>
            </template>
          </v-list-item>
        </template>
      </v-list-item-group>
    </v-list>
  </ContentLoading>
</template>

<script>
import ContentLoading from '@/components/ContentLoading'

import { mapActions, mapGetters } from 'vuex'
import fieldMaps, { types as fieldMapsTypes } from '@/store/modules/field_maps'

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
  },

  data: () => ({

  }),

  computed: {
    ...mapGetters(fieldMaps.moduleName, {
      fieldMaps: 'sortedFieldMaps',
      fieldMapsChanges: 'fieldMapsChanges',
    }),
  },

  methods: {
    ...mapActions(fieldMaps.moduleName, {
      getFieldMaps: fieldMapsTypes.GET_FIELD_MAPS,
      setFieldMap: fieldMapsTypes.SET_FIELD_MAP,
      resetFieldMap: fieldMapsTypes.RESET_FIELD_MAP,
    }),

    hasChanged (key) {
      return this.fieldMapsChanges.filter(change => {
        return change.path.includes(key)
      }).length !== 0
    },

  }
}
</script>

<style lang="scss" scoped>
.field-mapping__list-container {
  height: 100%;
  max-height: calc(100vh - #{rem(152)});
  overflow-y: auto;
}

.field-mapping__item {
  box-shadow: 0 3px 1px -2px rgb(0 0 0 / 5%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
}

.field-mapping__item-changed {
  background-color: rgba(var(--v-orange-changes-base-rgb), 0.1);
}
</style>
