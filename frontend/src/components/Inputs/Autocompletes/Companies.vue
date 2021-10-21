<template>
  <v-autocomplete
    :value="value"
    :items="filteredCompanies"
    :item-value="itemValue"
    :item-text="itemText"
    :label="label"
    :placeholder="placeholder"
    :loading="loading"
    :dense="dense"
    :chips="chips"
    :deletable-chips="deletableChips"
    :small-chips="smallChips"
    :menu-props="{
      transition: 'scale-transition',
      offsetY: true,
      nudgeBottom: 12,
    }"
    name="company_id"
    clearable
    multiple
    hide-details
    @change="newValue => $emit('change', newValue)"
  >
    <template v-slot:selection="{ item, index }">
      <span v-if="index === 0">{{ item.name }}</span>
      <span
        v-if="index === 1"
        class="grey--text text-caption ml-1"
      >
        {{ `(+ ${value.length -1 } others)` }}
      </span>
    </template>
  </v-autocomplete>
</template>

<script>
import allCompanies from '@/mixins/all_companies'

export default {
  name: 'Companies',

  mixins: [allCompanies],

  props: {
    value: { type: Array, required: true },
    itemValue: { type: String, required: false, default: 'id' },
    itemText: { type: String, required: false, default: 'name' },
    label: { type: String, required: false, default: 'Company' },
    placeholder: { type: String, required: false, default: 'All Companies' },
    dense: { type: Boolean, required: false, default: false },
    chips: { type: Boolean, required: false, default: false },
    smallChips: { type: Boolean, required: false, default: false },
    deletableChips: { type: Boolean, required: false, default: false },
    onboarding: { type: Boolean, required: false, default: false },
  },

  data: () => ({
    loading: false,
  }),

  computed: {
    filteredCompanies () {
      return this.onboarding
        ? this.companies
        : this.companies.filter(company => {
          return !company.name.toLowerCase().includes('onboarding') &&
            !company.name.toLowerCase().includes('demo')
        })
    },
  },

  async beforeMount () {
    this.loading = true
    await this.fetchCompanies()
    this.loading = false
  },

  methods: {
    handleFilterDeletion (item) {
      this.$emit('change', this.value.filter(s => s !== item))
    },
  }
}
</script>
