<template>
  <div :class="{'form-field-element-time': true, focused: focused}">
    <v-icon>
      mdi-clock-outline
    </v-icon>

    <div :class="{time__mask: true, focused: focused}">
      <span :class="{mask__label: true, focused: focused, filled: Boolean(time)}">{{ altLabel || field.name }}</span>
      <input
        v-model="time"
        class="mask__input"
        type="text"
        @focus="toggleFocused"
        @blur="change"
      >
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormFieldElementTime',

  props: {
    field: {
      type: Object,
      required: true
    },
    altLabel: {
      type: String,
      required: false,
      default: ''
    },
    isEditing: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    time: '',
    focused: false
  }),

  watch: {
    isEditing: function () {
      this.syncValue()
    }
  },

  methods: {
    toggleFocused () {
      this.focused = !this.focused
    },

    change () {
      this.toggleFocused()
      this.$emit('change', this.time)
    },

    syncValue () {
      if (typeof this.field.value === 'object') {
        this.time = this.field.value.time
        return
      }

      this.time = this.field.value
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-element-time {
  display: flex;
  align-items: flex-start;
  height: 6.6rem;
  padding-top: 1.6rem;

  .v-icon {
    color: map-get($colors , grey-9);
    margin-top: 0.4rem;
    margin-bottom: 0.4rem;
    margin-right: 0.9rem;
  }

  &.focused .v-icon {
    color: map-get($colors , blue);
  }
}

.time__mask {
  position: relative;
  height: 3.2rem;
  border-width: 0.1rem;
  width: 100%;
  box-shadow: inset 0 -0.1rem 0 0 map-get($colors , grey-10);
  transition: box-shadow 200ms ease-in-out;

  &.focused {
    border-width: 0.2rem;
    box-shadow: inset 0 -0.1rem 0 0 map-get($colors , blue);
  }
}

.mask__label {
  display: block;
  margin-top: 0.8rem;
  color: map-get($colors , black-2);
  text-transform: capitalize;
  transition: color 200ms ease-in-out, transform 200ms ease-in-out;

  &.filled {
    transform: translateY(-1.8rem) !important;
  }

  &.focused {
    color: map-get($colors , blue);
    transform: translateY(-1.8rem) !important;
  }
}

.mask__input {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  outline: none;
}
</style>
