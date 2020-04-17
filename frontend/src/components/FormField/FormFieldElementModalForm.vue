/*
  TODO:
    - rename because this is not a form but a type of select
    - add global addresses array as inner_utils in Details
    - use those addresses in exampleForm
    - convert field objects to fns (bit refactor to make it shorter and prettier)
*/

<template>
  <div class="form-field-element-modal-form">
    <v-dialog
      v-model="isOpen"
      :close-on-content-click="false"
      :nudge-right="40"
      transition="scale-transition"
      offset-y
      width="290px"
    >
      <template v-slot:activator="{ on }">
        <p
          class="modal-form__link"
          @click="toggleModal"
          v-on="on"
        >
          <strong>{{ field.el.placeholder }}</strong>: {{ field.name }}
        </p>
      </template>

      <v-card>hello</v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementModalForm',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    isOpen: false
  }),

  methods: {
    toggleModal () {
      this.isOpen = !this.isOpen
    }
  }
}
</script>

<style lang="scss" scoped>
.modal-form__link {
  cursor: pointer;
  font-size: 1.44rem !important;
  text-decoration: underline;
  text-transform: capitalize;
  color: map-get($colors , blue);
  margin-bottom: 1rem;
}
</style>
