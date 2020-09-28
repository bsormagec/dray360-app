<template>
  <v-menu
    v-bind="menuAttributes"
    content-class="split-btn__menu"
  >
    <template v-slot:activator="{ on, attrs }">
      <div
        class="split-btn"
        :class="{'split-btn--floated': floated}"
      >
        <v-btn
          rounded
          outlined
          v-bind="buttonAttributes"
          color="primary"
          class="split-btn__primary"
          title="Go to Order Details"
          :href="mainAction.path"
        >
          {{ mainAction.title }}
        </v-btn>
        <v-btn
          v-bind="[attrs, buttonAttributes]"
          color="primary"
          rounded
          class="split-btn__actions-btn"
          outlined
          v-on="on"
        >
          <v-icon>
            mdi-chevron-down
          </v-icon>
        </v-btn>
      </div>
    </template>
    <v-list>
      <v-list-item
        v-for="(option, index) in options"
        :key="index"
        :disabled="needPermission(option.hasPermission)"
        @click="option.action"
      >
        <v-list-item-content>
          <v-list-item-title v-text="option.title" />
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'OutlinedButtonGroup',
  props: {
    mainAction: {
      type: Object,
      required: true
    },
    options: {
      type: Array,
      required: true
    },
    floated: {
      type: Boolean
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    menuAttributes () {
      return {
        'offset-y': !this.floated,
        'offset-x': this.floated,
        bottom: !this.floated,
        left: !this.floated,
        'nudge-bottom': !this.floated ? 4 : 0,
        'nudge-right': this.floated ? 4 : 0,
        'nudge-with': 'auto'
      }
    },
    buttonAttributes () {
      return {
        small: !this.floated,
        elevation: this.floated ? 3 : 0,
        disabled: this.disabled
      }
    }
  },
  methods: {
    needPermission (permission) {
      if (permission !== undefined) {
        return !permission
      }
      return false
    }
  }
}
</script>

<style scoped lang="scss">
  .split-btn {
    display: inline-block;
    text-transform: none;
    font-size: 13px;
    &--floated {
      position: fixed;
      left: auto;
      bottom: 23px;
      z-index: 1;
      transform: translateX(-3px);
    }
  }

  .split-btn {
    &__primary {
      border-radius: 3px 0 0 3px;
      text-transform: inherit;
      z-index: 1;
    }
    &__actions-btn,
    &__primary {
      background-color: #FFFFFF;
    }
  }

  .split-btn {
    &__actions-btn.v-btn {
      min-width: 24px;
      margin-left: -1px;
      padding: 0;
      border-radius: 0 3px 3px 0;
      &[aria-expanded=true] {
        background-color: rgba(var(--v-primary-base-rgb, 1));
        .v-btn__content i {
          color: #FFFFFF;
        }
      }
    }
  }

  .split-btn__menu {
    box-shadow: none;
    border: 1px solid #D2D7D7;
    border-radius: 3px;
    .v-list-item--disabled .v-list-item__title {
      color: rgba(0, 0, 0, 0.38) !important;
    }
  }
</style>
