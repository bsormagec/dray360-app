<template>
  <div class="form-field-element-switch">
    <v-switch
      :label="field.name"
      @change="e => $emit('change', e)"
    />
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementSwitch',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-switch {
  height: 6.6rem;
}
</style>
