<template>
  <div class="time-range">
    <v-menu
      ref="menu"
      v-model="menu"
      :close-on-content-click="false"
      :return-value.sync="dates"
      transition="scale-transition"
      offset-y
      min-width="290px"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          :value="dates.join(' - ')"
          :prepend-icon="prependIcon"
          class="calendar__input"
          :label="label"
          v-bind="[attrs, computedInputAttributes]"
          v-on="on"
          @click:clear="clear"
        />
      </template>
      <v-date-picker
        v-model="dates"
        no-title
        scrollable
        range
        color="#397E92"
        hide-details

        @change="change"
      >
        <v-spacer />
        <v-btn
          text
          color="primary"
          @click="menu = false"
        >
          Cancel
        </v-btn>
        <v-btn
          text
          color="primary"
          @click="finishDateSelection"
        >
          OK
        </v-btn>
      </v-date-picker>
    </v-menu>
  </div>
</template>

<script>
export default {
  props: {
    value: { type: Array, required: true, default: () => [] },
    label: { type: String, required: false, default: undefined },
    prependIcon: { type: String, required: false, default: 'mdi-calendar-blank' },
    inputAttributes: {
      type: Object,
      required: false,
      default: () => ({
        clearable: true,
        readonly: true,
        outlined: true,
        'hide-details': true,
        dense: true,
      })
    }
  },

  data () {
    return {
      dates: this.value,
      menu: false,
      defaultInputAttrs: {
        clearable: true,
        readonly: true,
        outlined: true,
        'hide-details': true,
        dense: true,
      }
    }
  },

  computed: {
    computedInputAttributes () {
      return {
        ...this.defaultInputAttrs,
        ...this.inputAttributes
      }
    }
  },

  watch: {
    value (newValue) {
      this.dates = newValue
    }
  },

  methods: {
    finishDateSelection () {
      if (this.dates.length === 1) {
        this.dates.push(this.dates[0])
      }
      this.dates = this.dates.sort((a, b) => Date.parse(a) - Date.parse(b))

      this.$refs.menu.save(this.dates)
      this.$emit('input', this.dates)
    },

    change (event) {
      this.$emit('change', event)
    },

    clear (event) {
      this.dates = []
      this.$emit('input', this.dates)
    }
  }
}
</script>
