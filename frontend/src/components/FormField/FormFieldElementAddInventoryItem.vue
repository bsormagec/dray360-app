<template>
  <div class="form-field-element-add-inventory-item">
    <span class="inventory__total">
      {{ field.value.inventoryItemsCount }}
      Item{{ field.value.inventoryItemsCount.length > 1 ? 's' : '' }}
      ({{ field.value.hazardousCount }} Hazardous).
    </span>
    <v-btn
      outlined
      color="primary"
    >
      Add Item
    </v-btn>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementAddInventoryItem',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  beforeMount () {
    this.$emit('change', {
      type: 'inventory-item-initialize'
    })
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-add-inventory-item {
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
