<template>
  <div class="action-btns">
    <div
      v-if="editMode"
      class="btns__edit"
    >
      <div
        class="btns__close"
        @click.stop="$emit('cancel')"
      >
        <v-icon>mdi-close</v-icon>
      </div>

      <div
        class="btns__accept"
        @click.stop="$emit('accept')"
      >
        <v-icon>mdi-check</v-icon>
      </div>
    </div>

    <div
      v-if="!editMode"
      class="btns__accept"
    >
      <v-icon>mdi-pencil</v-icon>
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
  position: absolute;
  bottom: 0;
  right: 0;
  display: flex;
  background: map-get($colors , white);
  border-top: rem(1) solid var(--v-primary-base);
  border-left: rem(1) solid var(--v-primary-base);
  border-top-left-radius: rem(2);

  &:not(:last-child) {
    border-right: rem(1) solid var(--v-primary-base);
  }
}

.btns__edit {
  display: flex;
}

i {
  font-size: rem(16) !important;
}

.btns__close, .btns__accept {
  margin-top: rem(-1);
  cursor: pointer;
}

.btns__close i {
  color: var(--v-primary-base);
}

.btns__accept {
  background: var(--v-primary-base);

  i {
    color: map-get($colors , white)
  }
}
</style>
