<template>
  <div class="content-loading">
    <transition name="fade">
      <div
        v-if="loaded"
        class="loaded"
      >
        <slot />
      </div>
    </transition>

    <div
      v-if="!loaded"
      class="loader"
    >
      ...loading
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContentLoading',

  props: {
    loaded: {
      type: Boolean,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.content-loading {
  width: 100%;
  height: 100%;
}

.loaded {
  display: flex;
}

.loader,
.loader:after {
  border-radius: 50%;
  width: rem(30);
  height: rem(30);
}

.loader {
  margin: rem(60) auto;
  font-size: rem(10);
  position: relative;
  text-indent: -9999rem; // ???
  border-top: rem(5) solid rgba(var(--v-primary-base-rgb), 0.2);
  border-right: rem(5) solid rgba(var(--v-primary-base-rgb), 0.2);
  border-bottom: rem(5) solid rgba(var(--v-primary-base-rgb), 0.2);
  border-left: rem(5) solid var(--v-primary-base);
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load8 1.1s infinite linear;
  animation: load8 1.1s infinite linear;
}

@-webkit-keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>
