<template>
  <div
    v-if="editMode"
    class="action-btns"
  >
    <div
      class="btn btn--accept"
      @click.stop="$emit('accept')"
    >
      <v-icon>mdi-check</v-icon>
    </div>
    <div
      class="btn btn--close"
      @click.stop="$emit('cancel')"
    >
      <v-icon>mdi-close</v-icon>
    </div>
  </div>
  <div
    v-else
    class="action-btns"
  >
    <div class="btn">
      <v-icon>mdi-pencil-outline</v-icon>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldHighlightBtns',

  props: {
    editMode: {
      type: Boolean,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.action-btns {
  display: flex;
  align-items: center;
  flex: 0 0 rem(46);

  .btn:last-child {
    border-radius: 0 rem(4) rem(4) 0;
  }
}

.btn {
  position: relative;
  height: rem(22);
  width: rem(22);
  margin-left: rem(1);
  color: var(--v-slate-gray-base);
  cursor: pointer;

  i {
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: rem(20);
    color: inherit;
    transform: translate(-50%, -50%);
  }

  .edit & {
    background-color: white;
  }

  &.btn--accept {
    color: map-get($colors, mainblue);
  }

  &.btn--close {
    color: map-get($colors, red);
  }
}
</style>
