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
  width: 3rem;
  height: 3rem;
}

.loader {
  margin: 6rem auto;
  font-size: 1rem;
  position: relative;
  text-indent: -9999rem;
  border-top: 0.5rem solid rgba(var(--v-primary-base-rgb), 0.2);
  border-right: 0.5rem solid rgba(var(--v-primary-base-rgb), 0.2);
  border-bottom: 0.5rem solid rgba(var(--v-primary-base-rgb), 0.2);
  border-left: 0.5rem solid var(--v-primary-base);
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
