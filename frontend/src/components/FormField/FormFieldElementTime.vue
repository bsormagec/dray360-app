<template>
  <div class="form-field-element-date">
    <v-dialog
      v-model="isOpen"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      width="290px"
    >
      <template v-slot:activator="{ on }">
        <v-text-field
          v-model="time"
          :label="field.name"
          prepend-icon="mdi-calendar"
          readonly
          v-on="on"
        />
      </template>
      <v-time-picker
        v-model="time"
        @input="isOpen = false"
        @change="e => $emit('change', e)"
      />
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementTime',

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
    time: undefined,
    isOpen: false
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  methods: {
    syncValue () {
      if (typeof this.field.value === 'object') {
        this.time = this.field.value.time
        return
      }

      this.time = this.field.value
    }
  }
}
</script>
