<template>
  <div class="form-field-element-input">
    <v-text-field
      :label="field.name"
      :placeholder="field.name"
      outlined
      dense
      @change="test"
    />
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementInput',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  methods: {
    test (e) {
      console.log('test', e)
    }
  }
}
</script>
