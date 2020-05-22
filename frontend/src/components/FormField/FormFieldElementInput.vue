<template>
  <div class="form-field-element-input">
    <v-text-field
      :label="field.name"
      :placeholder="field.el.placeholder"
      :type="field.type"
      outlined
      dense
      :value="field.value"
      @change="e => change(e)"
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

  mounted () {
    if (this.field.value) {
      this.change(this.field.value)
    }
  },

  methods: {
    change (e) {
      this.$emit('change', e)
    }
  }
}
</script>
