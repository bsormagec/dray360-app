<template>
  <div>
    <span class="field__name">{{ field.name }}</span>
    <div
      v-for="(value, key) in field.value"
      :key="key"
      class="field__children"
    >
      <span class="field__name">{{ key }}</span>
      <span class="field__value">{{ value }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldPresentationComplex',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
