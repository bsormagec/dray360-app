<template>
  <div
    class="field"
  >
    <span class="field__name">{{ field.name }}</span>

    <span
      v-if="isText"
      class="field__text"
    >
      {{ field.value }}
    </span>

    <a
      v-if="isLink"
      class="field__link"
      :href="field.value.href"
    >
      {{ field.value.text }}
      <v-icon
        color="primary"
        :style="{ marginLeft: '1.5rem' }"
      >{{ field.value.icon }}</v-icon>
    </a>

    <span
      v-else
      class="field__value"
    >{{ field.value }}</span>
  </div>
</template>

<script>
export default {
  name: 'DetailsFormFieldPresentation',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  computed: {
    isText () {
      return this.field.type === 'text'
    },

    isLink () {
      if (!this.field.value) return false
      return this.field.value.type === 'link'
    }
  }
}
</script>

<style lang="scss" scoped>
.field {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.1rem;
}

.field__name {
  font-size: 1.4rem !important;
  font-weight: bold;
  text-transform: capitalize;
}

.field__link {
  text-decoration: none;
  font-size: 1.44rem;
  font-weight: bold;
  display: flex;
  align-items: center;
}

.field__value {
  font-size: 1.44rem !important;
  text-transform: capitalize;
}
</style>
