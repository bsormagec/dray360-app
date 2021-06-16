<template>
  <v-menu
    v-model="menu"
    buttom
    left
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    nudge-bottom="12"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        icon
        color="primary"
        small
        v-bind="attrs"
        v-on="on"
      >
        <v-icon dense>
          {{ icon }}
        </v-icon>
      </v-btn>
    </template>
    <v-list
      subheader
      dense
    >
      <v-subheader v-if="title.length !== 0">
        {{ title }}
      </v-subheader>
      <v-list-item-group
        :value="activeOptions"
        multiple
        @change="change"
      >
        <template v-for="(option, i) in options">
          <v-list-item
            :key="`option-${i}`"
            :value="option.value"
          >
            <template v-slot:default="{ active }">
              <v-list-item-action class="mr-2">
                <v-checkbox
                  :input-value="active"
                />
              </v-list-item-action>
              <v-list-item-content
                class="text-left"
                v-text="option.name"
              />
            </template>
          </v-list-item>
        </template>
      </v-list-item-group>
    </v-list>
  </v-menu>
</template>

<script>
export default ({
  name: 'OptionList',

  props: {
    options: {
      type: Array,
      required: true,
      default: () => ([])
    },
    icon: {
      type: String,
      required: true,
      default: 'mdi-information'
    },
    title: {
      type: String,
      required: false,
      default: ''
    },
    value: {
      type: Array,
      required: true,
      default: () => []
    },
  },

  data: (vm) => ({
    menu: false,
    activeOptions: vm.value
  }),

  watch: {
    value (newValue) {
      this.activeOptions = newValue
    }
  },

  methods: {
    change (currentSelection) {
      this.activeOptions = currentSelection
      this.$emit('input', currentSelection)
      this.$emit('change', currentSelection)
    }
  }
})
</script>
