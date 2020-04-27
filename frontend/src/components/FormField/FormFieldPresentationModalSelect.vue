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
  font-size: 1.44rem !important;
  font-weight: bold;
  color: map-get($colors, blue);
  outline: unset;

  i {
    margin-left: 1.05rem;
  }
}

.link__popover {
  position: absolute;
  width: 24rem;
  padding: 2.4rem 1.9rem 1.9rem 3.3rem;
  background: map-get($colors, white);
  box-shadow: 0rem 0.2rem 0.4rem rgba(0, 0, 0, 0.1);
  border: 0.1rem solid map-get($colors, grey-2);
  border-radius: 0.4rem;
  top: 3.5rem;
  right: 0;
}

.popover__triangle {
  position: absolute;
  width: 1rem;
  height: 1rem;
  top: -0.6rem;
  right: 2rem;
  background: white;
  transform: rotate(45deg);
  border-left-color: map-get($colors, grey-2);
  border-left-width: 0.1rem;
  border-left-style: solid;
  border-top-color: map-get($colors, grey-2);
  border-top-width: 0.1rem;
  border-top-style: solid;
}

.popover__header {
  font-size: 2.1rem !important;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.6rem;
}

.popover__address {
  display: block;
  width: 10rem;
  font-size: 1.44rem !important;
  font-weight: normal;
  margin-bottom: 1.6rem;
}

.popover__info-item {
  span {
    font-size: 1.44rem !important;
  }
}

.link {
  font-weight: normal;
  color:  map-get($colors , blue);
  text-decoration: underline;
}
</style>
