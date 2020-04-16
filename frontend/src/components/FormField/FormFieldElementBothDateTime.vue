<template>
  <div class="form-field-element-both-datetime">
    <div class="datetime__date">
      <FormFieldElementDate
        :field="field"
        @change="e => setDate(e)"
      />
    </div>

    <div class="datetime__time">
      <FormFieldElementTime
        :field="field"
        alt-label="time"
        @change="e => setTime(e)"
      />
    </div>
  </div>
</template>

<script>
import FormFieldElementDate from '@/components/FormField/FormFieldElementDate'
import FormFieldElementTime from '@/components/FormField/FormFieldElementTime'

export default {
  name: 'FormFieldElementBothDateTime',

  components: {
    FormFieldElementDate,
    FormFieldElementTime
  },

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    date: undefined,
    time: undefined
  }),

  methods: {
    setDate (newDate) {
      this.date = newDate
      this.change()
    },
    setTime (newTime) {
      this.time = newTime
      this.change()
    },
    change () {
      if (Boolean(this.date) && Boolean(this.time)) {
        this.$emit('change', {
          date: this.date,
          time: this.time
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-both-datetime {
  display: flex;
  justify-content: space-between;
  height: 6.6rem;
}

.datetime__date, .datetime__time {
  width: 48%;
}
</style>
