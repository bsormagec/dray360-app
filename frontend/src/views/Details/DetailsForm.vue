<template>
  <div class="form">
    <Fragment
      v-for="(section, i) in form"
      :key="i"
    >
      <h1 class="form__title">
        {{ section.title }}
      </h1>

      <div
        v-for="field in section.rootFields"
        :key="field.name"
        class="form__field"
      >
        <span class="field__name">{{ field.name }}</span>
        <span class="field__value">{{ field.value }}</span>
      </div>
    </Fragment>
  </div>
</template>

<script>
import { Fragment } from 'vue-fragment'

export default {
  name: 'DetailsForm',

  components: {
    Fragment
  },

  props: {
    form: {
      type: Array,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
.form {
  width: 45%;
  padding: 3.6rem 6.5rem;
}

.form__title {
  text-transform: uppercase;
  font-size: 1.7rem;
  line-height: 3rem;
  background: map-get($colors, grey-6);
  padding: 0 0.65rem;
  margin-bottom: 2rem;
  color: map-get($colors, grey-7);
}

.form__field {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.1rem;
}

.field__name {
  font-size: 1.4rem !important;
  font-weight: bold;
  text-transform: capitalize;
}

.field__value {
  font-size: 1.44rem !important;
  text-transform: capitalize;
}
</style>
