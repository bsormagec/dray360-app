<template>
  <div>
    <template>
      <v-toolbar
        flat
        color="white"
      >
        <v-toolbar-title><h1>Edit Profile</h1></v-toolbar-title>

        <v-spacer />
      </v-toolbar>
    </template>
    <template>
      <div
        v-for="item in fields"
        :key="item.key"
        style="width: 44%"
      >
        <FormField
          :field="item"
          :is-editing="true"
          :readonly="item.readonly"
          :callbacks="{}"
        />
      </div>
    </template>

    <v-row>
      <v-col
        cols="4"
        sm="4"
      >
        <v-btn
          class="cancel-button button"
          outlined
          @click="$router.push({path: '/user/dashboard/change-password/'})"
        >
          Change Password
        </v-btn>
      </v-col>
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
import FormField from '@/components/FormField/FormField'
export default {
  components: {
    FormField
  },
  data () {
    return {
      fields: [
        {
          name: 'First Name',
          readonly: false,
          el: {
            type: 'input'
          }
        },
        {
          name: 'Last Name',
          readonly: false,
          el: {
            type: 'input'
          }
        },
        {
          name: 'Email',
          readonly: false,
          el: {
            type: 'input'
          }
        },
        {
          name: 'Position',
          readonly: false,
          el: {
            type: 'input'
          }
        },
        {
          name: 'Org',
          readonly: false,
          el: {
            type: 'input'
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
    margin-right: 1rem;
    letter-spacing: 0.075rem;
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
