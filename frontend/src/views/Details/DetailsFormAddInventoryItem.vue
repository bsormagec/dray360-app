<template>
  <div class="details-form-add-inventory-item">
    <span class="inventory__total">
      {{ itemCount }} Item{{ itemCount > 1 ? 's' : '' }}
      ({{ hazCount }}) Hazardous.
    </span>

    <v-btn
      v-if="isEditing"
      outlined
      color="primary"
      @click="addInventoryItem"
    >
      Add Item
    </v-btn>
  </div>
</template>

<script>
import { uuid } from '@/utils/uuid_valid_id'
import { detailsState, detailsMethods } from '@/views/Details/inner_store'
import { inventoryItemFields } from '@/views/Details/inner_utils/example_form'

export default {
  name: 'DetailsFormAddInventoryItem',

  computed: {
    isEditing () {
      return detailsState.isEditing
    },

    itemCount () {
      return Object.keys(detailsState.form.sections.inventory.subSections).length
    },

    hazCount () {
      const subSections = detailsState.form.sections.inventory.subSections
      let count = 0

      for (const key in subSections) {
        if (typeof subSections[key].fields.hazardous.value === 'object') {
          count += 1
        }
      }

      return count
    }
  },

  methods: {
    addInventoryItem () {
      detailsMethods.addFormInventoryItem({
        key: uuid(),
        fields: inventoryItemFields()
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.details-form-add-inventory-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 3.2rem;
  border-top: 0.1rem solid map-get($colors , grey-3);
  margin-bottom: 3.6rem;
}

.inventory__total {
  font-size: 1.44rem !important;
  font-weight: bold;
}
</style>
