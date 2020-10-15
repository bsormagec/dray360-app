<template>
  <div :class="`select ${label}`">
    <v-select
      v-model="selected"
      :items="items"
      solo
      dense
      multiple
      persistent-hint
      @change="e => $emit('change', e)"
    />

    <v-icon color="primary">
      mdi-chevron-down
    </v-icon>

    <span class="select__label">
      {{ label }}
    </span>
  </div>
</template>

<script>
export default {
  name: 'Select',

  props: {
    label: {
      type: String,
      required: true
    },
    items: {
      type: Array,
      required: true
    },
    defaultItem: {
      type: Object,
      required: false,
      default: () => ({})
    },
    selectedItems: {
      type: Array,
      required: false,
      default: () => ([])
    }
  },

  data: () => ({
    selected: []
  }),

  mounted () {
    if (this.selectedItems.length > 0) {
      this.selected = this.selectedItems
      this.change(this.selected)
      return
    }

    if (typeof this.defaultItem.index === 'number') {
      this.selected = [this.items[this.defaultItem.index].value]
      this.change(this.selected)
      return
    }

    this.selected = this.items
    this.change(this.selected)
  },

  methods: {
    change (e) {
      this.$emit('change', e)
    }
  }
}
</script>

<style lang="scss" scoped>
.select {
  position: relative;
  height: rem(24);
  width: 100%;
  max-width: rem(160);

  @media screen and (max-width: 1220px) {
    max-width: rem(120);
  }

  .v-input {
    opacity: 0;
    z-index: 2;
  }
}

i {
  position: absolute !important;
  top: 50%;
  transform: translateY(-50%);
  right: rem(8);
  font-size: rem(16) !important;
}

.select__label {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  left: 0;
  display: block;
  width: 100%;
  height: 100%;
  color: var(--v-primary-base);
  border: rem(1) solid var(--v-primary-base);
  border-radius: rem(2);
  padding: rem(2) rem(24) rem(3) rem(8);
  font-size: rem(13);
}
</style>
