<template>
  <div class="form-field-element-datetime">
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
          v-model="date"
          :label="field.name"
          prepend-icon="mdi-calendar"
          readonly
          v-on="on"
        />
      </template>

      <v-date-picker
        v-model="date"
        no-title
        scrollable
        @input="isOpen = false"
      />

      <!--
        TODO: Add date time
      -->
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementDateTime',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    date: new Date().toISOString().substr(0, 10),
    isOpen: false
  })
}
</script>
