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

  // beforeMount () {
  //   this.selected = location.search.split('=')[1]

  //   if (this.selected !== undefined) {
  //     if (this.selected.includes('&')) {
  //       this.selected = this.selectedsplit('&')[0]
  //     }

  //     // this.seleted = this.statusQuery.split(',')
  //   } else {
  //     this.statusQuery = ''
  //   }
  // },

  mounted () {
    if (this.selectedItems.length > 0) {
      console.log('conditional 1')
      this.selected = this.selectedItems
      this.change(this.selected)
      return
    }

    if (typeof this.defaultItem.index === 'number') {
      console.log('conditional 2')
      this.selected = [this.items[this.defaultItem.index].value]
      this.change(this.selected)
      return
    }

    console.log('this.items: ', this.items)
    console.log('this.selected: ', this.selected)

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
  height: 2.4rem;
  width: 100%;
  max-width: 16rem;

  @media screen and (max-width: 1220px) {
    max-width: 12rem;
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
  right: 0.8rem;
  font-size: 1.6rem !important;
}

.select__label {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  left: 0;
  display: block;
  width: 100%;
  height: 100%;
  color: map-get($colors , blue);
  border: 0.1rem solid map-get($colors , blue);
  border-radius: 0.2rem;
  padding: 0.2rem 2.4rem 0.3rem 0.8rem;
  font-size: 1.3rem;
}
</style>
