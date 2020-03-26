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
      class="buttons__single"
    >
      <v-btn
        color="primary"
        :outlined="isOutlined(btn)"
        @click="btn.action(btn.value + 1)"
      >
        {{ displayText(btn.value) }}
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
      return value + 1 !== this.activePage
    },

    displayText (value) {
      return typeof value === 'number' ? value + 1 : value
    }
  }
}
</script>

<style lang="scss" scoped>
.buttons {
  display: flex;

  &.altStyle {
    margin: 0 1rem;

    .buttons__single {
      margin: unset;
      .v-btn {
        border-radius: unset;
      }
    }

    .buttons__single:first-child .v-btn {
      border-top-left-radius: 0.4rem;
      border-bottom-left-radius: 0.4rem;
    }

    .buttons__single:last-child .v-btn {
      border-top-right-radius: 0.4rem;
      border-bottom-right-radius: 0.4rem;
    }
  }
}

.buttons__single:not(:last-child) {
  margin-right: 1rem;
}
</style>
