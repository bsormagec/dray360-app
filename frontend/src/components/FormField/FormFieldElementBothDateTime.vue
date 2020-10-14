<template>
  <div class="form-field-element-both-datetime">
    <div class="datetime__date">
      <FormFieldElementDate
        :field="field"
        :is-editing="isEditing"
        @change="e => setDate(e)"
      />
    </div>

    <div class="datetime__time">
      <FormFieldElementTime
        alt-label="time"
        :field="field"
        :is-editing="isEditing"
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
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    date: undefined,
    time: undefined
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

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
      const changes = {}
      if (this.date) changes.date = this.date
      if (this.time) changes.time = this.time
      this.$emit('change', changes)
    },

    syncValue () {
      if (!this.field.value) return
      this.date = this.field.value.date
      this.time = this.field.value.time
      this.change()
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-both-datetime {
  display: flex;
  justify-content: space-between;
  height: rem(66);
}

.datetime__date, .datetime__time {
  width: 48%;
}
</style>
