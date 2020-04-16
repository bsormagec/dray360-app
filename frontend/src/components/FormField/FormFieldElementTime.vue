/*
  TODO: Timepicker mask but is an input
*/

<template>
  <div class="form-field-element-time">
    <v-dialog
      v-model="modal2"
      width="290px"
    >
      <template v-slot:activator="{ on }">
        <v-text-field
          v-model="time"
          label="Picker in dialog"
          prepend-icon="mdi-clock-outline"
          readonly
          v-on="on"
        />
      </template>

      <v-time-picker
        v-if="modal2"
        v-model="time"
        full-width
        @input="modal2 = false"
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
    }
  },

  data: () => ({
    modal2: false,
    time: undefined
  })
}
</script>
