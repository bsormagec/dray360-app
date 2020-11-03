<template>
  <div class="time-range">
    <v-menu
      ref="menu"
      v-model="menu"
      :close-on-content-click="false"
      :return-value.sync="date"
      transition="scale-transition"
      offset-y
      min-width="290px"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-text-field
          :value="dateRangeText"
          prepend-icon="mdi-calendar-blank"
          class="calendar__input"
          clearable
          readonly
          outlined
          hide-details
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

  props: ['value'],
  data () {
    return {
      // dates: [],
      date: new Date().toISOString().substr(0, 10),
      menu: false
    }
  },
  computed: {
    // dates () {
    //   return this.value
    // },
    dates: {
      get () {
        return this.value
      },
      set (newRange) {
        this.$emit('input', newRange)
      }

    },
    dateRangeText () {
      return this.value.join(' - ')
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
<style lang="scss" scoped>
.range__calendar {
    //   max-width: rem(300);
    //   width: rem(300);
    //   margin-right: rem(10);
    //   height: 0;
    //   margin-bottom: rem(25);
    //   .calendar__input {
    //     max-height: rem(30) !important;
    //      .v-input__prepend-outer{
    //           margin-top: 0 !important;
    //         }
    //       .v-input__slot{
    //         min-height: rem(20) !important;
    //         .v-input__append-inner{
    //           margin-top: 0 !important;
    //           .v-input__icon > .v-icon{
    //             margin-top: rem(3);
    //           }
    //         }
    //         label{
    //           top: rem(9);
    //           height: rem(10);
    //           line-height: rem(10);
    //           font-size: rem(10);
    //           text-transform: lowercase !important;
    //         }
    //         fieldset{
    //           color: map-get($colors, grey-8 ) !important;
    //         }
    //       }
    //       .v-input__control{
    //         height: rem(30);
    //       }
    //   }
    }
</style>
