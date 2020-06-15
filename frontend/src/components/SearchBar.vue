<template>
  <div class="search-bar">
    <input placeholder="Search">
    <v-icon
      slot="append"
    >
      mdi-magnify
    </v-icon>
  </div>
</template>

<script>
export default {
  name: 'SearchBar'
}
</script>

<style lang="scss" scoped>
.search-bar {
  position: relative;
  height: 2.4rem;
  min-width: 9rem;

  input {
    width: 100%;
    height: 100%;
    outline: none;
    padding: 0 2.5rem 0 1rem;
    border-radius: 0.2rem;
    border: 0.1rem solid map-get($colors , grey-10);
    transition: border-color 200ms ease-in-out;

    &:focus {
      border-color: map-get($colors , blue);
    }
  }

  i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 0.5rem;
    font-size: 1.8rem;
  }
}
</style>
