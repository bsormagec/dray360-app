<template>
  <div class="field-modalselect">
    <div class="field__group">
      <span class="field__name">{{ field.name }}</span>

      <span
        v-if="field.value"
        class="field__link"
        tabindex="0"
        @click="togglePopupOpen"
        @focusout="togglePopupOpen"
      >
        {{ field.value['company name'] }}
        <v-icon color="primary">mdi-account-box</v-icon>

        <div
          v-show="isPopupOpen"
          class="link__popover"
        >
          <div class="popover__triangle" />

          <span class="popover__header">
            {{ field.value['company name'] }}
            <v-icon color="primary">mdi-account-box</v-icon>
          </span>

          <span class="popover__address">
            {{ field.value.address }}
          </span>

          <div class="popover__info-item">
            <span>Phone: </span>
            <span class="link">({{ field.value.ext }})-{{ field.value.phone }}</span>
          </div>

          <div class="popover__info-item">
            <span>Email: </span>
            <span class="link">{{ field.value.email }}</span>
          </div>

          <div class="popover__info-item">
            <span>Manager: </span>
            <span :style="{ fontWeight: 'normal' }">{{ field.value['contact name'] }}</span>
          </div>
        </div>
      </span>

      <span v-else>--</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldPresentationModalSelect',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    isPopupOpen: false
  }),

  methods: {
    togglePopupOpen () {
      this.isPopupOpen = !this.isPopupOpen
    }
  }
}
</script>

<style lang="scss" scoped>
.field__link {
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  font-size: rem(14.4) !important;
  font-weight: bold;
  color: var(--v-primary-base);
  outline: unset;

  i {
    margin-left: rem(10.5);
  }
}

.link__popover {
  position: absolute;
  width: rem(240);
  padding: rem(24) rem(19) rem(19) rem(33);
  background: map-get($colors, white);
  box-shadow: 0 rem(2) rem(4) rgba(0, 0, 0, 0.1);
  border: rem(1) solid map-get($colors, grey-2);
  border-radius: rem(4);
  top: rem(35);
  right: 0;
  z-index: 2;
}

.popover__triangle {
  position: absolute;
  width: rem(10);
  height: rem(10);
  top: rem(-6);
  right: rem(20);
  background: white;
  transform: rotate(45deg);
  border-left-color: map-get($colors, grey-2);
  border-left-width: rem(1);
  border-left-style: solid;
  border-top-color: map-get($colors, grey-2);
  border-top-width: rem(1);
  border-top-style: solid;
}

.popover__header {
  font-size: rem(21) !important;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: rem(16);
}

.popover__address {
  display: block;
  width: rem(100);
  font-size: rem(14.4) !important;
  font-weight: normal;
  margin-bottom: rem(16);
}

.popover__info-item {
  span {
    font-size: rem(14.4) !important;
  }
}

.link {
  font-weight: normal;
  color: var(--v-primary-base);
  text-decoration: underline;
}
</style>
