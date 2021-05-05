<template>
  <div
    :id="sectionId"
    class="form__section-title--managed d-flex align-center justify-center mb-2"
  >
    <h3>
      {{ sectionName }} managed by template
    </h3>
  </div>
</template>

<script>
export default {
  name: 'ManagedByTemplateSection',
  props: {
    sectionName: { type: String, required: true },
    sectionId: { required: false, default: null },
  }
}
</script>

<style lang="scss" scoped>
.form__section-title--managed {
  padding: rem(4) rem(10) rem(3);
  background-color: var(--v-slate-gray-base);
  h3 {
    text-transform: uppercase;
    font-size: rem(10);
    font-weight: 500;
    line-height: (15 / 10);
    letter-spacing: rem(1.5);
    color: map-get($colors, white);
  }
}
</style>
