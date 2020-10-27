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
import { formModule } from '@/views/Details/inner_store/index'
import { inventoryItemFields } from '@/views/Details/inner_utils/example_form'

export default {
  name: 'DetailsFormAddInventoryItem',

  computed: {
    isEditing () {
      return formModule.state.isEditing
    },

    itemCount () {
      return Object.keys(formModule.state.form.sections.inventory.subSections).length
    },

    hazCount () {
      const subSections = formModule.state.form.sections.inventory.subSections
      let count = 0

      for (const key in subSections) {
        if (subSections[key].fields.hazardous && typeof subSections[key].fields.hazardous.value === 'object') {
          count += 1
        }
      }

      return count
    }
  },

  methods: {
    addInventoryItem () {
      formModule.methods.addFormInventoryItem({
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
  padding-top: rem(32);
  border-top: rem(1) solid map-get($colors , grey-3);
  margin-bottom: rem(36);
}

.inventory__total {
  font-size: rem(14.4) !important;
  font-weight: bold;
}
</style>
