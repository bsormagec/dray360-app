<template>
  <div
    class="field"
  >
    <div
      v-show="isSimple"
      class="field__group"
    >
      <span class="field__name">{{ field.name }}</span>

      <span
        class="field__value"
      >{{ field.value ? field.value : '--' }}</span>
    </div>

    <div
      v-show="isComplex"
    >
      <span class="field__name">{{ field.name }}</span>
      <div
        v-for="(value, key) in field.value"
        :key="key"
        class="field__children"
      >
        <span class="field__name">{{ key }}</span>

        <span
          class="field__value"
        >{{ value }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldPresentation',

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
    },

    isSimple () {
      return typeof this.field.value !== 'object'
    },

    isComplex () {
      return typeof this.field.value === 'object'
    }
  }
}
</script>

<style lang="scss" scoped>
.field__group {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.1rem;
}

.field__name, .field__children .field__name {
  font-size: 1.4rem !important;
  font-weight: bold;
  text-transform: capitalize;
}

.field__value, .field__children .field__value {
  font-size: 1.44rem !important;
  text-transform: capitalize;
}

.field__children {
  display: flex;
  justify-content: space-between;
  padding-left: 1rem;
}
</style>
