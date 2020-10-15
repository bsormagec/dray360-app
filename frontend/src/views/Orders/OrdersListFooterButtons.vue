<template>
  <div
    :class="{
      buttons: true,
      altStyle: altStyle
    }"
  >
    <div
      v-for="btn in buttonsList"
      :key="btn.value"
      :class="`buttons__single ${btn.value}`"
    >
      <v-btn
        color="primary"
        class="footer-btn"
        :disabled="btn.disabled"
        :outlined="isOutlined(btn)"
        @click="btn.action(btn.value)"
      >
        {{ btn.value }}
      </v-btn>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OrdersListFooterButtons',

  props: {
    altStyle: {
      type: Boolean
    },
    activePage: {
      type: Number,
      required: true
    },
    buttonsList: {
      type: Array,
      required: true
    }
  },

  methods: {
    isOutlined ({ value }) {
      return value !== this.activePage
    }
  }
}
</script>

<style lang="scss" scoped>
.buttons {
  display: flex;

  &.altStyle {
    margin: 0 rem(10);

    .buttons__single {
      margin: unset;
      .v-btn {
        border-radius: unset;
      }
    }

    .buttons__single:first-child .v-btn {
      border-top-left-radius: rem(4);
      border-bottom-left-radius: rem(4);
    }

    .buttons__single:last-child .v-btn {
      border-top-right-radius: rem(4);
      border-bottom-right-radius: rem(4);
    }
  }
}

.footer-btn::v-deep span.v-btn__content {
  font-size: rem(12);
}

.buttons__single:not(:last-child) {
  margin-right: rem(10);
}
</style>
