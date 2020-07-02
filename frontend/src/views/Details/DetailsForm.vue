<template>
  <div :class="`form ${isMobile && 'mobile'}`">
    <div
      v-for="(sectionVal, sectionKey) in form.sections"
      :key="sectionKey"
      class="form__section"
    >
      <h1
        :id="`${cleanStrForId(sectionKey)}-${idSuffix}`"
        class="section__title"
        :style="{
          marginBottom: sectionVal.rootFields ? '2rem' : '1.6rem'
        }"
      >
        {{ sectionKey }}
      </h1>

      <div
        v-show="sectionVal.rootFields"
        class="section__rootfields"
      >
        <FormField
          v-for="(fieldVal, fieldKey) in sectionVal.rootFields"
          :key="fieldKey"
          :readonly="readonly"
          :is-editing="isEditing"
          :callbacks="fieldCallbacks"
          :field="{
            ...fieldVal,
            name: fieldKey,
            formLocation: `${sectionKey}/rootFields/${fieldKey}`
          }"
          @change="(v) => handleChange({
            v, key: fieldKey, formLocation: `${sectionKey}/rootFields/${fieldKey}`,
            cb: (formLocation) => setFormFieldProp({
              prop: 'value',
              value: v,
              formLocation
            })})"
          @close="stopEdit({
            field: {
              name: fieldKey,
              formLocation: `${sectionKey}/rootFields/${fieldKey}`
            },
          })"
        />
      </div>

      <div
        v-for="(subVal, subKey, subIndex) in sectionVal.subSections"
        :key="subKey"
        class="section__sub"
      >
        <div
          :id="`${cleanStrForId(subKey)}-${idSuffix}`"
          class="sub__title"
        >
          <h2>{{ sectionKey === 'inventory' ? `Item ${subIndex + 1}` : subKey }}</h2>

          <v-btn
            v-show="isEditing && hasInventoryAction({ sectionKey, sectionVal })"
            icon
            @click="deleteFormInventoryItem({ key: subKey })"
          >
            <v-icon color="red">
              mdi-delete
            </v-icon>
          </v-btn>
        </div>

        <FormField
          v-for="(subFieldVal, subFieldKey) in subVal.fields"
          :key="subFieldKey"
          :readonly="readonly"
          :is-editing="isEditing"
          :callbacks="fieldCallbacks"
          :field="{
            ...subFieldVal,
            name: subFieldKey,
            formLocation: `${sectionKey}/subSections/${subKey}/fields/${subFieldKey}`
          }"
          @change="(v) => handleChange({
            v, key: subFieldKey, formLocation: `${sectionKey}/subSections/${subKey}/fields/${subFieldKey}`,
            cb: (formLocation) => setFormFieldProp({
              prop: 'value',
              value: v,
              formLocation
            })})"
          @close="stopEdit({
            field: {
              name: subFieldKey,
              formLocation: `${sectionKey}/subSections/${subKey}/fields/${subFieldKey}`
            },
          })"
        />
      </div>

      <div v-show="hasInventoryAction({ sectionKey, sectionVal })">
        <DetailsFormAddInventoryItem />
      </div>
    </div>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import { mapState, mapActions } from '@/utils/vuex_mappings'

import FormField from '@/components/FormField/FormField'
import DetailsFormAddInventoryItem from '@/views/Details/DetailsFormAddInventoryItem'

import { formModule, documentModule } from '@/views/Details/inner_store/index'
import { parseFormValues, getLineItems, getAddressEvents } from '@/views/Details/inner_utils/parse_form_values'
import { cleanStrForId } from '@/views/Details/inner_utils/clean_str_for_id'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'

import orders, { types } from '@/store/modules/orders'

export default {
  name: 'DetailsForm',

  components: {
    FormField,
    DetailsFormAddInventoryItem
  },

  mixins: [isMobile],

  props: {
    readonly: {
      type: Boolean,
      required: true
    },
    idSuffix: {
      type: String,
      required: true
    }
  },

  data: () => ({
    ...mapState(orders.moduleName, {
      currentOrder: state => state.currentOrder
    }),
    fieldCallbacks: {
      startEdit: documentModule.methods.startEdit,
      startHover: documentModule.methods.startHover,
      stopHover: documentModule.methods.stopHover
    },
    lockHandleChange: undefined,
    lockTimeout: undefined
  }),

  computed: {
    isEditing () {
      return formModule.state.isEditing
    },

    form () {
      return formModule.state.form
    }
  },

  watch: {
    isEditing (val, oldVal) {
      this.lockHandleTimeout()
      this.saveFormValuesIfNeeded(val, oldVal)
    }
  },

  created () {
    this.lockHandleTimeout()
  },

  beforeUpdate () {
    this.lockHandleTimeout()
  },

  methods: {
    cleanStrForId,
    stopEdit: documentModule.methods.stopEdit,
    setFormFieldProp: formModule.methods.setFormFieldProp,
    deleteFormInventoryItem: formModule.methods.deleteFormInventoryItem,

    ...mapActions(orders.moduleName, [types.updateOrderDetail]),

    lockHandleTimeout (manual) {
      if (this.lockTimeout) {
        clearTimeout(this.lockTimeout)
        this.lockTimeout = undefined
      }

      this.lockHandleChange = true
      this.lockTimeout = setTimeout(() => {
        this.lockHandleChange = false
      }, 0)
    },

    hasInventoryAction ({ sectionKey, sectionVal }) {
      return sectionKey === 'inventory' && sectionVal.actionSection
    },

    async handleChange ({ v, cb, key, formLocation }) {
      if (this.lockHandleChange) return
      cb(formLocation)

      const changes = {}

      if (key.includes('bill to') || key.includes('port ramp')) {
        changes[`${mapFieldNames.getName({ formFieldName: key })}_raw_text`] = v
      } else if (formLocation.includes('inventory')) {
        changes.order_line_items = getLineItems(this.currentOrder())
      } else if (formLocation.includes('itinerary')) {
        changes.order_address_events = getAddressEvents(this.currentOrder())
      } else {
        changes[mapFieldNames.getName({ formFieldName: key })] = v
      }

      if (this.isEditing === false) {
        const status = await this[types.updateOrderDetail]({
          id: this.$route.params.id,
          changes
        })

        return status
      }
    },

    async saveFormValuesIfNeeded (val, oldVal) {
      if (!shouldSaveFormValues.call(this)) return

      const changes = parseFormValues({ currentOrder: this.currentOrder() })

      const status = await this[types.updateOrderDetail]({
        id: this.$route.params.id,
        changes
      })

      return status

      function shouldSaveFormValues () {
        return (val === false) &&
          (oldVal === true) &&
          (this.readonly === false)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.form {
  width: 100%;
  height: 100vh;
  overflow-y: auto;
  padding: 3.6rem 6.5rem;
  padding-top: 8.4rem;
  scroll-behavior: smooth;

  &.mobile {
    height: 50vh;
    padding-bottom: 7rem;
    padding: 1.6rem;
  }
}

.section__title {
  text-transform: uppercase;
  font-size: 1.7rem;
  line-height: 3rem;
  background: map-get($colors, grey-6);
  padding: 0 0.65rem;
  margin-bottom: 2rem;
  color: map-get($colors, grey-7);
}

.section__rootfields {
  margin-bottom: 3.6rem;
}

.section__field {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.1rem;
}

.field__name {
  font-size: 1.4rem !important;
  font-weight: bold;
  text-transform: capitalize;
}

.field__value {
  font-size: 1.44rem !important;
  text-transform: capitalize;
}

.section__sub {
  margin-bottom: 3.6rem;
}

.sub__title {
  display: flex;
  align-items: center;
  justify-content: space-between;

  h2 {
    width: 100%;
    font-size: 1.6rem;
    line-height: 3.6rem;
    color: map-get($colors, grey-4);
    border-bottom: 0.1rem solid map-get($colors, grey-3);
    margin-bottom: 1.4rem;
    text-transform: capitalize;
  }

  button {
    margin-left: auto;
  }
}
</style>
