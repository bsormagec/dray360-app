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
        :disabled="btn.disabled"
        :outlined="isOutlined(btn)"
        @click="btn.action(btn.value)"
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
      return value !== this.activePage
    },

    displayText (value) {
      return typeof value === 'number' ? value : value
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

.First, .Last {
  display: none;

  @media screen and (min-width: map-get($breakpoints, lg)) {
    display: block;
  }
}
</style>
