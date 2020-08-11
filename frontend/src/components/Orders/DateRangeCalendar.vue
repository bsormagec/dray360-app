<template>
  <div class="range__calendar">
    <v-menu
      ref="menu"
      v-model="menu"
      :close-on-content-click="false"
      :return-value.sync="date"
      transition="scale-transition"
      offset-y
      min-width="29rem"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          v-model="dateRangeText"
          label="Date Range Filled"
          append-icon="mdi-calendar-month"
          readonly
          v-bind="attrs"
          v-on="on"
        />
      </template>
      <v-date-picker
        v-model="dates"
        no-title
        scrollable
        range
        color="#397E92"
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
          @click="$refs.menu.save(date)"
        >
          OK
        </v-btn>
      </v-date-picker>
    </v-menu>
  </div>
</template>
<script>
export default {
  data () {
    return {
      dates: [],
      date: new Date().toISOString().substr(0, 10),
      menu: false
    }
  },
  computed: {
    dateRangeText () {
      return this.dates.join(' - ')
    }
  },
  methods: {
    change (event) {
      this.$emit('change', event)
    }
  }

}
</script>
<style lang="scss">
.range__calendar{
      max-width: 30rem;
      width: 30rem;
      margin-right: 1rem;
    }
</style>
