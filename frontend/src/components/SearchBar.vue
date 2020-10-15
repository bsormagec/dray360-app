<template>
  <div class="search-bar">
    <input
      placeholder="Search"
      @input="change"
    >
    <v-icon
      slot="append"
    >
      mdi-magnify
    </v-icon>
  </div>
</template>

<script>
export default {
  name: 'SearchBar',

  data: () => ({
    timeout: undefined
  }),

  methods: {
    change (e) {
      if (this.timeout) {
        clearInterval(this.timeout)
        this.timeout = undefined
      }

      this.timeout = setTimeout(() => this.$emit('change', e.target.value), 300)
    }
  }
}
</script>

<style lang="scss" scoped>
.search-bar {
  position: relative;
  height: rem(24);
  min-width: rem(90);

  input {
    width: 100%;
    height: 100%;
    outline: none;
    padding: 0 rem(25) 0 rem(10);
    border-radius: rem(2);
    border: rem(1) solid map-get($colors , grey-10);
    transition: border-color 200ms ease-in-out;
    font-size: rem(12);

    &:focus {
      border-color: var(--v-primary-base);
    }
  }

  i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: rem(5);
    font-size: rem(18);
  }
}
</style>
