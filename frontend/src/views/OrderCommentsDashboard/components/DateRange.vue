<template>
  <v-menu
    ref="menu"
    v-model="menu"
    :close-on-content-click="false"
    :return-value.sync="dates"
    transition="scale-transition"
    offset-y
    nudge-bottom="12"
  >
    <template v-slot:activator="{on, attrs}">
      <v-text-field
        label="Date Range"
        append-icon="mdi-calendar-blank"
        readonly
        hide-details
        :value="dateToDisplay.join(' - ')"
        v-bind="attrs"
        v-on="on"
      >
        <template v-slot:append>
          <v-icon
            color="primary"
          >
            mdi-calendar-blank
          </v-icon>
        </template>
      </v-text-field>
    </template>
    <v-date-picker
      v-model="dates"
      no-title
      range
      color="primary"
      @change="change"
    />
  </v-menu>
</template>

<script>
import format from 'date-fns/format'

export default {
  name: 'DateRange',

  props: {
    value: { type: Array, required: true, default: () => [] },
  },

  data: (vm) => ({
    menu: false,
    dates: vm.value
  }),

  computed: {
    dateToDisplay () {
      return this.dates.map(date => format(
        new Date(date.replace(/-/g, '/')),
        'MM/dd/yyyy')
      )
    }
  },

  watch: {
    value (newValue) {
      this.dates = newValue
    }
  },

  methods: {
    change (event) {
      this.dates = this.dates.sort((a, b) => Date.parse(a) - Date.parse(b))
      this.$refs.menu.save(this.dates)
      this.$emit('input', this.dates)
      this.$emit('change', event)
    }
  }
}
</script>
