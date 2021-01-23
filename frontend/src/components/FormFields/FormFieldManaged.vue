<template>
  <div class="form-field">
    <div class="form-field__group">
      <div class="form-field__label">
        {{ label }}
      </div>
      <div class="managed-by-template">
        Managed By Template
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'FormFieldInput',

  props: {
    references: { type: String, default: null },
    label: { required: true, type: String }
  }
}
</script>

<style lang="scss" scoped>
.managed-by-template {
  font-weight: 500;
  font-size: rem(10);
  line-height: (15 / 10);
  letter-spacing: rem(1.5);
  text-transform: uppercase;
  color: var(--v-slate-gray-base);
}
</style>
