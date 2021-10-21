<template>
  <v-autocomplete
    :value="value"
    :items="variants"
    :item-value="itemValue"
    :item-text="itemText"
    :label="label"
    :loading="loading"
    :dense="dense"
    :chips="chips"
    :deletable-chips="deletableChips"
    :small-chips="smallChips"
    name="abbyy_variant_id"
    clearable
    multiple
    hide-details
    @change="newValue => $emit('change', newValue)"
  >
    <template v-slot:selection="{ item, index }">
      <v-chip
        v-if="index === 0"
        close
        small
        @click:close="handleFilterDeletion(item.abbyy_variant_id)"
      >
        <span>{{ item.abbyy_variant_id }}</span>
      </v-chip>
      <span
        v-if="index === 1"
        class="grey--text caption"
      >
        (+{{ value.length - 1 }} others)
      </span>
    </template>
  </v-autocomplete>
</template>

<script>
import { getVariants } from '@/store/api_calls/rules_editor'

export default {
  name: 'Variants',

  props: {
    value: { type: Array, required: true },
    sortBy: { type: String, required: false, default: 'abbyy_variant_id' },
    itemValue: { type: String, required: false, default: 'abbyy_variant_id' },
    itemText: { type: String, required: false, default: 'abbyy_variant_id' },
    label: { type: String, required: false, default: 'Variant Id' },
    dense: { type: Boolean, required: false, default: false },
    chips: { type: Boolean, required: false, default: false },
    smallChips: { type: Boolean, required: false, default: false },
    deletableChips: { type: Boolean, required: false, default: false },
  },

  data: () => ({
    loading: false,
    variants: [],
  }),

  beforeMount () {
    this.fetchVariants()
  },

  methods: {
    handleFilterDeletion (item) {
      this.$emit('change', this.value.filter(s => s !== item))
    },

    async fetchVariants () {
      this.loading = true
      const [error, data] = await getVariants({
        'fields[t_ocrvariants]': 'abbyy_variant_id,abbyy_variant_name',
        sort: this.sortBy,
      })
      this.loading = false

      if (error !== undefined) {
        return
      }

      this.variants = data
    },
  }
}
</script>
