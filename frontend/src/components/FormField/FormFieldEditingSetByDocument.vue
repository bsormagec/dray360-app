<template>
  <div :class="`form-field-editing-by-document ${field.editing_set_by_document}`">
    <textarea rows="1" />

    <div class="action-btns">
      <div
        v-show="isEditing"
        class="btns__close"
      >
        <v-icon>mdi-close</v-icon>
      </div>

      <div class="btns__accept">
        <v-icon>{{ isEditing ? 'mdi-check' : 'mdi-pencil' }}</v-icon>
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
    modes
  }),

  computed: {
    isEditing () {
      return this.field.editing_set_by_document === this.modes.edit
    }
  }
}
</script>

<style lang="scss" scoped>
.form-field-editing-by-document {
  position: relative;
  display: flex;
  width: 60%;
  border: 0.1rem solid map-get($colors, blue);
  border-radius: 0.2rem;
  padding-bottom: 2.5rem;

  &.hover {
    background: rgba(map-get($colors , blue), 0.15);
  }
}

textarea {
  width: 100%;
  height: 100%;
  min-height: 3rem;
  padding: 0.7rem;
  outline: unset;
  overflow: auto;
  font-size: 1.44rem !important;
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
