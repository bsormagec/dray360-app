<template>
  <FormFieldInputAutocomplete
    :references="references"
    :label="label"
    :value="value"
    :autocomplete-items="autocompleteItems"
    :item-value="itemValue"
    :item-text="itemText"
    :edit-mode="editMode"
    :display-value="displayValue"
    :managed-by-template="managedByTemplate"
    :readonly="readonly"
    :admin-notes="adminNotes"
    @change="event => $emit('change', event)"
  />
</template>

<script>
import FormFieldInputAutocomplete from './FormFieldInputAutocomplete'

import { getDictionaryItems } from '@/store/api_calls/dictionary_items'

export default {
  name: 'FormFieldDictionaryItem',

  components: {
    FormFieldInputAutocomplete
  },

  props: {
    references: { type: String, default: null },
    label: { type: String, required: true },
    value: { required: true, default: '' },
    itemValue: { type: String, required: false, default: 'id' },
    itemText: { required: false, default: 'name' },
    itemType: { type: String, required: true },
    companyId: { type: Number, required: false, default: null },
    tmsProviderId: { type: Number, required: false, default: null },
    editMode: { type: Boolean, required: true },
    displayKeyValue: { type: Boolean, required: false, default: false },
    managedByTemplate: { type: Boolean, required: false, default: false },
    readonly: { type: Boolean, required: false, default: false },
    adminNotes: { type: String, required: false, default: '' },
  },

  data: () => ({
    autocompleteItems: []
  }),

  beforeMount () {
    this.fetchDictionaryItems()
  },

  methods: {
    async fetchDictionaryItems () {
      const [error, data] = await getDictionaryItems({
        'filter[company_id]': this.companyId,
        'filter[tms_provider_id]': this.tmsProviderId,
        'filter[item_type]': this.itemType
      })

      if (error !== undefined) {
        this.autocompleteItems = []
      }

      this.autocompleteItems = data.data
    },

    displayValue (value) {
      const result = this.autocompleteItems.filter(el => el.id === value)

      if (result.length > 0 && this.displayKeyValue) {
        return `${result[0].item_display_name} (${result[0].item_key})`
      }

      if (result.length > 0 && !this.displayKeyValue) {
        return result[0].item_display_name
      }

      return value
    },
  }
}
</script>
