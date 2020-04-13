<template>
  <div class="document" />
</template>

<script>
export default {
  name: 'DetailsDocument'
}
</script>

<style lang="scss" scoped>
.document {
  height: 100%;
  display: flex;
  flex-grow: 1;
  background: map-get($colors , grey-8);
}
</style>
