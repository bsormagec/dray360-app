<template>
  <v-autocomplete
    :value="value"
    :items="users"
    :item-value="itemValue"
    :item-text="itemText"
    :label="label"
    :placeholder="placeholder"
    :loading="loading"
    :dense="dense"
    :chips="chips"
    :deletable-chips="deletableChips"
    :small-chips="smallChips"
    name="user_id"
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
        {{ `(+ ${value.length - 1 } others)` }}
      </span>
    </template>
  </v-autocomplete>
</template>

<script>
import { getUsers } from '@/store/api_calls/users'

export default {
  name: 'Users',

  props: {
    value: { type: Array, required: true },
    sortBy: { type: String, required: false, default: 'name' },
    itemValue: { type: String, required: false, default: 'id' },
    itemText: { type: String, required: false, default: 'name' },
    label: { type: String, required: false, default: 'Users' },
    placeholder: { type: String, required: false, default: 'All Users' },
    dense: { type: Boolean, required: false, default: false },
    chips: { type: Boolean, required: false, default: false },
    smallChips: { type: Boolean, required: false, default: false },
    deletableChips: { type: Boolean, required: false, default: false },
  },

  data: () => ({
    loading: false,
    users: [],
  }),

  beforeMount () {
    this.fetchUsers()
  },

  methods: {
    async fetchUsers () {
      this.loading = true
      const [error, data] = await getUsers({
        page: 1,
        sort: this.sortBy,
        perPage: 1000
      })

      if (error === undefined) {
        this.users = data.data
      }

      this.loading = false
    },
  }
}
</script>
