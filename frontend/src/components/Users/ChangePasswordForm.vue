<template>
  <div>
    <template>
      <v-toolbar
        flat
        color="white"
      >
        <v-toolbar-title><h1>Change Password</h1></v-toolbar-title>

        <v-spacer />
      </v-toolbar>
    </template>
    <template>
      <div
        v-for="item in fields"
        :key="item.key"
        style="width: 44%"
      >
        <FormFieldElementInput
          :field="item"
          @change="e =>(password = e)"
        />
      </div>
    </template>
    <v-row>
      <v-col
        cols="4"
        sm="4"
      />
      <v-col
        cols="2"
        sm="2"
      >
        <v-btn
          class="cancel-button button"
          outlined
        >
          Cancel
        </v-btn>
        <v-btn
          class="save-button button"
        >
          Save
        </v-btn>
      </v-col>
    </v-row>
  </div>
</template>
<script>
import FormFieldElementInput from '@/components/FormField/FormFieldElementInput'
export default {
  components: {
    FormFieldElementInput
  },
  data () {
    return {
      fields: [
        {
          name: 'Current Password',
          type: 'password',
          placeholder: 'password',
          el: {
            value: ''
          }
        },
        {
          name: 'New Password',
          type: 'password',
          placeholder: 'password',
          el: {
            value: ''
          }
        },
        {
          name: 'Confirm New Password',
          type: 'password',
          placeholder: 'password',
          el: {
            value: ''
          }
        }
      ]
    }
  }
}
</script>
<style lang="scss">

h1 {
  color: rgba(map-get($colors , black-2), 1) !important;
}
  .button {
    margin-right: 1.0rem;
    letter-spacing: 0.07rem;
  }
  .delete-button {
    color: rgba(map-get($colors , red), 1) !important;
  }
  .secondary-button {
    color: rgba(map-get($colors, secondary-blue), 1) !important;
  }
  .save-button {
    color: rgba(map-get($colors , white), 1) !important;
    background-color: rgba(map-get($colors , secondary-blue), 1) !important;
  }
  .cancel-button {
    color: rgba(map-get($colors , secondary-blue), 1) !important;
    border: unset !important;
  }
</style>
