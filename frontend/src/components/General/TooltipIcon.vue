<template>
  <v-tooltip
    bottom
    :max-width="maxWidth"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-icon
        v-bind="[attrs, computedIconAttrs]"
        v-on="on"
      >
        {{ icon }}
      </v-icon>
    </template>
    <span>{{ text }}</span>
  </v-tooltip>
</template>

<script>
export default ({
  name: 'TooltipIcon',

  props: {
    text: { type: String, required: true, default: '' },
    icon: { type: String, required: false, default: 'mdi-information' },
    customIconAttrs: { type: Object, required: false, default: () => ({}) },
    maxWidth: { type: String, required: false, default: 'auto' },
  },

  data: () => ({
    defaultIcontAttrs: {
      small: true,
      color: 'primary'
    },
  }),

  computed: {
    computedIconAttrs () {
      return {
        ...this.defaultIcontAttrs,
        ...this.customIconAttrs,
      }
    }
  },
})
</script>
