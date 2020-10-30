<template>
  <span class="chip">
    <v-chip
      v-if="visible"
      :close="closeable"
      :color="color"
      v-bind="$attrs"
      :text-color="textColor"
      :class="sizeClasses"
      @click:close="onClose"
    ><slot class="chip-content" /></v-chip>
  </span>
</template>
<script>

export default {
  name: 'Chip',
  components: {
  },
  props: {
    handleClose: {
      type: Boolean,
      required: false,
      default: false
    },
    color: {
      type: String,
      required: false,
      default: 'blue'
    },
    textColor: {
      type: String,
      required: false,
      default: 'white'
    },
    closeable: {
      type: Boolean,
      required: false,
      default: false
    },
    meta: {
      type: Object,
      required: false,
      default: null
    }
  },
  data () {
    return {
      visible: true,
      sizes: {
        'x-small': 'px-2 py-2',
        small: 'px-3',
        large: 'px-4',
        'x-large': 'px-5'
      }
    }
  },
  computed: {
    sizeClasses () {
      // design requires different paddings based on size
      const classes = []

      Object.keys(this.$attrs).forEach(prop => {
        if (this.sizes.hasOwnProperty(prop)) {
          classes.push(this.sizes[prop])
        }
      })

      return classes.join(' ')
    }
  },

  methods: {
    onClose () {
      console.log('closing')
      if (!this.handleClose) {
        this.visible = false
      }
      this.$emit('closed', this.meta)
    }
  }
}
</script>

<style lang="scss" scoped>
    // if custom styling is required
    .chip {
      display: inline-block;
    }
</style>
