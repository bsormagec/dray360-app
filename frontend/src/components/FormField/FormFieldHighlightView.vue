<template>
  <div
    v-if="typeof field.value === 'object'"
    class="field__complex"
  >
    <span class="field__name">{{ field.presentationName || field.name }}</span>
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
    <span class="field__name">{{ field.presentationName || field.name }}</span>
    <span
      class="field__value"
    >{{ valueByType }}</span>
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
  },

  computed: {
    valueByType () {
      if (this.field.el.type === 'switch') {
        return this.field.value === true ? 'yes' : 'no'
      }

      return this.field.value ? this.field.value : '--'
    }
  }
}
</script>

<style lang="scss" scoped>
.field__complex {
  width: 100%;
}
</style>
