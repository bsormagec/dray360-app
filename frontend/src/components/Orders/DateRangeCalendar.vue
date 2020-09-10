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
          :value="dateRangeText"
          label="YYYY-MM-DD — YYYY-MM-DD"
          prepend-icon="mdi-calendar-month"
          class="calendar__input"
          clearable
          readonly
          outlined
          dense
          v-bind="attrs"
          v-on="on"
          @click:clear="click"
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
    },
    click (event) {
      this.$emit('click:clear', event)
    }
  }

}
</script>
<style lang="scss">
.range__calendar{
      max-width: 30rem;
      width: 30rem;
      margin-right: 1rem;
      height: 0rem;
      margin-bottom: 2.5rem;
      .calendar__input {
        max-height: 3rem !important;
         .v-input__prepend-outer{
              margin-top: 0rem !important;
            }
          .v-input__slot{
            min-height: 2rem !important;
            .v-input__append-inner{
              margin-top: 0rem !important;
              .v-input__icon > .v-icon{
                margin-top: 0.3rem !important;
              }
            }
            label{
              top: 0.9rem !important;
              height: 1rem !important;
              line-height: 1rem !important;
              font-size: 1rem !important;
              text-transform: lowercase !important;
            }
            fieldset{
              color: map-get($colors, grey-8 ) !important;
            }
          }
          .v-input__control{
            height: 3rem;
          }
      }
    }
</style>
