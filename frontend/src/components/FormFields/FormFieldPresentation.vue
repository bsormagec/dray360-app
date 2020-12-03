<template>
  <div
    :id="`${cleanStrForId(references)}-formfield`"
    class="form-field-presentation"
  >
    <div
      v-if="!editMode"
      :class="{
        'form-field-highlight': true,
        hover: isHovering,
        edit: isEditing
      }"
      @mouseover="isMobile || isEditing ? () => {} : startHover({ path: references })"
      @mouseleave="isMobile || isEditing ? () => {} : stopHover({ path: references })"
      @click="startFieldEdit({ path: references })"
    >
      <div
        v-show="!isEditing && !onlyHover"
        class="form-field__group"
      >
        <span class="form-field__label">{{ label }}</span>
        <span
          v-if="isLoading"
          class="field__value"
        >
          <v-progress-circular
            :size="20"
            indeterminate
            color="primary"
          />
        </span>
        <span
          v-else
          class="field__value"
        >
          {{ value === null || value === '' ? '--' : value }}
        </span>
      </div>
      <div
        :class="{
          'highlight__edit': true,
          'only-hover': onlyHover,
          hover: isHovering,
          edit: isEditing
        }"
      >
        <slot v-if="isEditing || onlyHover" />
        <FormFieldHighlightBtns
          v-show="(isEditing || highlight.hover) && !onlyHover"
          :edit-mode="isEditing"
          @accept="handleAccept"
          @cancel="handleCancel"
        />
      </div>
    </div>
    <slot
      v-if="editMode"
    />
  </div>
</template>

<script>
import FormFieldHighlightBtns from './FormFieldHighlightBtns'

import isMobile from '@/mixins/is_mobile'
import { mapState, mapActions } from 'vuex'
import orderForm, { types } from '@/store/modules/order-form'
import get from 'lodash/get'
import { cleanStrForId } from '@/utils/clean_str_for_id.js'

export default {
  name: 'FormFieldPresentation',

  components: { FormFieldHighlightBtns },

  mixins: [isMobile],

  props: {
    editMode: { type: Boolean, required: true },
    references: { type: String, required: true },
    label: { type: String, required: true },
    value: { required: true, default: '' },
    onlyHover: { type: Boolean, required: false, default: false }
  },

  computed: {
    ...mapState(orderForm.moduleName, {
      allHighlights: state => state.highlights
    }),
    highlight () {
      if (this.references === null || this.references === undefined) {
        return null
      }

      return this.allHighlights[this.references]
    },
    isHovering () {
      return !this.isEditing && this.highlight.hover
    },
    isEditing () {
      return this.editMode || get(this.highlight, 'edit', false)
    },
    isLoading () {
      return this.allHighlights[this.references]?.loading || false
    }
  },

  methods: {
    ...mapActions(orderForm.moduleName, {
      startHover: types.startHover,
      stopHover: types.stopHover,
      startFieldEdit: types.startFieldEdit,
      stopFieldEdit: types.stopFieldEdit
    }),
    cleanStrForId,
    handleAccept () {
      this.stopFieldEdit({ path: this.references })
      this.$emit('accept')
    },
    handleCancel () {
      this.stopFieldEdit({ path: this.references })
      this.$emit('cancel')
    }
  }
}
</script>

<style lang="scss">
.form-field-presentation {
  .form-field-highlight {
    position: relative;
    display: flex;
    align-items: center;
    transition: all 200ms ease-in-out;
    cursor: pointer;

    &.hover {
      background-color: rgba($blue--lt, 0.4);
      padding-right: rem(12);
    }
  }

  .only-hover {
    width: 100%;
  }

  .highlight__edit.edit {
    width: 100%;
    display: flex;
    padding: rem(7) rem(5.5) rem(7) rem(10);
    background-color: $blue--lt;
    color: white;

    fieldset {
      border: none;
    }

    .v-input__slot {
      border-radius: rem(4) 0 rem(0) rem(4);
    }
  }
}
</style>
