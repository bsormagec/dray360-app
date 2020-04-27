<template>
  <div
    class="field__group"
  >
    <span class="field__name">{{ field.name }}</span>
    <span class="field__value">{{ field.value ? field.value : '--' }}</span>
  </div>
</template>

<script>
export default {
  name: 'FormFieldPresentationSimple',

  props: {
    field: {
      type: Object,
      required: true
    }
  }
}
</script>
