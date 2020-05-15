<template>
  <div
    v-if="typeof field.value === 'object'"
    class="field__complex"
  >
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

  <div
    v-else
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>
    <span
      class="field__value"
    >{{ field.value ? field.value : '--' }}</span>
  </div>
</template>

<script>
export default {
  name: 'FormFieldHighlightView',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.field__complex {
  width: 100%;
}
</style>
