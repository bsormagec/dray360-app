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
      class="loading-animation"
    >
      <!--       <img
        :class="`loading-animation ${isMobile && 'mobile'}`"
        src="../assets/images/loading-animation.gif"
      >> -->
      <img
        src="../assets/images/loading-animation.gif"
      >
    </div>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'

export default {
  name: 'ContentLoading',

  mixins: [isMobile],

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

.loading-animation {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background-size: cover;
  text-align: center;
}
</style>
