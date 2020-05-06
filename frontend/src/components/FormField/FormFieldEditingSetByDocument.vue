<template>
  <div :class="`form-field-editing-by-document ${field.editing_set_by_document}`">
    <input
      v-model="value"
      :class="{ pointer: field.editing_set_by_document === modes.hover }"
    >

    <div class="action-btns">
      <div
        v-show="isEditing"
        class="btns__close"
        @click.stop="close"
      >
        <v-icon>mdi-close</v-icon>
      </div>

      <div
        v-show="isEditing"
        class="btns__accept"
        @click.stop="acceptChanges"
      >
        <v-icon>mdi-check</v-icon>
      </div>

      <div
        v-show="!isEditing"
        class="btns__accept"
      >
        <v-icon>mdi-pencil</v-icon>
      </div>
    </div>
  </div>
</template>

<script>
import { modes } from '@/views/Details/inner_types'

export default {
  name: 'FormFieldEditingSetByDocument',

  props: {
    field: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    modes,
    value: undefined
  }),

  computed: {
    isEditing () {
      return this.field.editing_set_by_document === this.modes.edit
    }
  },

  mounted () {
    setTimeout(() => {
      this.value = this.field.value
    }, 0)
  },

  methods: {
    close () {
      this.$emit('close')
    },

    acceptChanges () {
      this.$emit('change', this.value)
      this.close()
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-editing-by-document {
  cursor: pointer;
  position: relative;
  display: flex;
  width: 60%;
  height: 3rem;
  padding: 0.5rem 3rem 0.5rem 0.5rem;
  border: 0.1rem solid map-get($colors, blue);
  border-radius: 0.2rem;
  transition: opacity 200ms ease-in-out;

  &.hover {
    background: rgba(map-get($colors , blue), 0.15);
  }
}

input {
  width: 100%;
  height: 100%;
  height: 2.1rem;
  outline: unset;
  overflow: auto;
  font-size: 1.44rem !important;

  &.pointer {
    cursor: pointer;
  }
}

.action-btns {
  position: absolute;
  bottom: 0;
  right: 0;
  display: flex;
  border-top: 0.1rem solid map-get($colors, blue);
  border-left: 0.1rem solid map-get($colors, blue);
  border-top-left-radius: 0.2rem;

  &:not(:last-child) {
    border-right: 0.1rem solid map-get($colors, blue);
  }
}

i {
  font-size: 1.6rem !important;
}

.btns__close, .btns__accept {
  margin-top: -0.1rem;
  cursor: pointer;
}

.btns__close i {
  color: map-get($colors , blue);
}

.btns__accept {
  background: map-get($colors , blue);

  i {
    color: map-get($colors , white)
  }
}
</style>
